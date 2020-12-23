<?php
namespace App\Repositories;

use App\Models\Bonus;
use App\Models\Promotion;
use App\Models\PromotionClaimUser;
use App\Models\User;

class BonusRepository
{
    /**
     * 获取归属日期
     *
     * @param   Bonus   $bonus  红利
     * @return  string
     */
    public static function findDate(Bonus $bonus)
    {
        $now = now();
        switch ($bonus->cycle) {

            case Bonus::CYCLE_DAILY:
                $result = $now->toDateString();
                break;
            case Bonus::CYCLE_WEEKLY:
                $startWeek = $now->startOfWeek();
                $result = $bonus->effective_start_at > $startWeek ? $bonus->effective_start_at->toDateString() : $startWeek->toDateString();
                break;
            case Bonus::CYCLE_MONTHLY:
                $startMonth = $now->startOfMonth();
                $result = $bonus->effective_start_at > $startMonth ? $bonus->effective_start_at->toDateString() : $startMonth->toDateString();
                break;
            case Bonus::CYCLE_WHOLE: # 整个活动周期，记录活动第一天
            default:
                $result = $bonus->effective_start_at->toDateString();
                break;
        }

        return $result;
    }

    /**
     * 获取红利周期开始时间和结束时间
     *
     * @param   Bonus   $bonus    红利
     * @return  array
     */
    public static function getStartAndEndAt(Bonus $bonus)
    {
        $now = now();
        switch ($bonus->cycle) {
            case Bonus::CYCLE_DAILY:
                $startAt = $now->startOfDay();
                $endAt   = $now->copy()->endOfDay();
                break;
            case Bonus::CYCLE_WEEKLY:
                $startAt = $now->startOfWeek();
                $startAt = $bonus->effective_start_at > $startAt ? $bonus->effective_start_at : $startAt;
                $endAt   = $now->copy()->endOfWeek();
                $endAt   = $bonus->effective_end_at < $endAt ? $bonus->effective_end_at : $endAt;
                break;
            case Bonus::CYCLE_MONTHLY:
                $startAt = $now->startOfMonth();
                $startAt = $bonus->effective_start_at > $startAt ? $bonus->effective_start_at : $startAt;
                $endAt   = $now->copy()->endOfMonth();
                $endAt   = $bonus->effective_end_at < $endAt ? $bonus->effective_end_at : $endAt;
                break;
            case Bonus::CYCLE_WHOLE: # 整个活动周期，记录活动第一天
            default:
                $startAt = $bonus->effective_start_at;
                $endAt   = $bonus->effective_end_at;
                break;
        }

        return [
            'start_at' => $startAt,
            'end_at'   => $endAt,
        ];
    }

    /**
     * 检查会员是否报表名
     *
     * @param   integer   $bonusId
     * @param   integer   $userId
     * @return  bool
     */
    public static function checkIsClaimed($bonusId, $userId)
    {
        return PromotionClaimUser::query()->where('user_id', $userId)
                ->where('related_type', Promotion::RELATED_TYPE_BONUS)
                ->where('related_id', $bonusId)
                ->where('status', PromotionClaimUser::STATUS_APPROVE)
                ->exists();
    }

    /**
     * 获取会员能使用的红利
     *
     * @param   User    $user           会员
     * @param   null    $platformCode   平台code
     * @return  mixed
     */
    public static function getUserBonusesByCache(User $user, $platformCode=null)
    {
        $now = now();
        $bonuses = Bonus::getAll()->where('effective_start_at', '<=', $now)
            ->where('effective_end_at', '>', $now)
            ->where('status', true);

        if ($platformCode) {
            $bonuses = $bonuses->where('platform_code', $platformCode);
        }

        $userBonuses = [];

        foreach ($bonuses as $bonus) {

            if (!$bonus->getCurrencySet($user->currency)) {
                continue;
            }

            # 检查会员类型
            if (!static::checkUserType($bonus, $user)) {
                continue;
            }

            # 检查是否是报名，如果是报名需要检查是否报名
            if ($bonus->isNeedClaim() && !BonusRepository::checkIsClaimed($bonus->id, $user->id)) {
                continue;
            }

            # 检查组别限制
            if (UserBonusPrizeRepository::checkBonusGroupLimit($bonus, $user)) {
                continue;
            }

            $userBonuses[] = $bonus;
        }

        return collect($userBonuses);
    }

