<?php
namespace App\Transformers;

use App\Models\Deposit;
use App\Models\PaymentPlatform;

/**
 * @OA\Schema(
 *   schema="UserDeposit",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="交易id"),
 *   @OA\Property(property="order_no", type="string", description="交易订单号"),
 *   @OA\Property(property="amount", type="number", description="充值金额"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="payment_type", type="integer", description="支付类型"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class UserDepositTransformer extends Transformer
{
    public function transform(Deposit $deposit)
    {
        return [
            'id'                        => $deposit->id,
            'order_no'                  => $deposit->order_no,
            'amount'                    => $deposit->amount,
            'status'                    => transfer_show_value($deposit->status, Deposit::$statues),
            'payment_type'              => $deposit->payment_type,
            'display_payment_platform'  => $deposit->online_banking_channel ? transfer_show_value($deposit->online_banking_channel, PaymentPlatform::$onlineBankingChannels) : $deposit->paymentPlatform->name,
            'created_at'                => convert_time($deposit->created_at),
        ];
    }
}