<?php

namespace App\Console\Commands;

use App\Models\Adjustment;
use App\Models\CrmWeeklyReport;
use App\Services\CrmService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Arr;

class CountCRMAdjustmentAmountCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:count-crm-adjustment-amount {--force=0}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '这周统计上周 CRM weekly 中 adjustment amount 字段数据';

    /*
    统计规则：在统计周期内，分配给 bo user[admin] 的用户[user] 所有的 adjustment 金额中，仅统计 retention 和 promotion 两种类型的
            adjustment，其他的 adjustment 不需要统计进去，adjustment 的时间节点，以 adjustment 的开始时间为准
     */
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
            # 测试使用，全部更新
            $info  = CrmWeeklyReport::query()->select(['week'])->distinct()->get();
            $weeks = array_unique(Arr::flatten($info->toArray()));
            foreach ($weeks as $week) {
                $report    = CrmWeeklyReport::query()->where('week', $week)->first();
                $timeStart = $report->week_start_at . ' 00:00:00';
                $timeEnd   = $report->week_end_at . ' 23:59:59';
                echo $this->updateReport($report->week, $timeStart, $timeEnd);
            }
        }else{
            # 正式使用
            $week      = now()->subWeek()->weekOfYear;
            $timeStart = now()->subWeek()->startOfWeek()->toDateTimeString();
            $timeEnd   = now()->subWeek()->endOfWeek()->toDateTimeString();
            return $this->updateReport($week, $timeStart, $timeEnd);
        }
    }

    protected function updateReport($lastWeek, $timeStart, $timeEnd)
    {
        $dateWeekStart = Carbon::parse($timeStart)->toDateString();
        $adminIds      = CrmWeeklyReport::getWeeklyAdminIds($lastWeek);
        $relation      = app(CrmService::class)->getCalledUserIdByAdminIds($adminIds, $timeStart, $timeEnd);
        $userIds       = array_unique(Arr::collapse($relation));


        $categoryList = [
            Adjustment::CATEGORY_RETENTION,
            Adjustment::CATEGORY_PROMOTION
        ];

        $depositGroup  = [];
        $withdrawGroup = [];

        // 先计算 deposit
        // 分组统计，按照 admin id 为组 key
        Adjustment::query()->whereIn('user_id', $userIds)
            ->where('type', Adjustment::TYPE_DEPOSIT)
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->whereIn('category', $categoryList)
            ->where('created_at', '>=', $timeStart)
            ->where('created_at', '<=', $timeEnd)
            ->chunk(20, function ($collection) use ($relation, &$depositGroup) {
                foreach ($collection as $adjustment) {
                    foreach ($relation as $adminId => $userIds) {
                        if (!isset($depositGroup[$adminId])) {
                            $depositGroup[$adminId] = 0;
                        }
                        if (in_array($adjustment->user_id, $userIds)) {
                            $depositGroup[$adminId] += $adjustment->amount;
                        }
                    }
                }
            });

        // 计算 withdraw
        Adjustment::query()->whereIn('user_id', $userIds)
            ->where('type', Adjustment::TYPE_WITHDRAW)
            ->where('status', Adjustment::STATUS_SUCCESSFUL)
            ->whereIn('category', $categoryList)
            ->where('created_at', '>=', $timeStart)
            ->where('created_at', '<=', $timeEnd)
            ->chunk(20, function ($collection) use ($relation, &$withdrawGroup) {
                foreach ($collection as $adjustment) {
                    foreach ($relation as $adminId => $userIds) {
                        if (!isset($withdrawGroup[$adminId])) {
                            $withdrawGroup[$adminId] = 0;
                        }
                        if (in_array($adjustment->user_id, $userIds)) {
                            $withdrawGroup[$adminId] += $adjustment->amount;
                        }
                    }
                }
            });

        $totalAmount = [];
        foreach ($depositGroup as $depositAdminId => $depositAmount) {
            if (!isset($totalAmount[$depositAdminId])) {
                $totalAmount[$depositAdminId] = 0;
            }
            $totalAmount[$depositAdminId] += $depositAmount;

            foreach ($withdrawGroup as $withdrawAdminId => $withdrawAmount) {
                if (!isset($totalAmount[$withdrawAdminId])) {
                    $totalAmount[$withdrawAdminId] = 0;
                }
                $totalAmount[$withdrawAdminId] -= $withdrawAmount;
            }
        }

        foreach ($totalAmount as $adminId => $amount) {
            CrmWeeklyReport::query()->where('week', $lastWeek)
                ->where('week_start_at', $dateWeekStart)
                ->where('admin_id', $adminId)
                ->update(['adjustment_amount' => $amount]);
        }


        return true;

        // 限定统计时间范围
        // 找到待统计的 admin_id
        // 找到 user_id 集合
        // 根据 user_id 和 时间范围 和 类型、状态 查询 adjustment 各个用户的 adjustment 总和

        // 如果存在一个用户被派给不止一个 admin 的情况，优先计算 welcome，否则是以先派发的为准
    }
}
