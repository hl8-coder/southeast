<?php


namespace App\Services;


use App\Models\Admin;
use App\Models\CrmBoAdmin;
use App\Models\CrmExcludeUser;
use App\Models\CrmOrder;
use App\Models\CrmResource;
use App\Models\User;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 *
 * @package App\Services
 */
class CrmService
{
    private $crmOrder;

    public function __construct(CrmOrder $crmOrder)
    {
        $this->crmOrder = $crmOrder;
    }


    /**
     * 分页获取 crm orders 列表数据
     *
     * @param Request $request
     * @return LengthAwarePaginator
     *
     * @author  Martin
     */
    public function getCrmOrdersPaginate(Request $request)
    {
        # 检测当前用户，如果是 crm bo admin，则只显示自己名下的用户
        $admin  = auth('admin')->user();
        $exists = CrmBoAdmin::query()->where('admin_id', $admin->id)->exists();
        $query  = QueryBuilder::for(CrmOrder::class)
            ->allowedFilters(
                Filter::exact('type'),
                Filter::scope('name'),
                Filter::scope('full_name'),
                Filter::scope('email'),
                Filter::scope('phone'),
                Filter::scope('currency'),
                Filter::scope('user_status'),
                Filter::scope('risk_group_id'),
                Filter::scope('payment_group_id'),
                Filter::scope('affiliated_code'),
                Filter::scope('register_start'),
                Filter::scope('register_end'),
                Filter::scope('last_login_start'),
                Filter::scope('last_login_end'),
                Filter::scope('last_deposit_start'),
                Filter::scope('last_deposit_end'),
                Filter::scope('tag_start'),
                Filter::scope('tag_end'),
                Filter::scope('last_save_start'),
                Filter::scope('last_save_end'),
                Filter::scope('register_ip'),
                Filter::scope('deposit'),
                Filter::exact('status'),
                Filter::exact('call_status'),
                Filter::exact('admin_name'),
                Filter::exact('tag_admin_name')
            )
            ->with(['user'])
            ->latest();
        if ($exists) {
            $query->where('admin_id', $admin->id);
        }
        $pageData = $query->paginate($request->per_page);
        return $pageData;
    }

    /**
     * 人为批量派发订单，或者更换crm bo user
     * note: 用户状态非 active，订单为 locked 状态，已经有通话记录，上述三个条件下的订单不可以被派发
     *
     * @param array $crmOrderIds
     * @param $adminId
     * @param bool $distribute
     * @return bool|string
     *
     * @author Martin
     */
    public function batchUpdateCrmOrders(array $crmOrderIds, $adminId, bool $distribute = false)
    {
        $checkResult = $this->checkCrmOrdersCanTagging($crmOrderIds);
        if ($checkResult !== true) {
            return $checkResult;
        }
        $batchData = $this->batchUpdate($crmOrderIds, $adminId, $distribute);

        if (count($crmOrderIds) == CrmOrder::updateBatch($batchData)) {
            $result = true;
        } else {
            $result = 'There are some orders tagging fail!';
        }

        return $result;
    }

    /**
     * 人为批量更新资源订单
     *
     * @param array $crmResourceIds
     * @param $adminId
     * @param bool $distribute
     * @return bool|string
     *
     * @author Martin
     */
    public function batchUpdateCrmResources(array $crmResourceIds, $adminId, bool $distribute = false)
    {
        $can = $this->checkCrmResourcesCanTagging($crmResourceIds);
        if (!$can) {
            return 'There are some orders tagging fail!';
        }
        $batchData = $this->batchUpdate($crmResourceIds, $adminId, $distribute);

        if (count($crmResourceIds) == CrmResource::updateBatch($batchData)) {
            $result = true;
        } else {
            $result = 'There are some orders tagging fail!';
        }

        return $result;
    }

