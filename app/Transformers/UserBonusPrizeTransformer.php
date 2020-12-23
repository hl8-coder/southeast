<?php
namespace App\Transformers;

use App\Models\Bonus;
use App\Models\Model;
use App\Models\UserBonusPrize;

/**
 * @OA\Schema(
 *   schema="UserBonusPrize",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="category", type="string", description="新旧红利"),
 *   @OA\Property(property="display_category", type="string", description="新旧红利显示"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="bonus_prize_id", type="integer", description="红利奖励id"),
 *   @OA\Property(property="bonus_id", type="integer", description="红利id"),
 *   @OA\Property(property="bonus_code", type="string", description="红利code"),
 *   @OA\Property(property="bonus_group_id", type="integer", description="红利组别id"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="deposit_amount", type="number", description="转账金额"),
 *   @OA\Property(property="total_deposit", type="number", description="总充值金额"),
 *   @OA\Property(property="prize", type="integer", description="奖励金额"),
 *   @OA\Property(property="is_max_prize", type="boolean", description="是否达到奖励上限"),
 *   @OA\Property(property="display_is_max_prize", type="string", description="是否达到奖励上限显示"),
 *   @OA\Property(property="turnover_current_value", type="number", description="当前值"),
 *   @OA\Property(property="turnover_closed_value", type="number", description="关闭值"),
 *   @OA\Property(property="is_turnover_closed", type="boolean", description="是否关闭"),
 *   @OA\Property(property="display_is_close", type="string", description="是否关闭显示"),
 *   @OA\Property(property="turnover_closed_at", type="string", description="关闭时间", format="date-time"),
 *   @OA\Property(property="date", type="string", description="归属时间", format="date"),
 *   @OA\Property(property="void", type="boolean", description="是否人工关闭"),
 *   @OA\Property(property="turnover_closed_admin_name", type="string", description="操作管理员"),
 *   @OA\Property(property="remark", type="string", description="关闭理由"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="registered_at", type="string", description="会员注册时间", format="date-time"),
 * )
 */
class UserBonusPrizeTransformer extends Transformer
{
    public function transform(UserBonusPrize $prize)
    {
        $data = [
            'id'                        => $prize->id,
            'user_id'                   => $prize->user_id,
            'category'                  => $prize->category,
            'display_category'          => transfer_show_value($prize->category, Bonus::$categories),
            'user_name'                 => $prize->user_name,
            'bonus_prize_id'            => $prize->bonus_prize_id,
            'bonus_id'                  => $prize->bonus_id,
            'bonus_amount'              => thousands_number($prize->prize),
            'bonus_code'                => $prize->bonus_code,
            'bonus_group_id'            => $prize->bonus_group_id,
            'currency'                  => $prize->currency,
            'product_code'              => $prize->product_code,
            'deposit_amount'            => thousands_number($prize->deposit_amount),
            'prize'                     => thousands_number($prize->prize),
            'is_max_prize'              => $prize->is_max_prize,
            'display_is_max_prize'      => transfer_show_value($prize->is_max_prize, Model::$booleanDropList),
            'turnover_current_value'    => thousands_number($prize->turnover_current_value),
            'turnover_closed_value'     => thousands_number($prize->turnover_closed_value),
            'is_turnover_closed'        => $prize->is_turnover_closed,
            'display_is_turnover_closed'=> transfer_show_value($prize->is_turnover_closed, Model::$booleanDropList),
            'turnover_closed_at'        => convert_time($prize->turnover_closed_at),
            'date'                      => $prize->date,
            'void'                      => !empty($prize->turnover_closed_admin_name) ? 'YES' : '',
            'turnover_closed_admin_name'=> $prize->turnover_closed_admin_name,
            'remark'                    => $prize->remark,
            'status'                    => $prize->status,
            'display_status'            => transfer_show_value($prize->status, UserBonusPrize::$statuses),
            'created_at'                => convert_time($prize->created_at),
            'registered_at'             => convert_time($prize->user->created_at)
        ];

        switch ($this->type) {
            case 'user':
                $isTurnoverAutoClosed = $prize->is_turnover_closed && empty($prize->turnover_closed_admin_name);
                $data['display_is_turnover_closed'] = transfer_show_value($isTurnoverAutoClosed, Model::$booleanDropList);
                break;
            case 'report':
                $data['total_deposit'] = $prize->user->report ? thousands_number($prize->user->report->deposit) : 0;
                break;
        }

        return $data;
    }
}