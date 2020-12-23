<?php


namespace App\Transformers;


use App\Models\TransferDetail;
use App\Models\User;

/**
 * @OA\Schema(
 *   schema="TransferDetail",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="user_id", type="integer", description="转账者ID"),
 *   @OA\Property(property="user_name", type="string", description="转账者"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="to_user_id", type="string", description="收款者ID"),
 *   @OA\Property(property="to_user_name", type="string", description="收款者"),
 *   @OA\Property(property="status", type="integer", description="订单状态"),
 *   @OA\Property(property="display_status", type="string", description="订单状态显示"),
 *   @OA\Property(property="is_agent", type="integer", description="是否是代理"),
 *   @OA\Property(property="display_is_agent", type="string", description="显示"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="amount", type="string", description="金额"),
 *   @OA\Property(property="from_before_balance", type="string", description="转账前的金额"),
 *   @OA\Property(property="from_after_balance", type="string", description="转账后的金额"),
 *   @OA\Property(property="created_at", type="integer", description="建立日期"),
 * )
 */
class TransferDetailTransformer extends Transformer
{
    public function transform(TransferDetail $detail)
    {
        $data = [
            'id'                    => $detail->id,
            'order_no'              => $detail->order_no,
            'user_id'               => $detail->user_id,
            'user_name'             => $detail->user_name,
            'currency'              => $detail->user->currency,
            'to_user_id'            => $detail->to_user_id,
            'to_user_name'          => $detail->to_user_name,
            'to_user_name_is_agent' => $detail->toUser->is_agent,
            'status'                => $detail->status,
            'display_status'        => transfer_show_value($detail->status, TransferDetail::$statuses),
            'remark'                => $detail->remark,
            'code'                  => $detail->user->affiliate_code,
            'amount'                => thousands_number($detail->amount),
            'is_agent'              => (int)$detail->toUser->is_agent,
            'display_is_agent'      => transfer_show_value($detail->toUser->is_agent, User::$agent),
            'created_at'            => convert_time($detail->created_at),
            'from_before_balance'   => thousands_number($detail->from_before_balance),
            'from_after_balance'    => thousands_number($detail->from_after_balance),
        ];
        switch ($this->type) {
            case 'affiliate_fund_managements':
                $data['to_user_name'] = hidden_name($data['to_user_name']);
                break;
        }
        return $data;
    }
}