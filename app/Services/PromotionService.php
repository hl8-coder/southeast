<?php

namespace App\Services;

use App\Models\Bonus;
use App\Models\Promotion;
use App\Models\PromotionClaimUser;
use App\Models\User;
use App\Repositories\BonusRepository;

class PromotionService
{
    /**
     * 申请红利
     *
     * @param   Promotion   $promotion
     * @param   User        $user
     * @param   mixed       $relatedModel
     * @param   string      $frontRemark
     * @return  PromotionClaimUser|\Illuminate\Database\Eloquent\Model
     */
    public function claim(Promotion $promotion, User $user, $relatedModel=null, $frontRemark='')
    {
        # 检查优惠状态
        if ($promotion->isInActive()) {
            error_response(422, __('promotion.inactive_promotion_dont_claim'));
        }

        # 检查会员是否已申请
        if (PromotionClaimUser::isAlreadyClaimed($promotion->id, $user->id)) {
            error_response(422, __('promotion.promotion_has_been_claimed'));
        }

        # 针对泰迁移用户---参加过泰首存活动的用户无法再参与越南系统的首存.
        if($user->info->is_get_first_deposit_reward && $promotion->promotion_type_code == "new_members") { // 新手活动 且参与过泰首存
            error_response(422, __('bonus.only_one_bonus_code_can_be_used_in_same_group'));
        }

        # 检查关联模型状态及是否可以报名
        if ($relatedModel ) {
            $this->checkRelatedModel($promotion->related_type, $user, $relatedModel);
        }

        # 创建申请名单
        return PromotionClaimUser::add($promotion, $user, $relatedModel, $frontRemark);
    }

    /**
     * 获取关联model
     *
     * @param   integer     $relatedType    关联类型  1:红利
     * @param   string      $code           关联code
     * @return  null
     */
    public function getRelatedModel($relatedType, $code)
    {
        $relatedModel = null;

        switch ($relatedType) {
            case Promotion::RELATED_TYPE_BONUS;
                $relatedModel = Bonus::findByCodeFromCache($code);
                break;
        }

        return $relatedModel;
    }

    /**
     * 检查关联模型是否可以报名及其状态
     *
     * @param integer       $relatedType
     * @param $relatedModel
     */
    public function checkRelatedModel($relatedType, User $user, $relatedModel)
    {
        switch ($relatedType) {
            case Promotion::RELATED_TYPE_BONUS;
                # 检查关联模型状态
                BonusRepository::checkIsCanClaim($relatedModel, $user);
                break;
        }
    }
}