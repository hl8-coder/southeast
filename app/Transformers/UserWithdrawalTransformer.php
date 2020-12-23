<?php
namespace App\Transformers;

use App\Models\Withdrawal;

/**
 * @OA\Schema(
 *   schema="UserWithdrawal",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="提现id"),
 *   @OA\Property(property="order_no", type="integer", description="订单号"),
 *   @OA\Property(property="amount", type="number", description="提现金额"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class UserWithdrawalTransformer extends Transformer
{
    public function transform(Withdrawal $withdrawal)
    {
        return [
            'id'                        => $withdrawal->id,
            'order_no'                  => $withdrawal->order_no,
            'amount'                    => thousands_number($withdrawal->amount),
            'status'                    => $withdrawal->status,
            'display_status'            => transfer_show_value($withdrawal->status, Withdrawal::$statuses),
            'created_at'                => convert_time($withdrawal->created_at),
        ];
    }
}