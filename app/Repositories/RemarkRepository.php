<?php
namespace App\Repositories;

use App\Models\Bonus;
use App\Models\BonusPrize;
use App\Models\Remark;

class RemarkRepository
{
    /**
     * 创建remark
     *
     * @param   int     $userId     会员id
     * @param   int     $type       类型
     * @param   int     $category   分类
     * @param   string  $reason     理由
     * @param   null    $adminName
     * @return  Remark
     */
    public static function create($userId, $type, $category, $reason='', $adminName=null)
    {
        $remark = new Remark();

        $remark->user_id    = $userId;
        $remark->type       = $type;
        $remark->category   = $category;
        $remark->reason     = $reason;
        $remark->admin_name = $adminName;

        $remark->save();

        return $remark;
    }

    /**
     * 判断是否存在未移除的remark
     *
     * @param  integer      $userId     会员id
     * @return bool
     */
    public static function isHasWithdrawalNotRemoveRemark($userId)
    {
        return Remark::query()->where('user_id', $userId)->whereIn('type', Remark::$holdWithdrawalTypes)->exists();
    }


    /**
     * 获取红利理由
     *
     * title + rollover + max_payout_amount
     *
     * @param Bonus $bonus
     * @param array $currencySet
     * @return string
     */
    public static function getBonusReason(Bonus $bonus, $currencySet)
    {
        return $bonus->title . '(' . $bonus->rollover . ' Times Rollover)-Max Payout Amt' . $currencySet['max_prize'];
    }

    /**
     * 判断是否存在未移除的自动hold的提现类型
     *
     * @param  integer  $userId     会员id
     * @return bool
     */
    public static function isHasBonusHoldWithdrawalType($userId)
    {
        return Remark::query()->where('user_id', $userId)
            ->where('type', Remark::TYPE_HOLD_WITHDRAWAL)
            ->where('category', Remark::CATEGORY_PROMOTION)
            ->exists();
    }

    public static function getByYear($userId, $years)
    {
        return Remark::query()->where('user_id', $userId)
                    ->withTrashed()
                    ->where('created_at', '>=', now()->subYears($years));
    }
}