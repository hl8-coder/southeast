<?php

namespace App\Listeners;

use App\Events\ReportSavedEvent;
use App\Models\BetToRewardRule;
use App\Models\UserProductMonthlyReport;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;

class UpUserVipLevelListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  ReportSavedEvent  $event
     * @return void
     */
    public function handle(ReportSavedEvent $event)
    {
        $report = $event->report;
        $user = $report->user;

        $totalBet = UserProductMonthlyReport::findSum($report->user_id, 'bet', $report->date);

        # 获取vip下一等级
        if ($user->vip && $nextVip = $user->vip->findNext()) {
            # 满足vip触发vip升级
            if ($totalBet >= $nextVip->rule) {
                $user->updateVip($nextVip->id);
            }
        }

        # 获取积分等级下一等级
        if ($user->reward && $nextReward = $user->reward->findNext()) {
            # 获取积分
            $rewards = BetToRewardRule::getRewards($user->currency, $totalBet);

            # 满足积分等级触发积分等级升级
            if ($rewards >= $nextReward->rule) {
                $user->updateReward($nextReward->id);
            }
        }
    }
}
