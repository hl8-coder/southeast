<?php
namespace App\Transformers;

use App\Models\BankTransaction;

/**
 * @OA\Schema(
 *   schema="BankTransaction",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="transaction_id", type="string", description="充值订单号"),
 *   @OA\Property(property="fund_in_account", type="string", description="公司银行卡辨识码"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="account_no", type="string", description="转账账户"),
 *   @OA\Property(property="bank_reference", type="string", description="银行参考"),
 *   @OA\Property(property="transfer_details", type="string", description="交易详情"),
 *   @OA\Property(property="description", type="string", description="描述"),
 *   @OA\Property(property="debit", type="number", description="取款金额"),
 *   @OA\Property(property="credit", type="number", description="存款金额"),
 *   @OA\Property(property="amount", type="number", description="固定存款金额"),
 *   @OA\Property(property="balance", type="number", description="余额"),
 *   @OA\Property(property="channel", type="string", description="通道"),
 *   @OA\Property(property="transaction_date", type="string", description="交易日期", format="date"),
 *   @OA\Property(property="transaction_at", type="string", description="交易详细时间", format="date-time"),
 *   @OA\Property(property="location", type="string", description="本地信息"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="deposit_id", type="integer", description="被领取的充值id"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="is_show_edit_button", type="boolean", description="是否显示编辑按钮"),
 *   @OA\Property(property="is_show_modify_button", type="boolean", description="是否显示modify按钮"),
 *   @OA\Property(property="is_can_modify", type="boolean", description="是否可编辑"),
 * )
 */
class BankTransactionTransformer extends Transformer
{
    public function transform(BankTransaction $bankTransaction)
    {
        return [
            'id'                        => $bankTransaction->id,
            'order_no'                  => $bankTransaction->order_no,
            'transaction_id'            => $bankTransaction->transaction_id,
            'fund_in_account'           => $bankTransaction->fund_in_account,
            'currency'                  => $bankTransaction->currency,
            'account_no'                => $bankTransaction->account_no,
            'bank_reference'            => $bankTransaction->bank_reference,
            'transfer_details'          => $bankTransaction->transfer_details,
            'description'               => $bankTransaction->description,
            'debit'                     => thousands_number($bankTransaction->debit),
            'credit'                    => thousands_number($bankTransaction->credit),
            'amount'                    => thousands_number($bankTransaction->amount),
            'balance'                   => thousands_number($bankTransaction->balance),
            'channel'                   => $bankTransaction->channel,
            'transaction_date'          => $bankTransaction->transaction_date,
            'transaction_at'            => $bankTransaction->transaction_at ? $bankTransaction->transaction_at->format('H:i:s') : '',
            'location'                  => $bankTransaction->location,
            'status'                    => transfer_show_value($bankTransaction->status, BankTransaction::$statuses),
            'admin_name'                => $bankTransaction->admin_name,
            'deposit_id'                => $bankTransaction->deposit_id,
            'remark'                    => $bankTransaction->remark,
            'is_show_edit_button'       => empty($bankTransaction->debit),
            'is_show_modify_button'     => !$bankTransaction->isDeleted(),
            'is_can_modify'             => !$bankTransaction->isDeleted(),
        ];
    }
}