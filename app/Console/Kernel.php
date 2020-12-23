<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * The Artisan commands provided by your application.
     *
     * @var array
     */
    protected $commands = [
        //
    ];

    /**
     * Define the application's command schedule.
     *
     * @param \Illuminate\Console\Scheduling\Schedule $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        # 创建拉取报表
        $schedule->command('southeast:generate-game-platform-pull-report-schedules --days=7')->weekly();

        # 拉取平台报表
        $schedule->command('southeast:pull-game-platform-reports')->everyMinute();
        $schedule->command('southeast:pull-fail-game-platform-reports')->everyFifteenMinutes();

        # 将投注记录添加到处理流程
        $schedule->command('southeast:add-game-bet-details-to-process')->everyMinute();
        # 将失败投注记录重新添加进处理流程
        $schedule->command('southeast:add-fail-game-bet-details-to-process')->everyThirtyMinutes();

        # 重置公司银行卡每日记录字段数据
        $schedule->command('southeast:reset-company-bank-account-daily-records')->daily();

        # 计算返点
        $schedule->command('southeast:calculate-rebates')->dailyAt('12:00');

        # 每月月初计算上个月的分红
        $schedule->command('southeast:calculate-affiliate-commission-command 1')->monthlyOn(1);

        # 每个月十五号计算当月的分红
//        $schedule->command('southeast:calculate-affiliate-commission-command 0')->monthlyOn(15);


        # 分配 CRM 订单给BO 管理员
        //$schedule->command('southeast:auto-distribution-crm')->hourly();

        # CRM 上周注册人数统计，更新到 CRM 周报表
        $schedule->command('southeast:find-register-user-from-crm-resource')->weeklyOn(1, '1:00');

        # CRM 统计 FTD 相关信息
        $schedule->command('southeast:count-crm-ftd-amount')->weeklyOn(1, '1:00')->runInBackground();
        $schedule->command('southeast:count-crm-adjustment-amount')->weeklyOn(1, '2:00');

        # 每分钟递增生成一次 slot jackpot 的假数据
        $schedule->command('southeast:make-fake-slot-jackpot')->everyMinute();

        # 拉取GPI lottery信息
        $schedule->command('southeast:pull-gpi-lottery-report')->dailyAt('21:00');

        # 拉取IMSports结算状态报表
        $schedule->command('southeast:pull-imsports-settled-status-report')->hourly();

        # 游戏数据迁移历史表, 被迁移的游戏数据保证不会再进行修改.
        $schedule->command('southeast:migrate-game_bet-detail-to-history')->daily();

        # 统计报表
        $schedule->command('southeast:sum-user-bet-count-log')->everyFiveMinutes();

        # 每小时检查gpi是否有拉取错误
        $schedule->command('southeast:re-pull-gpi-fail-schedule')->hourly();

        # 每五分钟更新一次 Kpi Report 数据，执行统计汇总，大概 20 条 SQL，其中四到五条可能存在稍大的数据量
        $schedule->command('southeast:calculate-kpi-report')->everyFiveMinutes();

        # 将失败transaction重新添加进处理流程
        $schedule->command('southeast:add-fail-transactions-to-job')->everyMinute();

    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__ . '/Commands');

        require base_path('routes/console.php');
    }
}