    /**
     * 检查红利是否可以报名
     *
     * @param User $user
     * @param Bonus $bonus
     */
    public static function checkIsCanClaim(Bonus $bonus, User $user)
    {
        # 检查红利状态
        static::checkBonusStatus($bonus);

        # 检查红利是否需要申请
        if (!$bonus->isNeedClaim()) {
            error_response(422, __('bonus.bonus_does_not_need_to_claim'));
        }

        # 检查注册日期
        $now = now();
        if (!empty($bonus->sign_start_at) && !empty($bonus->sign_end_at)  && ($now < $bonus->sign_start_at || $now >= $bonus->sign_end_at)) {
            error_response(422, __('bonus.bonus_not_in_the_time_range'));
        }

        # 检查会员注册时间
        static::checkSignUpDate($bonus, $user);

        # 检查红利有效时间
        static::checkEffectiveDate($bonus);

        # 检查会员类型
        static::checkUserTypeIsTrue($bonus, $user);

        # 检查红利组别限制
        static::checkBonusGroupLimit($bonus, $user);

        # 检查币别
        static::checkCurrency($bonus, $user);
    }

    /**
     * 检查红利状态
     *
     * @param Bonus $bonus
     */
    public static function checkBonusStatus(Bonus $bonus)
    {
        if (!$bonus->isEnable()) {
            error_response(422, __('bonus.bonus_code_invalid'));
        }
    }

    /**
     * 检查会员类型
     *
     * @param Bonus $bonus
     * @param User $user
     */
    public static function checkUserTypeIsTrue(Bonus $bonus, User $user)
    {
        if (!static::checkUserType($bonus, $user)) {
            error_response(422, __('bonus.no_permission_to_use_bonus_code'));
        }
    }

    /**
     * 判断会员是否符合红利条件
     *
     * @param   Bonus   $bonus      红利
     * @param   User    $user       会员
     * @return  bool
     */
    public static function checkUserType(Bonus $bonus, User $user)
    {
        $result = false;
        switch ($bonus->user_type) {
            case Bonus::USER_TYPE_ALL:
                $result = true;
                break;
            case Bonus::USER_TYPE_RISK:
                if (in_array($user->risk_group_id, $bonus->risk_group_ids)) {
                    $result = true;
                }
                break;

            case Bonus::USER_TYPE_PAYMENT:
                if (in_array($user->payment_group_id, $bonus->payment_group_ids)) {
                    $result = true;
                }
                break;

            case Bonus::USER_TYPE_RISK_AND_PAYMENT:
                if (in_array($user->risk_group_id, $bonus->risk_group_ids) && in_array($user->payment_group_id, $bonus->payment_group_ids)) {
                    $result = true;
                }
                break;
            case Bonus::USER_TYPE_LIST:
                if (in_array($user->id, $bonus->user_ids)) {
                    $result = true;
                }
                break;
        }

        return $result;
    }

    /**
     * 检查组别限制（一个组别只能使用同一个红利代码）
     *
     * @param $bonus
     * @param $user
     */
    public static function checkBonusGroupLimit(Bonus $bonus, User $user)
    {
        if (UserBonusPrizeRepository::checkBonusGroupLimit($bonus, $user)) {
            error_response(422, __('bonus.only_one_bonus_code_can_be_used_in_same_group'));
        }
    }

    /**
     * 检查币别
     *
     * @param $bonus
     * @param $user
     * @return
     */
    public static function checkCurrency(Bonus $bonus, User $user)
    {
        # 检查币别
        if (!$currencySet = $bonus->getCurrencySet($user->currency)) {
            error_response(422, __('bonus.no_permission_to_use_bonus_code'));
        }

        return $currencySet;
    }

    /**
     * 检查会员注册日期
     *
     * @param Bonus $bonus
     * @param User $user
     */
    public static function checkSignUpDate(Bonus $bonus, User $user)
    {
        $registerAt = $user->created_at;
        if ((!empty($bonus->sign_start_at) && $registerAt < $bonus->sign_start_at) || (!empty($bonus->sign_end_at) && $registerAt >= $bonus->sign_end_at)) {
            error_response(422, __('bonus.bonus_not_in_the_time_range'));
        }
    }

    /**
     * 检查红利有效时间
     *
     * @param Bonus $bonus
     */
    public static function checkEffectiveDate(Bonus $bonus)
    {
        $now = now();
        if ((!empty($bonus->effective_start_at) && $now < $bonus->effective_start_at) || (!empty($bonus->effective_end_at) && $now >= $bonus->effective_end_at)) {
            error_response(422, __('bonus.bonus_not_in_the_time_range'));
        }
    }
}