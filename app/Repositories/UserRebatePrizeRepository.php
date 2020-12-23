<?php
namespace App\Repositories;

use App\Models\Rebate;
use App\Models\Report;
use App\Models\User;
use App\Models\UserBetCountLog;
use App\Models\UserProductDailyReport;
use App\Models\UserRebatePrize;
use App\Services\ReportService;
use Illuminate\Support\Facades\DB;

class UserRebatePrizeRepository
{
    /**
     * 创建会员返点
     *
     * @param   User                    $user
     * @param   Rebate                  $rebate
     * @param   UserProductDailyReport  $report
     * @return  UserRebatePrize|null
     */
    public static function create(User $user, Rebate $rebate, UserProductDailyReport $report)
    {
        $vipSet = $rebate->getVipSet($user->vip_id);
        # 获取奖励值
        $prize = static::getPrizeValue($vipSet, $report);

        # 获取最小金额和最高金额
        $currencySet = $rebate->getCurrencySet($user->currency);
        # 奖励小于最小金额直接返回
        if ($prize < $currencySet['min_prize']) {
            return null;
        }

        $userRebatePrize = new UserRebatePrize([
            'user_id'               => $report->user_id,
            'user_name'             => $report->user_name,
            'risk_group_id'         => $user->risk_group_id,
            'vip_id'                => $user->vip_id,
            'rebate_code'           => $rebate->code,
            'report_id'             => $report->id,
            'effective_bet'         => $report->effective_bet,
            'close_bonus_bet'       => $report->close_bonus_bet,
            'calculate_rebate_bet'  => $report->calculate_rebate_bet,
            'currency'              => $user->currency,
            'product_code'          => $report->product_code,
            'is_manual_send'        => $rebate->is_manual_send,
            'date'                  => $report->date,
            'multipiler'            => $vipSet['multipiler'],
        ]);

        if (!empty($currencySet['max_prize']) && $prize >= $currencySet['max_prize']) {
            $userRebatePrize->is_max_prize = true;
            $prize = $currencySet['max_prize'];
        }
        $userRebatePrize->prize = $prize;

        # 判断是否需要手动
        if ($rebate->isManualSend()) {
            $userRebatePrize->status = UserRebatePrize::STATUS_WAITING_MARKET_SEND;
        } else {
            $userRebatePrize->status = UserRebatePrize::STATUS_CREATED;
        }

        $userRebatePrize->save();

        return $userRebatePrize;
    }

    /**
     * 获取奖励值
     *
     * @param array                  $vipSet
     * @param UserProductDailyReport $report
     * @return float|int
     */
    public static function getPrizeValue($vipSet, UserProductDailyReport $report)
    {
        return (float)$report->calculate_rebate_bet * $vipSet['multipiler'] / 100;
    }


    /**
     * 设计返点订单为成功派发，并且将派发金额添加到会员的统计系统
     *
     * @param UserRebatePrize $prize
     * @param null $adminName
     * @return bool
     */
    public static function setSuccess(UserRebatePrize $prize, $adminName=null)
    {
        if ($prize->success($adminName)) {
            UserBetCountLog::report(
                UserBetCountLog::PREFIX_REBATE . $prize->id,
                $prize->user->id,
                $prize->product_code,
                $prize->payment_sent_at,
                [Report::$productMappingTypes[Report::TYPE_REBATE] => $prize->prize]
            );

            return true;
        }

        return false;
    }

    /**
     * 获取最近一笔返点
     *
     * @param array $userIds
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getLatestRebateByUser(array $userIds)
    {
        if (empty($userIds)){
            return null;
        }
        $ids = DB::select("select id from (select * from `user_rebate_prizes` where `user_id` in (". implode(',',$userIds) .") order by created_at desc) as temp group by `id` order by created_at desc");
        $ids = collect($ids)->pluck('id')->toArray();
        return UserRebatePrize::query()->whereIn('id', $ids)->get();
    }
}
