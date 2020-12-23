<?php
namespace App\Repositories;

use App\Models\AffiliateCommission;
use App\Models\User;
use App\Models\UserPlatformDailyReport;
use App\Models\UserProductDailyReport;
use Illuminate\Support\Facades\DB;

class AffiliateRepository
{
    /**
     * 从下到上获取所有代理
     *
     * @return mixed
     */
    public static function getAffiliateByParentIdList()
    {
        return User::query()->isAgent()->orderByDesc('parent_id_list')->get();
    }

    /**
     * 获取所有直属下级游戏相关统计数据
     *
     * @param $parentId
     * @param $startAt
     * @param $endAt
     * @return mixed
     */
    public static function getAllSubUserGameData($parentId, $startAt, $endAt)
    {
        return UserProductDailyReport::query()->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('users.parent_id', $parentId)
            ->where('users.is_agent', false)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->first([
                DB::raw('SUM(profit) as profit'),
                DB::raw('SUM(stake) as stake'),
                DB::raw('SUM(rebate) as rebate'),
            ]);
    }

    /**
     * 获取自身游戏统计数据
     *
     * @param $userId
     * @param $startAt
     * @param $endAt
     * @return mixed
     */
    public static function getSelfGameData($userId, $startAt, $endAt)
    {
        return UserProductDailyReport::query()
            ->where('user_id', $userId)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->first([
                DB::raw('SUM(profit) as profit'),
                DB::raw('SUM(stake) as stake'),
                DB::raw('SUM(rebate) as rebate'),
            ]);
    }

    /**
     * 获取所有直属下级充值和提现相关统计数据
     *
     * @param $parentId
     * @param $startAt
     * @param $endAt
     * @return mixed
     */
    public static function getAllSubUserTransactionData($parentId, $startAt, $endAt)
    {
        # 统计报表中的充值和提现
        return UserPlatformDailyReport::query()->leftJoin('users', 'users.id', '=', 'user_platform_daily_reports.user_id')
            ->where('users.parent_id', $parentId)
            ->where('users.is_agent', false)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->first([
                DB::raw('SUM(deposit) as deposit'),
                DB::raw('SUM(withdrawal) as withdrawal'),
                DB::raw('SUM(promotion) as promotion'),
                DB::raw('SUM(adjustment_in - adjustment_out) as adjustment'),
            ]);
    }

    /**
     * 获取自身的调整相关统计数据
     *
     * @param $userId
     * @param $startAt
     * @param $endAt
     * @return mixed
     */
    public static function getSelfTransactionData($userId, $startAt, $endAt)
    {
        # 统计报表中的充值和提现
        return UserPlatformDailyReport::query()
            ->where('user_id', $userId)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->first([
                DB::raw('SUM(deposit) as deposit'),
                DB::raw('SUM(withdrawal) as withdrawal'),
                DB::raw('SUM(promotion) as promotion'),
                DB::raw('SUM(adjustment_in - adjustment_out) as adjustment'),
            ]);
    }

    /**
     * 获取所有直属代理抽成总金额
     *
     * @param $parentId
     * @param $startAt
     * @param $endAt
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|int|mixed
     */
    public static function getAllSubAgentCommission($parentId, $startAt, $endAt)
    {
        $totalCommission = AffiliateCommission::query()->leftJoin('users', 'users.id', '=', 'affiliate_commissions.user_id')
            ->where('users.parent_id', $parentId)
            ->where('users.is_agent', true)
            ->where('start_at', '>=', $startAt->toDateString())
            ->where('end_at', '<=', $endAt->toDateString())
            ->first([
                DB::raw('SUM(parent_commission) as parent_commission'),
            ]);

        return !empty($totalCommission['parent_commission']) ? $totalCommission['parent_commission'] : 0;
    }

    /**
     * 获取最近一次分红
     *
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getRecentAffiliateCommission($userId)
    {
        return AffiliateCommission::query()->where('user_id', $userId)->latest('end_at')->first();
    }

    /**
     * 获取所有直属下级会员总数
     *
     * @param $parentId
     * @param $startAt
     * @param $endAt
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Query\Builder|int|mixed
     */
    public static function getSubActiveUserCount($parentId, $startAt, $endAt)
    {
        $activeCount =  UserProductDailyReport::query()->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('users.parent_id', $parentId)
            ->where('users.is_agent', false)
            ->where('users.status', User::STATUS_ACTIVE)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->where('stake', '>', 0)
            ->first([
                DB::raw('COUNT(DISTINCT user_id) as active'),
            ]);

        return !empty($activeCount['active']) ? $activeCount['active'] : 0;
    }
}