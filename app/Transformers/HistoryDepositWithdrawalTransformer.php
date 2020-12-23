<?php

namespace App\Transformers;

use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Services\HistoryService;

/**
 * @OA\Schema(
 *   schema="HistoryDepositWithdrawal",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="交易id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="显示状态"),
 *   @OA\Property(property="amount", type="string", description="金额"),
 *   @OA\Property(property="created_at", type="string", description="建立日期"),
 *   @OA\Property(property="is_can_cancel", type="boolean", description="是否可取消")
 * )
 */
class HistoryDepositWithdrawalTransformer extends Transformer
{
    public function transform($data)
    {
        $dropList = (new HistoryService())->getDropList();
        return [
            'id'             => $data->id,
            'order_no'       => $data->order_no,
            'type'           => $dropList['display_type'][$data->type],
            'status'         => $data->status,
            'display_status' => $dropList['display_status'][$data->status],
            'amount'         => thousands_number($data->amount),
            'created_at'     => convert_time($data->created_at),
            'is_can_cancel'  => $data->type == 'withdrawal' && $data->status == '3',
        ];
    }

}
