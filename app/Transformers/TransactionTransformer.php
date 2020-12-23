<?php
namespace App\Transformers;

use App\Models\Model;
use App\Models\Transaction;

/**
 * @OA\Schema(
 *   schema="Transaction",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="trace_id", type="integer", description="追踪id"),
 *   @OA\Property(property="type_group", type="integer", description="类型分组"),
 *   @OA\Property(property="display_type_group", type="string", description="分组显示"),
 *   @OA\Property(property="type", type="integer", description="类型"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="is_income", type="string", description="是否入账"),
 *   @OA\Property(property="display_is_income", type="string", description="是否入账显示"),
 *   @OA\Property(property="amount", type="string", description="帐变金额"),
 *   @OA\Property(property="before_balance", type="string", description="帐变前金额"),
 *   @OA\Property(property="after_balance", type="string", description="帐变后金额"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 *   @OA\Property(property="admin_remark", type="string", description="管理员备注"),
 *   @OA\Property(property="sys_remark", type="string", description="系统备注"),
 *   @OA\Property(property="start_process_at", type="string", description="开始处理时间"),
 *   @OA\Property(property="end_process_at", type="string", description="结束处理时间"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 * )
 */
class TransactionTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    public function transform(Transaction $transaction)
    {
        return [
            'id'                 => $transaction->id,
            'user_id'            => $transaction->user_id,
            'trace_id'           => $transaction->trace_id,
            'order_no'           => $transaction->order_no,
            'type_group'         => $transaction->type_group,
            'display_type_group' => transfer_show_value($transaction->type_group, Transaction::$typeGroups),
            'type'               => $transaction->type,
            'currency'           => $transaction->currency,
            'is_income'          => $transaction->is_income,
            'display_is_income'  => transfer_show_value($transaction->is_income, Transaction::$isIncomes),
            'amount'             => thousands_number($transaction->amount),
            'before_balance'     => thousands_number($transaction->before_balance),
            'after_balance'      => thousands_number($transaction->after_balance),
            'admin_name'         => $transaction->admin_name,
            'admin_remark'       => $transaction->admin_remark,
            'sys_remark'         => $transaction->sys_remark,
            'start_process_at'   => convert_time($transaction->start_process_at),
            'end_process_at'     => convert_time($transaction->end_process_at),
            'status'             => $transaction->status,
            'display_status'     => transfer_show_value($transaction->status, Transaction::$statuses),
            'created_at'         => convert_time($transaction->created_at),
        ];
    }

    public function includeUser(Transaction $transaction)
    {
        return $this->item($transaction->user, new UserTransformer());
    }
}
