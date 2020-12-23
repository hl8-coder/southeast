<?php
namespace App\Transformers;

use App\Models\Model;
use App\Models\RiskGroup;
use App\Models\UserRebatePrize;
use App\Models\Vip;

/**
 * @OA\Schema(
 *   schema="UserRebatePrize",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="risk_group_id", type="integer", description="会员风控等级"),
 *   @OA\Property(property="display_risk_group_id", type="integer", description="会员风控等级显示"),
 *   @OA\Property(property="vip_id", type="integer", description="会员vip等级"),
 *   @OA\Property(property="display_vip_id", type="integer", description="会员vip等级显示"),
 *   @OA\Property(property="rebate_code", type="string", description="返利code"),
 *   @OA\Property(property="report_id", type="integer", description="报表id"),
 *   @OA\Property(property="effective_bet", type="number", description="有效流水"),
 *   @OA\Property(property="close_bonus_bet", type="number", description="关闭红利流水"),
 *   @OA\Property(property="calculate_rebate_bet", type="number", description="计算返点流水"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="multipiler", type="number", description="计算数值(%)"),
 *   @OA\Property(property="prize", type="number", description="奖励"),
 *   @OA\Property(property="is_max_prize", type="boolean", description="是否最大奖励"),
 *   @OA\Property(property="is_manual_send", type="boolean", description="是否手动派发"),
 *   @OA\Property(property="date", type="string", description="所属日期", format="date"),
 *   @OA\Property(property="marketing_admin_name", type="string", description="Marketing派发管理员"),
 *   @OA\Property(property="marketing_sent_at", type="string", description="Marketing派发时间", format="date-time"),
 *   @OA\Property(property="payment_admin_name", type="string", description="Payment派发管理员"),
 *   @OA\Property(property="payment_sent_at", type="string", description="Payment派发时间", format="date-time"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 * )
 */
class UserRebatePrizeTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    public function transform(UserRebatePrize $prize)
    {
        $data = [
            'id'                    => $prize->id,
            'user_id'               => $prize->user_id,
            'user_name'             => $prize->user_name,
            'risk_group_id'         => $prize->risk_group_id,
            'display_risk_group_id' => transfer_show_value($prize->risk_group_id, RiskGroup::getDropList()),
            'vip_id'                => $prize->vip_id,
            'display_vip_id'        => transfer_show_value($prize->vip_id, Vip::getDropList()),
            'rebate_code'           => $prize->rebate_code,
            'report_id'             => $prize->report_id,
            'effective_bet'         => thousands_number($prize->effective_bet),
            'close_bonus_bet'       => thousands_number($prize->close_bonus_bet),
            'calculate_rebate_bet'  => thousands_number($prize->calculate_rebate_bet),
            'currency'              => $prize->currency,
            'product_code'          => $prize->product_code,
            'multipiler'            => thousands_number($prize->multipiler),
            'prize'                 => thousands_number($prize->prize),
            'is_max_prize'          => transfer_show_value($prize->is_max_prize, Model::$booleanDropList),
            'is_manual_send'        => transfer_show_value($prize->is_manual_send, Model::$booleanDropList),
            'date'                  => $prize->date,
            'marketing_admin_name'  => $prize->marketing_admin_name,
            'marketing_sent_at'     => convert_time($prize->marketing_sent_at),
            'payment_admin_name'    => $prize->payment_admin_name,
            'payment_sent_at'       => convert_time($prize->payment_sent_at),
            'status'                => $prize->status,
            'display_status'        => transfer_show_value($prize->status, UserRebatePrize::$statuses),
            'created_at'            => convert_time($prize->created_at),
            'updated_at'            => convert_time($prize->updated_at),
        ];

        switch ($this->type) {
            case 'marketing':
                $data['display_status'] = $prize->isWaitingMarketSend() ? 'No' : 'Yes';
                break;

            case 'payment':
                $data['display_status'] = $prize->isWaitingPaymentSend() ? 'No' : 'Yes';
                break;
        }

        return $data;
    }

    public function includeUser(UserRebatePrize $prize)
    {
        return $this->item($prize->user, new UserTransformer());
    }
}