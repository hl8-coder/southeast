<?php

namespace App\Transformers;

use App\Models\AffiliateSubTransferBalance;

/**
 * @OA\Schema(
 *   schema="AffiliateSubTransfer",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="code", type="integer", description="代码"),
 *   @OA\Property(property="to_user", type="string", description="收款者"),
 *   @OA\Property(property="amount", type="string", description="金额"),
 *   @OA\Property(property="is_agent", type="string", description="是否代理"),
 *   @OA\Property(property="balance_before", type="string", description="转账前"),
 *   @OA\Property(property="balance_after", type="string", description="转账后"),
 *   @OA\Property(property="created_at", type="integer", description="建立日期"),
 * )
 */
class AffiliateSubTransferBalanceTransformer extends Transformer
{
    public function transform(AffiliateSubTransferBalance $transfer)
    {
        $code = $transfer->affiliate->code . " - " . $transfer->affiliate->user->name;

        return [
            'id'             => $transfer->id,
            'code'           => $code,
            'to_user'        => $transfer->toUser->name,
            'amount'         => thousands_number($transfer->amount),
            'is_agent'       => (int)$transfer->toUser->is_agent,
            'created_at'     => convert_time($transfer->created_at),
            'balance_before' => $transfer->balance_before,
            'balance_after'  => $transfer->balance_after,
        ];
    }

}