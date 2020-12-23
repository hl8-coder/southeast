<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\GamePlatform;
use App\Models\Remark;
use App\Models\User;
use App\Repositories\BonusRepository;
use App\Repositories\DepositRepository;
use App\Repositories\RemarkRepository;
use App\Repositories\UserBonusPrizeRepository;

class BonusService
{
    /**
     * 创建红利
     *
     * @param   GamePlatform    $platform
     * @param   User            $user
     * @param   string          $bonusCode      红利代码
     * @param   float           $depositAmount  充值金额
     * @return  \App\Models\UserBonusPrize
     */
    public function store(GamePlatform $platform, User $user, $bonusCode, $depositAmount)
    {
        $bonus = Bonus::findByCodeFromCache($bonusCode);

        # 检查产品code是否正确
        if ($bonus->platform_code != $platform->code) {
            error_response(422, __('gamePlatform.bonus_code_does_not_belong_to_this_product'));
        }

        # 检查红利状态
        BonusRepository::checkBonusStatus($bonus);

        # 检查会员类型
        BonusRepository::checkUserTypeIsTrue($bonus, $user);

        # 检查红利是否需要申请,并且在申请审批通过
        if ($bonus->isNeedClaim() && !BonusRepository::checkIsClaimed($bonus->id, $user->id)) {
            error_response(422, __('gamePlatform.bonus_code_need_to_apply'));
        }

        # 检查是否存在对应币别的红利代码
        $currencySet = BonusRepository::checkCurrency($bonus, $user);

        # 检查转账金额是否符合最小转账金额限制
        if ($currencySet['min_transfer'] > $depositAmount) {
            error_response(422, __('gamePlatform.transfer_amount_does_not_meet_the_requirement'));
        }

        # 检查会员注册时间
        BonusRepository::checkSignUpDate($bonus, $user);

        # 检查红利有效时间
        BonusRepository::checkEffectiveDate($bonus);

        # 检查红利组别限制
        BonusRepository::checkBonusGroupLimit($bonus, $user);

        # 检查周期限制
        if (UserBonusPrizeRepository::checkCycleLimit($bonus, $user)) {
            error_response(422, __('gamePlatform.bonus_code_is_used'));
        }

        # 检查周期充值笔数
        $this->checkDepositCountLimit($bonus, $user, $currencySet);

        # 创建奖励
        $remark = null;

        if ($bonus->isAutoHoldWithdrawal()) {
            $remark = RemarkRepository::create(
                $user->id,
                Remark::TYPE_HOLD_WITHDRAWAL,
                Remark::CATEGORY_PROMOTION,
                RemarkRepository::getBonusReason($bonus, $currencySet)
            );
        }
        $remarkId = $remark ? $remark->id : null;

        return UserBonusPrizeRepository::createPrize($bonus, $user, $currencySet, $depositAmount, $remarkId);
    }

    /**
     * 检查充值笔数
     *
     * @param Bonus $bonus
     * @param array $currencySet
     * @param User $user
     */
    public function checkDepositCountLimit(Bonus $bonus, User $user, $currencySet)
    {
        if ($currencySet['deposit_count'] <= 0) {
            return;
        }

        # 获取日期
        $date = BonusRepository::getStartAndEndAt($bonus);

        # 获取充值次数
        $depositCount = DepositRepository::getSuccessDepositCount($user, $date['start_at'], $date['end_at']);

        if ($depositCount < $currencySet['deposit_count']) {
            error_response(422, __('bonus.INSUFFICIENT_RECHARGE'));
        }
    }

}