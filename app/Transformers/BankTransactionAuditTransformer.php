<?php
namespace App\Transformers;

use App\Models\Deposit;
use OwenIt\Auditing\Models\Audit;

/**
 * @OA\Schema(
 *   schema="BankTransactionAudit",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="user_type", type="integer", description="用户类型(会员/管理员)"),
 *   @OA\Property(property="user_id", type="integer", description="用户id"),
 *   @OA\Property(property="old_value", type="string", description="旧值"),
 *   @OA\Property(property="new_value", type="string", description="新值"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class BankTransactionAuditTransformer extends Transformer
{
    public function transform(Audit $audit)
    {
        $tags = explode(',', $audit->tags);

        $type = '';
        $depositOrderNo = '';
        if (in_array('deposit_id', $tags)) {
            if (!empty($audit->old_values['deposit_id'])) {
                $depositId = $audit->old_values['deposit_id'];
                $type = 'UNMATCH TO';
            } else {
                $depositId = $audit->new_values['deposit_id'];
                $type = 'MATCH TO';
            }
            if ($deposit = Deposit::find($depositId)) {
                $depositOrderNo = $deposit->order_no;
            }
        } elseif (in_array('deleted_at', $tags)) {
            $type = 'HOUSEKEEP';
        } elseif (in_array('amount', $tags)) {
            $type = 'MODIFY';
        }

        return [
            'id'                => $audit->id,
            'admin_name'        => empty($audit->user) ? '' : $audit->user->name,
            'type'              => $type,
            'deposit_order_no'  => $depositOrderNo,
            'created_at'        => convert_time($audit->created_at),
        ];
    }
}
