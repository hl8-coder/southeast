<?php

namespace App\Transformers;

use App\Models\CompanyBankAccountTransaction;
use App\Models\Model;
use App\Models\PgAccountTransaction;

/**
 * @OA\Schema(
 *   schema="PgAccountTransaction",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="payment_platform_code", type="string", description="第三方支付通道code"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="is_income", type="boolean", description="是否是入账 true:入账 false:出账"),
 *   @OA\Property(property="debit", type="number", description="出账金额"),
 *   @OA\Property(property="credit", type="number", description="入账金额"),
 *   @OA\Property(property="from_account", type="string", description="来源账户"),
 *   @OA\Property(property="to_account", type="string", description="去向账户"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="total_amount", type="number", description="总金额"),
 *   @OA\Property(property="amount", type="number", description="金额"),
 *   @OA\Property(property="fee", type="number", description="手续费"),
 *   @OA\Property(property="after_balance", type="number", description="帐变后金额"),
 *   @OA\Property(property="trace_id", type="integer", description="追踪id"),
 *   @OA\Property(property="admin_name", type="string", description="操作人员姓名"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class PgAccountTransactionTransformer extends Transformer
{
    public function transform(PgAccountTransaction $transaction)
    {
        $data = [
            'id'                    => $transaction->trace_id ? $transaction->trace_id : '',
            'payment_platform_code' => $transaction->payment_platform_code,
            'type'                  => transfer_show_value($transaction->type, PgAccountTransaction::$types),
            'is_income'             => transfer_show_value($transaction->is_income, Model::$booleanDropList),
            'debit'                 => $transaction->is_income ? 0 : thousands_number($transaction->amount),
            'credit'                => $transaction->is_income ? thousands_number($transaction->amount) : 0,
            'from_account'          => $transaction->from_account,
            'to_account'            => $transaction->to_account,
            'user_name'             => $transaction->user_name,
            'total_amount'          => thousands_number($transaction->total_amount),
            'amount'                => thousands_number($transaction->amount),
            'fee'                   => thousands_number($transaction->fee),
            'after_balance'         => thousands_number($transaction->after_balance),
            'trace_id'              => $transaction->trace_id,
            'admin_name'            => $transaction->admin_name,
            'remark'                => $transaction->remark,
            'created_at'            => convert_time($transaction->created_at),
        ];
        switch ($this->type) {
            case 'backstage_index':
                # 部分上分用户，在全额上分后，只显示 申请上分总数 - 部分上分 之后的金额， user 要求 显示全部
                # 已经与 user 核对，流水方面没有问题，但是上分与流水的逻辑耦合过大，出于稳定对需求，仅对显示部分
                # 作出显示性修正，不对逻辑和原始数据作出修改，*** 仅修改显示数据 ***
                $deposit = $transaction->deposit;
                if ($transaction->is_income && $deposit && $deposit->is_partial == true && $deposit->button_flow_code == '1.2.2.2.2.1') {
                    $data['credit'] = thousands_number($deposit->amount);
                }
        }
        return $data;
    }
}
