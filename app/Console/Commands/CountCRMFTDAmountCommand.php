<?php

namespace App\Console\Commands;

use Carbon\Carbon;
use App\Models\Deposit;
use App\Models\CrmOrder;
use App\Models\CrmWeeklyReport;
use Illuminate\Console\Command;

class CountCRMFTDAmountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:count-crm-ftd-amount {--force=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '统计CRM系统 FTD 金额，并更新到 CRM 周统计报表';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        if ($this->option('force') == 1){
            # local 本地测试使用，测试时间为当周
            $weekStart = now()->startOfWeek();
            $weekEnd   = now()->endOfWeek();
            $week      = now()->weekOfYear;
        }else{
            # 统计时间为上周
            $weekStart = now()->subWeek()->startOfWeek();
            $weekEnd   = now()->subWeek()->endOfWeek();
            $week      = now()->subWeek()->weekOfYear;
        }

        $this->updateWelcomeFTD($week, $weekStart, $weekEnd); // 不受通话时间影响
        $this->updateNonDepositFTD($week, $weekStart, $weekEnd);
        $this->updateDailyRetentionFTD($week, $weekStart, $weekEnd);
        $this->updateRetentionFTD($week, $weekStart, $weekEnd);
    }


    # welcome 类型的首充定义为分配制，无论是否打电话，会员的首充效益都归电销人员
    private function updateWelcomeFTD(int $week, Carbon $weekStart, Carbon $weekEnd)
    {
        # 找出记录表数据
        $adminWeeklyReports = CrmWeeklyReport::query()
            ->where('week_start_at', $weekStart->toDateString())
            ->where('week_end_at', $weekEnd->toDateString())
            ->where('type', CrmWeeklyReport::TYPE_WELCOME)
            ->get();
        $adminIds = $adminWeeklyReports->pluck('admin_id')->toArray();

        # 本周所有已经分配 welcome 会员的中充值记录
        $relations = CrmOrder::query()->where('type', CrmOrder::TYPE_WELCOME)
            ->whereIn('admin_id', $adminIds)
            ->tagStart($weekStart)
            ->tagEnd($weekEnd)
            ->pluck('admin_id', 'user_id');

        # 将用户按 admin_id 分组
        $adminUserIds = [];
        $relations->each(function ($adminId, $userId) use (& $adminUserIds) {
            $adminUserIds[$adminId][] = $userId;
        });

        # 获取首充用户与对应的首充金额
        $userDeposit    = $this->getUsersFTD(array_keys($relations->toArray()), $weekStart, $weekEnd);
        $depositUserIds = array_keys($userDeposit);

        # 筛选出各个电销人员对应的首充用户
        foreach ($adminUserIds as $adminId => $userIds) {
            $adminUserIds[$adminId] = array_intersect($userIds, $depositUserIds);
        }

        # 统计首充用户，与首充总额
        $updateData = [];
        foreach ($adminWeeklyReports as $adminWeeklyReport) {
            if (isset($adminUserIds[$adminWeeklyReport->admin_id])){
                $FTDNumber = count($adminUserIds[$adminWeeklyReport->admin_id]);
                $FTDAmount = empty($userDeposit) ? $adminWeeklyReport->ftd_amount : collect($userDeposit)->only($adminUserIds[$adminWeeklyReport->admin_id])->sum();
            }else{
                $FTDNumber = $adminWeeklyReport->ftd_member === null ? 0 : $adminWeeklyReport->ftd_member;
                $FTDAmount = $adminWeeklyReport->ftd_amount === null ? 0 : $adminWeeklyReport->ftd_amount;
            }
            $updateData[] = [
                'id'         => $adminWeeklyReport->id,
                'ftd_member' => $FTDNumber,
                'ftd_amount' => $FTDAmount,
            ];
        }

        CrmWeeklyReport::updateBatch($updateData);
    }


    # 更新 retention FTD 相关信息
    private function updateRetentionFTD(int $week, Carbon $weekStart, Carbon $weekEnd)
    {
        $this->updateFTD(CrmOrder::TYPE_RETENTION, $week, $weekStart, $weekEnd);
    }

    # 更新 daily retention FTD 相关信息
    private function updateDailyRetentionFTD(int $week, Carbon $weekStart, Carbon $weekEnd)
    {
        $this->updateFTD(CrmOrder::TYPE_DAILY_RETENTION, $week, $weekStart, $weekEnd);
    }

    # 更新 non deposit FTD 相关信息
    private function updateNonDepositFTD(int $week, Carbon $weekStart, Carbon $weekEnd)
    {
        $this->updateFTD(CrmOrder::TYPE_NON_DEPOSIT, $week, $weekStart, $weekEnd);
    }


    # 通用更新FTD方法
    private function updateFTD(int $type, int $week, Carbon $weekStart, Carbon $weekEnd)
    {
        $update = [];
        CrmWeeklyReport::query()->where('week_start_at', $weekStart->toDateString())
            ->where('week_end_at', $weekEnd->toDateString())
            ->where('type', $type)
            ->chunk(1, function ($adminReports) use ($type, $weekStart, $weekEnd, & $update) {

                $adminWeekReport = $adminReports->first();

                $crmOrderUsers = CrmOrder::query()
                    ->with('crmCallLogs')
                    ->where('type', $type)
                    ->where('admin_id', $adminWeekReport->admin_id)
                    ->where('tag_at', '>=', $weekStart)
                    ->where('tag_at', '<=', $weekEnd)
                    ->get();

                $userFirstCallTime = [];
                $userIdCallList    = [];
                foreach ($crmOrderUsers as $crmOrderUser) {
                    $callLog = $crmOrderUser->crmCallLogs->sortBy('id', SORT_ASC)->first();
                    if ($callLog) {
                        $userIdCallList[]                     = $callLog->crmOrder->user_id;
                        $userFirstCallTime[$callLog->crmOrder->user_id] = Carbon::parse($callLog->created_at);
                    }
                }

                $userDeposits = $this->getUsersFTD($userIdCallList, $weekStart, $weekEnd, true); // user_id => success deposit first
                $ftdMemberIds = [];
                $ftdAmount    = 0;
                foreach ($userDeposits as $userDeposit) {
                    $statementAt = Carbon::parse($userDeposit->statement_at);
                    $callTime    = $userFirstCallTime[$userDeposit->user_id];
                    if ($statementAt->lte($callTime)) {
                        $ftdMemberIds[] = $userDeposit->user_id;
                        $ftdAmount      = $ftdAmount + $userDeposit->amount;
                    }
                }

                $update[] = [
                    'id'         => $adminWeekReport->id,
                    'ftd_member' => count($ftdMemberIds),
                    'ftd_amount' => $ftdAmount == 0 ? $adminWeekReport->ftd_amount : $ftdAmount,
                ];
            });

        CrmWeeklyReport::updateBatch($update);
    }

    /**
     * 获取用户指定时间内首次充值的金额，返回的用户即为有成功充值的用户
     * @param array $userIds
     * @param Carbon $timeStart
     * @param Carbon $timeEnd
     * @param bool $needObject
     * @return array [user_id => amount, ... ]
     */
    private function getUsersFTD(array $userIds, Carbon $timeStart, Carbon $timeEnd, bool $needObject = false): array
    {
        # 这里需要定义首充时间的具体字段，按银行到账时间定义首充时间 2019-12-19 15：43 mark by martin
        $depositData = [];
        $relation    = [];

        $deposits = Deposit::query()->whereIn('user_id', $userIds)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where('statement_at', '>=', $timeStart)
            ->where('statement_at', '<=', $timeEnd)
            ->orderByDesc('user_id')
            ->chunk(100, function ($deposits) use (&$depositData) {

                # 每批数据 同一个 UseId 将会被去重
                $userIdUnique = collect($deposits)->unique('user_id')->pluck('user_id');
                foreach ($userIdUnique as $userId) {
                    $depositData[] = $deposits->where('user_id', $userId)->sortBy('statement_at', SORT_ASC)->first();
                }
            });

        # 再次去重，预防两批数据有同一个 user id
        $userIdUnique = collect($depositData)->unique('user_id')->pluck('user_id');
        $userIdUnique->chunk(10)->each(function ($UserIds) use (& $relation, $depositData, $needObject) {
            foreach ($UserIds as $userId) {
                $data = collect($depositData)
                    ->where('user_id', $userId)
                    ->sortBy('statement_at', SORT_ASC)
                    ->first();
                if ($needObject) {
                    $relation[$userId] = $data;
                } else {
                    $relation[$userId] = $data->amount;
                }
            }
        });

        return $relation;
    }
}
