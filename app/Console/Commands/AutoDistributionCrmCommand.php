<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\ChangingConfig;
use App\Models\Config;
use App\Models\CrmBoAdmin;
use App\Models\CrmOrder;
use App\Models\CrmResource;
use App\Models\User;
use App\Services\CrmReportService;
use App\Services\CrmService;
use Illuminate\Console\Command;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Log;

class AutoDistributionCrmCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:auto-distribution-crm';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '自动分配 CRM data 给 BO 管理员';
    protected $crmService;
    protected $sendOrderRelation = [];

    /**
     * Create a new command instance.
     * @param CrmService $service
     * @return void
     */
    public function __construct(CrmService $service)
    {
        $this->crmService = $service;
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        # 检测自动派单设置
        $setting = ['crm_welcome', 'crm_non_deposit', 'crm_retention', 'crm_daily_retention', 'crm_resource'];
        $canAuto = ChangingConfig::query()->whereIn('code', $setting)->where('value', true)->exists();
        if (!$canAuto) {
            return null;
        }

        # 获取每人每小时派单量
        $assignAmount = Config::findValue('assign_crm_order_per_admin_hourly', 30);

        # 获取电销人员
        $boAdmins = $this->getCrmBoAdmins();
        Log::channel('crm_log')->info('定时任务启动，检测自动排单前在岗BO User，检测结果为：' . json_encode($boAdmins));

        if ($boAdmins->isEmpty()) {
            Log::channel('crm_log')->info('任务应该没有被触动');
            return null;
        }
        Log::channel('crm_log')->info('任务被触动了，请确认在职BO user');

        # 初始化电销人人员派单的单据ID，避免后期派单逻辑导致出错
        $boAdmins->each(function ($item) {
            $this->sendOrderRelation[$item->admin_name] = [];
        });

        # 找到待分配订单，顺便检测用户是否为激活状态
        $boAdminAmount = $boAdmins->count();
        $crmOrders     = $this->getCrmOrdersForSend($boAdminAmount * $assignAmount);

        # 派单，优先派发 welcome 订单，平均派发
        $this->sendCrmOrderWelcome($crmOrders, $boAdmins);

        # 匹配以前有服务记录的客服与客户，并分配新会员
        $this->sendCrmOrder($crmOrders, $boAdmins, $assignAmount);

        # 分配外部客户资源
        $this->autoTaggingCrmResource($boAdmins, $assignAmount);
        return true;
    }

    /**
     * 根据用户关系与待分派用户ID，派发crm orders给指定的 admin
     * @param array $crmOrderIds
     * @param Collection $boAdminsCollection
     * @param array $sendRelation
     * @return bool
     */
    private function autoTaggingCrmOrders(array $crmOrderIds, $boAdminsCollection, $sendRelation = [])
    {
        $adminCount = $boAdminsCollection->count();

        $crmOrderIdsSlice = array_slice_ave($crmOrderIds, $adminCount);

        $batchData = [];
        $relation  = $this->sendOrderRelation;

        foreach ($boAdminsCollection as $key => $boAdmin) {

            if (empty($sendRelation)) {
                $crmOrderIdsForBoAdmin = $crmOrderIdsSlice[$key];
            } else {
                $crmOrderIdsForBoAdmin = $sendRelation[$boAdmin->admin_name];
            }

            $tagAdmin                       = Admin::query()->find($boAdmin->tag_admin_id);
            $relation[$boAdmin->admin_name] = Arr::flatten([$relation[$boAdmin->admin_name], $crmOrderIdsForBoAdmin]); // count 内容可能为 null

            foreach ($crmOrderIdsForBoAdmin as $crmOrderId) {

                $batchData[] = [
                    'id'                        => $crmOrderId,
                    'admin_id'                  => $boAdmin->admin_id,
                    'admin_name'                => $boAdmin->admin_name,
                    'tag_admin_id'              => $tagAdmin->id,
                    'tag_admin_name'            => $tagAdmin->name,
                    'tag_at'                    => now(),
                    'last_save_case_admin_id'   => $tagAdmin->id,
                    'last_save_case_admin_name' => $tagAdmin->name,
                    'last_save_case_at'         => now(),
                    'is_auto'                   => true,
                ];
            }
        }
        if (empty($batchData)){
            return null;
        }
        $this->sendOrderRelation = $relation;
        Log::channel('crm_log')->info('crm auto send:' . implode(',', $crmOrderIds));
        return count($crmOrderIds) == CrmOrder::updateBatch($batchData) ? true : false;
    }

    /**
     * 自动给派单不足的 admin 增加资源呼叫派单
     * @param Collection $boAdmins
     * @param int $assignAmount
     * @return bool
     */
    private function autoTaggingCrmResource(Collection $boAdmins, int $assignAmount)
    {
        $canAuto = ChangingConfig::query()->where('code', 'crm_resource')->where('value', true)->exists();
        if (!$canAuto) {
            return null;
        }

        $adminCount           = $boAdmins->count();
        $crmResourceToCall    = CrmResource::query()->whereNull('admin_id')->latest()->limit($assignAmount * $assignAmount)->get();
        $crmResourceToCallIds = $crmResourceToCall->pluck('id')->toArray();
        $idAve                = array_slice_ave($crmResourceToCallIds, $adminCount);

        $batchData = [];
        $mark      = [];

        foreach ($boAdmins as $key => $boAdmin) {

            $needle   = $assignAmount - count($this->sendOrderRelation[$boAdmin->admin_name]);
            $tagAdmin = Admin::query()->find($boAdmin->tag_admin_id);

            # 这里多次比较切割的目的在于，让派单顺序跟着表格数据的顺序派发，避免随机派发时出现靠前的数据迟迟不能派发
            $idPool                 = array_diff($crmResourceToCallIds, $mark);
            $idsForAdmin            = array_slice($idPool, 0, count($idAve[$key]));
            $crmResourceIdsForAdmin = array_slice($idsForAdmin, 0, $needle);

            foreach ($crmResourceIdsForAdmin as $id) {
                $mark[]      = $id;
                $batchData[] = [
                    'id'                        => $id,
                    'admin_id'                  => $boAdmin->admin_id,
                    'admin_name'                => $boAdmin->admin_name,
                    'tag_admin_id'              => $tagAdmin->id,
                    'tag_admin_name'            => $tagAdmin->name,
                    'tag_at'                    => now(),
                    'last_save_case_admin_id'   => $tagAdmin->id,
                    'last_save_case_admin_name' => $tagAdmin->name,
                    'last_save_case_at'         => now(),
                    'is_auto'                   => true,
                ];
            }
        }

        Log::channel('crm_log')->info('crm resource auto send:' . implode(',', $mark));

        if (empty($batchData)){
            return null;
        }
        $result = count($mark) == CrmResource::updateBatch($batchData) ? true : false;

        $crmReportService = new CrmReportService();
        $crmReportService->modifyOrders();

        return $result;
    }

    /**
     * 找到待配发的
     * @param int $limit
     * @return Collection $crmOrders
     */
    private function getCrmOrdersForSend(int $limit)
    {
        $type = $this->getAllowAutoSendCrmOrderType();

        return CrmOrder::query()
            ->whereNull('call_status')
            ->where('status', CrmOrder::STATUS_OPEN)
            ->whereIn('type', $type)
            ->orderBy('type', 'asc')
            ->whereHas('user', function ($query) {
                return $query->where('status', User::STATUS_ACTIVE);
            })
            ->whereNull('admin_id')
            ->limit($limit)
            ->get();
    }


    /**
     * 获取能够自动派发的 order 类型，在此过滤没有设置可以自动派发的订单
     * @return array
     */
    private function getAllowAutoSendCrmOrderType()
    {
        $type           = [];
        $setting        = ['crm_welcome', 'crm_non_deposit', 'crm_retention', 'crm_daily_retention', 'crm_resource'];
        $changingConfig = ChangingConfig::query()->whereIn('code', $setting)->pluck('value', 'code')->toArray();

        $changingConfig['crm_welcome'] == true ? $type[] = CrmOrder::TYPE_WELCOME : null;
        $changingConfig['crm_non_deposit'] == true ? $type[] = CrmOrder::TYPE_NON_DEPOSIT : null;
        $changingConfig['crm_retention'] == true ? $type[] = CrmOrder::TYPE_RETENTION : null;
        $changingConfig['crm_daily_retention'] == true ? $type[] = CrmOrder::TYPE_DAILY_RETENTION : null;

        return $type;
    }

    /**
     * 获取在职当班的 crm bo admin
     * @return Collection
     */
    private function getCrmBoAdmins()
    {
        $stopBefore = Config::findValue('assign_crm_order_stop_ahead', 60);
        $startDelay = Config::findValue('assign_crm_order_start_delay', 60);

        $start = now()->subMinutes($startDelay)->toTimeString();
        $stop  = now()->addMinutes($stopBefore)->toTimeString();

        $admins = CrmBoAdmin::query()->where('start_worked_at', '<=', $start)
            ->where('end_worked_at', '>=', $stop)
            ->where('status', true)
            ->where('on_duty', true)
            ->get();

        $adminsJson = $admins->toJson();
        Log::channel('crm_log')->info('Still Working BO User:' . $adminsJson);
        return $admins;
    }


    /**
     * welcome 订单当天要全部派发完，不限制数量
     * @param Collection $crmOrders
     * @param Collection $boAdmins
     * @return boolean
     */
    private function sendCrmOrderWelcome(Collection $crmOrders, Collection $boAdmins)
    {
        $crmOrdersWelcomeIds       = $crmOrders->where('type', CrmOrder::TYPE_WELCOME)->pluck('id')->toArray();
        $crmOrderWelcomeSendResult = $this->autoTaggingCrmOrders($crmOrdersWelcomeIds, $boAdmins);

        if (!$crmOrderWelcomeSendResult) {
            Log::channel('crm_log')->error('auto send welcome order fail!');
        }
        return true;
    }


    /**
     * 除 welcome 外其他订单需要定量派发，不能超额派发
     * @param Collection $crmOrders
     * @param Collection $boAdmins
     * @param int $assignAmount
     * @return boolean
     */
    private function sendCrmOrder(Collection $crmOrders, Collection $boAdmins, int $assignAmount)
    {
        $orderMatch          = [];
        $crmOrderIdMark      = [];
        $rmOrderReadySendIds = $crmOrders->where('type', '<>', CrmOrder::TYPE_WELCOME)->pluck('id')->toArray();
        $userIds             = $crmOrders->pluck('user_id')->toArray();

        foreach ($boAdmins as $boAdmin) {
            // 这里有个去重机制，user_id，该段代码暂时不能外移
            $crmOrderUserIds = CrmOrder::query()->whereNotNull('call_status')
                ->where('type', '<>', CrmOrder::TYPE_WELCOME)
                ->where('admin_id', $boAdmin->admin_id)
                ->whereIn('user_id', $userIds)
                ->groupBy('user_id')
                ->pluck('user_id')
                ->toArray();

            $crmOrderIds = $crmOrders->whereIn('user_id', $crmOrderUserIds)->pluck('id')->toArray();
            # 计算出管理员尚未达到小时分配配额的缺口数量
            $needle = $assignAmount - count($this->sendOrderRelation[$boAdmin->admin_name]);

            # 根据缺口最大值取得存在关联关系用户ID，并放入待分配关系名单
            $orderMatch[$boAdmin->admin_name] = array_slice($crmOrderIds, 0, $needle);

            if (count($orderMatch[$boAdmin->admin_name]) < $needle) {

                # 去重，去除已经分配给该管理员的用户ID
                $crmOrderIdDiff = array_diff($rmOrderReadySendIds, $orderMatch[$boAdmin->admin_name]);

                # 去重，去除已经分配的用户ID
                $crmOrderIdPool = array_diff($crmOrderIdDiff, $crmOrderIdMark);

                # 更新管理员与用户的分配关系
                $orderMatch[$boAdmin->admin_name] = Arr::flatten([$orderMatch[$boAdmin->admin_name], array_slice($crmOrderIdPool, 0, $needle)]);

                # 更新已分配给管理员的用户ID集合
                $crmOrderIdMark = Arr::flatten([$crmOrderIdMark, $orderMatch[$boAdmin->admin_name]]);
            }
        }

        $crmOrderOtherSendResult = $this->autoTaggingCrmOrders($rmOrderReadySendIds, $boAdmins, $orderMatch);
        if (!$crmOrderOtherSendResult) {
            Log::channel('crm_log')->error('auto send rest order fail!');
        }
        return true;
    }
}
