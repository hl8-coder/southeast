<?php

namespace App\Transformers;

use App\Models\RiskGroup;
use App\Models\User;
use App\Models\UserBonusPrize;
use App\Models\UserProductDailyReport;
use App\Repositories\UserBonusPrizeRepository;
use App\Repositories\UserRebatePrizeRepository;

/**
 * @OA\Schema(
 *   schema="RebateComputationReport",
 *   type="object",
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="display_risk_group_id", type="string", description="风控组别id"),
 *   @OA\Property(property="account_status", type="string", description="会员状态"),
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="effective_bet", type="number", description="投注流水总额"),
 *   @OA\Property(property="last_rebate_at", type="string", description="最近一笔返点时间"),
 *   @OA\Property(property="bonus_code", type="string", description="红利code"),
 *   @OA\Property(property="user_bonus_prize_count", type="number", description="领取红利达标数额"),
 *   @OA\Property(property="prize_created_at", type="string", description="申请时间"),
 *   @OA\Property(property="is_turnover_closed", type="string", description="人工处理关闭红利"),
 *   @OA\Property(property="turnover_closed_admin_name", type="string", description="操作者"),
 *   @OA\Property(property="turnover_closed_at", type="string", description="关闭时间"),
 *   @OA\Property(property="total_close_value", type="string", description="红利总流水要求"),
 *   @OA\Property(property="start_at", type="string", description="开始时间", format="date"),
 *   @OA\Property(property="end_at", type="string", description="结束时间", format="date"),
 * )
 */
class RebateComputationReportTransformer extends Transformer
{
    public function transform($report)
    {
        # TODO 待优化，这里会产生循环查询，后续要通过优化，只进行一次查询
        // $lastRebate                = UserRebatePrizeRepository::getLatestRebateByUser($report->user_id);

        $userBonusPrizeList        = $this->data['user_bonus_prize_list'];
        $userLatestRebatePrizeList = $this->data['user_latest_rebate_prize_list'];
        $lastRebate                = $userLatestRebatePrizeList->where('user_id', $report->user_id)->first();
        $userBonusPrize            = $userBonusPrizeList->where('id', $report->user_bonus_prize_id)->first();

        return [
            'user_id'                    => $report->user_id,
            'user_name'                  => $report->user->name,
            'display_risk_group_id'      => transfer_show_value($report->user->risk_group_id, RiskGroup::getDropList()),
            'account_status'             => transfer_show_value($report->user->status, User::$statuses),
            'product_code'               => $report->product_code,
            'currency'                   => $report->user->currency,
            'effective_bet'              => thousands_number($report->user_total_bet),
            'last_rebate_at'             => $lastRebate ? convert_time($lastRebate->payment_sent_at) : '',
            'bonus_code'                 => $report->bonus_code,
            'user_bonus_prize_count'     => $userBonusPrize ? thousands_number($userBonusPrize->turnover_closed_value) : '', // 每次获奖要求达到的流水
            'prize_created_at'           => $userBonusPrize ? convert_time($userBonusPrize->created_at) : '',
            'is_turnover_closed'         => $userBonusPrize ? transfer_show_value($userBonusPrize->is_turnover_closed, UserBonusPrize::$booleanDropList) : '',
            'turnover_closed_admin_name' => $userBonusPrize ? $userBonusPrize->turnover_closed_admin_name : '',
            'turnover_closed_at'         => $userBonusPrize ? convert_time($userBonusPrize->turnover_closed_at) : '',
            'total_close_value'          => thousands_number($report->total_turnover_value), // 总流水要求
            'start_at'                   => isset($this->data['start_at']) ? $this->data['start_at'] : '',
            'end_at'                     => isset($this->data['end_at']) ? $this->data['end_at'] : '',
        ];
    }
}
