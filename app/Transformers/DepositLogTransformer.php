<?php

namespace App\Transformers;

use App\Models\DepositLog;

/**
 * @OA\Schema(
 *   schema="DepositLog",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="充值Log id"),
 *   @OA\Property(property="deposit_id", type="integer", description="充值id"),
 *   @OA\Property(property="deposit_order_no", type="string", description="充值订单号"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 *   @OA\Property(property="bank_transaction_id", type="integer", description="银行交易id"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="interval", type="integer", description="操作间隔时间(s)"),
 * )
 */
class DepositLogTransformer extends Transformer
{
    protected $availableIncludes = ['deposit'];

    public function transform(DepositLog $depositLog)
    {
        return [
            'id'                  => $depositLog->id,
            'type'                => transfer_show_value($depositLog->type, DepositLog::$types),
            'admin_name'          => $depositLog->admin_name,
            'deposit_id'          => $depositLog->deposit_id,
            'reason'              => $depositLog->reason,
            'deposit_order_no'    => $depositLog->deposit->order_no,
            'bank_transaction_id' => $depositLog->bank_transaction_id,
            'created_at'          => convert_time($depositLog->created_at),
            'interval'            => $depositLog->interval,
        ];
    }
}