    /**
     * 人为批量派单或者取消订单数据
     *
     * @param array $tableIds
     * @param int $adminId crm_bo_admins.admin_id
     * @param bool $distribute 派单：true，取消：false
     * @return array 待更新到数据的字段数组信息
     *
     * @author Martin
     */
    protected function batchUpdate(array $tableIds, $adminId, bool $distribute = false)
    {
        $admin = auth('admin')->user();

        if ($distribute) {
            $crmBoAdmin     = CrmBoAdmin::query()->where('admin_id', $adminId)->first();
            $crmBoAdminId   = $crmBoAdmin->admin_id;
            $crmBoAdminName = $crmBoAdmin->admin_name;
        } else {
            $crmBoAdminId   = null;
            $crmBoAdminName = null;
        }

        $batchData = [];
        foreach ($tableIds as $tableId) {
            $batchData[] = [
                'id'                        => $tableId,
                'admin_id'                  => $crmBoAdminId,
                'admin_name'                => $crmBoAdminName,
                'tag_admin_id'              => $admin->id,
                'tag_admin_name'            => $admin->name,
                'tag_at'                    => now(),
                'last_save_case_admin_id'   => $admin->id,
                'last_save_case_admin_name' => $admin->name,
                'last_save_case_at'         => now(),
                'is_auto'                   => false,
            ];
        }

        return $batchData;
    }

    /**
     * 检测待检测的CRM ORDER，检测是否每个都适合Tagging
     *
     * @param array $crmOrderIds
     * @return bool|string
     *
     * @author Martin
     */
    protected function checkCrmOrdersCanTagging(array $crmOrderIds)
    {
        $numIds = count($crmOrderIds);
        // 检测：订单为 open 状态
        $crmOrders = CrmOrder::query()->whereIn('id', $crmOrderIds)
            ->where('status', CrmOrder::STATUS_OPEN)
            ->get();
        if ($crmOrders->count() != $numIds) {
            return 'There are some orders status not allow tagging!';
        }

        // 检测：未有通话记录
        $crmOrders = $crmOrders->where('call_status', null);
        if ($crmOrders->count() != $numIds) {
            return 'There are some orders have been called!';
        }

        // 检测：用户状态非 active，
        $userIsActive = $crmOrders->every(function ($crmOrder) {
            return $crmOrder->user->status == User::STATUS_ACTIVE;
        });

        if (!$userIsActive) {
            return 'There are some members` status are not active!';
        }

        return true;
    }

    /**
     * 检测 crm resource 订单是否可以被派发
     *
     * @param array $crmResourceIds
     * @return bool
     *
     * @author Martin
     */
    public function checkCrmResourcesCanTagging(array $crmResourceIds)
    {
        // 检测是否为未拨打状态
        return CrmResource::query()->whereIn('id', $crmResourceIds)
            ->where('status', CrmResource::STATUS_LOCKED)
            ->whereNotNull('call_status')
            ->doesntExist();
    }


    /**
     * 批量将订单锁住
     * @param array crm_orders id
     * @return boolean
     */
    public function crmOrderBatchLocked(array $crmOrderIds)
    {
        $lockData = [];
        foreach ($crmOrderIds as $crmOrderId) {
            $lockData[] = ['id' => $crmOrderId, 'status' => true];
        }
        return CrmOrder::updateBatch($lockData);
    }


    /**
     * 检测订单是否在可添加 crm call log 状态
     * @param $crmOrder |null
     * @return bool|string
     *
     * @author Martin
     */
    public function checkCrmOrderCanCreateCrmCallLog($crmOrder)
    {
        if (empty($crmOrder)) {
            return 'Order is not exists!';
        }

        if ($crmOrder->isLocked()) {
            return 'CRM order is locked!';
        }

        return true;
    }

    /**
     * crm bo user 修改记录中，将 audit 信息重组返回
     *
     * @param $auditValue
     * @return string
     *
     * @author Martin
     */
    public function transferAudit($auditValue): string
    {
        if (empty($auditValue)) {
            return '';
        }
        $value = '';
        switch ($auditValue) {
            case isset($auditValue['admin_name']):
                $value .= $auditValue['admin_name'] . ' | ';
                break;
            case isset($auditValue['status']):
                $value .= 'status:' . CrmBoAdmin::$statuses[$auditValue['status']] . ' | ';
                break;
            case isset($auditValue['on_duty']):
                $value .= CrmBoAdmin::$onDuty[$auditValue['on_duty']] . ' | ';
                break;
            case isset($auditValue['start_worked_at']):
                $value .= 'start work at: ' . $auditValue['start_worked_at'] . ' | ';
                break;
            case isset($auditValue['end_worked_at']):
                $value .= 'end work at: ' . $auditValue['end_worked_at'] . ' | ';
                break;
            default:
                $value .= json_encode($auditValue) . ' | ';
                break;
        }
        return substr($value, 0, -3);
    }

