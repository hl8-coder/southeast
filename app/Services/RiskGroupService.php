<?php


namespace App\Services;


use App\Models\Adjustment;
use App\Models\Admin;
use App\Models\ProfileRemark;
use App\Models\RiskGroup;
use App\Models\User;

class RiskGroupService
{

    /**
     * admin 修改 risk group 的时候，触发用户状态被修改
     *
     * @param RiskGroup $riskGroup
     * @param Admin $admin
     * @return array|bool 当返回值是数组的时候，表明有用户的状态已经被修改为 inactive
     *
     * @author  Martin
     * @date    2020/8/18 6:06 上午
     * @version viet-314
     */
    public function batchChangeUserStatusByRiskGroup(RiskGroup $riskGroup, Admin $admin)
    {
        $userStatus = null;
        $newRules   = $riskGroup->rules ?? [];

        if (in_array('user_status_inactive', $newRules)) {
            $userStatus = User::STATUS_INACTIVE;
        }

        if ($userStatus !== null) {
            $userIds = User::query()->where('risk_group_id', $riskGroup->id)
                ->where('status', '<>', $userStatus)
                ->pluck('id')
                ->toArray();

            if ($userIds) {
                User::query()->whereIn('id', $userIds)->update(['status' => $userStatus]);
                # 批量处理，防止该分组用户数量过大导致单次 IO 数据量过大
                collect($userIds)->chunk(50, function ($ids) use($admin){
                    foreach ($ids as $id) {
                        $remark[] = [
                            'user_id'    => $id,
                            'category'   => ProfileRemark::CATEGORY_CHANGE,
                            'remark'     => 'risk group change cause user status change',
                            'admin_name' => $admin->name,
                            'created_at' => now(),
                            'updated_at' => now(),
                        ];
                    }
                    ProfileRemark::query()->insert($remark);
                });

                if ($userStatus == User::STATUS_INACTIVE) {
                    return $userIds;
                }
            }
        }
        return true;
    }

    /**
     * risk group 限制用户某些 adjustment 操作
     *
     * @param User $user
     * @param $category
     * @return bool
     * @throws \Exception
     *
     * @author  Martin
     * @date    2020/8/18 4:18 下午
     * @version viet-314
     */
    public function checkUserCanDoAdjustment(User $user, $category)
    {
        $riskGroup = $user->riskGroup;
        # 部分逻辑发现 risk group 可能为 null
        if (empty($riskGroup)) {
            return true;
        }

        $rules = $riskGroup->rules ?? [];
        $cant  = false;
        switch ($category) {
            case Adjustment::CATEGORY_REBATE:
                $cant = in_array(RiskGroup::RULE_NO_ADJUSTMENT_REBATE, $rules);
                break;
            case Adjustment::CATEGORY_PROMOTION:
                $cant = in_array(RiskGroup::RULE_NO_ADJUSTMENT_PROMOTION, $rules);
                break;
            case Adjustment::CATEGORY_WELCOME_BONUS:
                $cant = in_array(RiskGroup::RULE_NO_ADJUSTMENT_WELCOME_BONUS, $rules);
                break;
            case Adjustment::CATEGORY_RETENTION:
                $cant = in_array(RiskGroup::RULE_NO_ADJUSTMENT_RETENTION, $rules);
                break;
            default:
                $cant = false;
        }

        if ($cant){
            throw new \Exception('The member can not do this adjustment limited by risk group');
        }
        return true;
    }

}
