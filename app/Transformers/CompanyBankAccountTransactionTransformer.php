<?php
namespace App\Transformers;

use App\Models\CompanyBankAccountTransaction;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="CompanyBankAccountTransaction",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="company_bank_account_code", type="string", description="公司银行卡code"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="is_income", type="boolean", description="是否是入账 true:入账 false:出账"),
 *   @OA\Property(property="debit", type="number", description="出账金额"),
 *   @OA\Property(property="credit", type="number", description="入账金额"),
 *   @OA\Property(property="from_account", type="string", description="来源银行卡账号"),
 *   @OA\Property(property="to_account", type="string", description="去向银行卡账号"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="total_amount", type="number", description="总金额"),
 *   @OA\Property(property="amount", type="number", description="金额"),
 *   @OA\Property(property="fee", type="number", description="手续费"),
 *   @OA\Property(property="after_balance", type="number", description="帐变后金额"),
 *   @OA\Property(property="trace_id", type="integer", description="追踪id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="admin_name", type="string", description="操作人员姓名"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="reason", type="integer", description="理由"),
 *   @OA\Property(property="display_reason", type="string", description="理由"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class CompanyBankAccountTransactionTransformer extends Transformer
{
    public function transform(CompanyBankAccountTransaction $transaction)
    {
        $data = [
            'id'                        => $transaction->id,
            'company_bank_account_code' => $transaction->company_bank_account_code,
            'type'                      => transfer_show_value($transaction->type, CompanyBankAccountTransaction::$types),
            'is_income'                 => transfer_show_value($transaction->is_income, Model::$booleanDropList),
            'debit'                     => $transaction->is_income ? 0 : $transaction->amount,
            'credit'                    => $transaction->is_income ? $transaction->amount : 0,
            'from_account'              => $transaction->from_account,
            'to_account'                => $transaction->to_account,
            'user_name'                 => $transaction->user_name,
            'total_amount'              => $transaction->total_amount,
            'amount'                    => $transaction->amount,
            'fee'                       => $transaction->fee,
            'after_balance'             => $transaction->after_balance,
            'trace_id'                  => $transaction->trace_id,
            'order_no'                  => $transaction->order_no,
            'admin_name'                => $transaction->admin_name,
            'remark'                    => $transaction->remark,
            'reason'                    => $transaction->reason,
            'display_reason'            => transfer_show_value($transaction->reason, CompanyBankAccountTransaction::$reasons),
            'created_at'                => convert_time($transaction->created_at),
        ];

        switch ($this->type) {
            case 'index':
                if ('VND' == $transaction->account->currency) {
                    $data['debit'] = thousands_number($data['debit'], 3);
                    $data['credit'] = thousands_number($data['credit'], 3);
                    $data['total_amount'] = thousands_number($data['total_amount'], 3);
                    $data['amount'] = thousands_number($data['amount'], 3);
                    $data['fee'] = thousands_number($data['fee'], 3);
                    $data['after_balance'] = thousands_number($data['after_balance'], 3);
                }
                break;
        }

        return $data;
    }
}