    /**
     * 检测 admin 是否有权限操作该数据
     * @param CrmExcludeUser $crmExcludeUser
     * @param Admin $admin
     * @return bool
     *
     * @author Martin
     */
    public function checkAdminCanDeleteExcludeUser(CrmExcludeUser $crmExcludeUser, Admin $admin)
    {
        // 删除条件：数据建立者且数据未被审核，超级管理员【组长之类】
        $crmBoAdminExists = CrmBoAdmin::query()->where('admin_id', $admin->id)->exists();
        if (!$crmBoAdminExists) {
            return true;
        }

        if ($crmExcludeUser->admin_id == $admin->id && $crmExcludeUser->review_by == null) {
            return true;
        }
        return false;
    }


    /**
     * 获取黑名单包含的用户ID
     * @return array
     */
    public function getExcludeUserIdList(): array
    {
        $userIds = CrmExcludeUser::query()
            ->where('is_affiliate', CrmExcludeUser::STATUS_TRUE)
            ->where('status', CrmExcludeUser::STATUS_TRUE)
            ->pluck('user_id')
            ->toArray();

        $affiliatedCodes = $this->getExcludeAffiliateCodes();

        $affiliateSubUserIds = User::query()
            ->whereIn('affiliated_code', $affiliatedCodes)
            ->where('status', User::STATUS_ACTIVE)
            ->pluck('id')
            ->toArray();

        $allUserIds = Arr::flatten([$userIds, $affiliateSubUserIds]);
        return array_unique($allUserIds);
    }

    /**
     * 获取被 crm 拉黑的代理线的代理 code
     * @return array
     */
    public function getExcludeAffiliateCodes(): array
    {
        return CrmExcludeUser::query()
            ->where('is_affiliate', CrmExcludeUser::STATUS_TRUE)
            ->where('status', CrmExcludeUser::STATUS_TRUE)
            ->pluck('affiliate_code')
            ->toArray();
    }

    /**
     * 根据admin id 获取用户ID
     *
     * @param array $adminIds 查询的 admin id 数组
     * @param null $tagStart tag开始时间
     * @param null $tagEnd tag截止时间
     * @param null $type 订单类型
     * @return array 用户ID，二维数组，一维数组的key 是 admin id
     *
     * @author  Martin
     * @date    22/7/2020 11:34 pm
     * @version viet-214
     */
    public function getCalledUserIdByAdminIds(array $adminIds, $tagStart = null, $tagEnd = null, $type = null)
    {
        $ORM = CrmOrder::query()->whereIn('admin_id', $adminIds)
            ->with('crmCallLogs')
            ->whereHas('crmCallLogs');

        if ($tagStart) {
            $ORM->tagStart($tagStart);
            // $ORM->where('tag_at', '>=', $tagStart);
        }
        if ($tagEnd) {
            $ORM->tagEnd($tagEnd);
            // $ORM->where('tag_at', '<=', $tagEnd);
        }
        if ($type) {
            $ORM->where('type', $type);
        }

        $info = $ORM->get();

        $relation = [];
        foreach ($info as $item) {
            $relation[$item->admin_id][] = $item->user_id;
        }
        return $relation;
    }

    public function createWelcomeOrder(User $user)
    {

        $welcomeOrder = [
            'user_id' => $user->id,
        ];
        $affiliate    = $user->affiliate;
        if ($affiliate) {
            $welcomeOrder['affiliate_id']    = $affiliate->id;
            $welcomeOrder['affiliated_code'] = $affiliate->affiliate_code;
        }
        return CrmOrder::query()->create($welcomeOrder);
    }

}
