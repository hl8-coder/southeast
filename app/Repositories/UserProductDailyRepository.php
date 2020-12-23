<?php


namespace App\Repositories;


use Carbon\Carbon;
use App\Models\User;
use App\Models\Affiliate;
use Illuminate\Support\Facades\DB;
use App\Models\UserProductDailyReport;

class UserProductDailyRepository
{
    # 获取会员的所有记录
    public static function getUserBetLog($ids)
    {
        return UserProductDailyReport::whereIn('user_id', $ids);
    }

    # 获取会员的当月记录
    public static function currentMonth($ids)
    {
        $ORM = self::getUserBetLog($ids);
        $ORM->where("date", ">=", Carbon::now()->startOfMonth()->toDateString())
            ->where("date", "<=", Carbon::now()->endOfMonth()->toDateString());
        return $ORM;
    }

    # 获取代理的下级会员ID array
    public static function getSubUserIds($affiliateIds)
    {
        $userIds = User::whereIn('parent_id', $affiliateIds)
            ->pluck('id');

        return $userIds;
    }


    public static function getProfitInfoByAffiliate(Affiliate $affiliate)
    {
        $subUserIds = $affiliate->subUsers()->pluck('id')->toArray();

        // 按游戏类型统计盈亏总额（游戏类型分类），统计人数（去重），
        return UserProductDailyReport::selectRaw("`game_type`, `platform_code`, sum(`effective_bet`) as user_bet, sum(`effective_profit`) as platform_profit, sum(`bet_num`) as bet_count, count(distinct `user_name`) 'active_count'")
             ->where('date', '>=', now()->startOfMonth()->toDateString())
            ->where('date', '<=', now()->endOfMonth()->toDateString())
            ->whereIn('user_id', $subUserIds)
            ->leftJoin(DB::Raw("(select `code` as `product_code`, `type` as 'game_type' from `game_platform_products`) as `relation`"), 'user_product_daily_reports.product_code', '=', 'relation.product_code')
            ->groupBy(['game_type', 'platform_code'])
            ->get();
    }
}
