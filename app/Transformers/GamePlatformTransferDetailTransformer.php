<?php
namespace App\Transformers;

use App\Models\GamePlatformTransferDetail;

/**
 * @OA\Schema(
 *   schema="GamePlatformTransferDetail",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="第三方转账明细id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="user_ip", type="string", description="会员ip"),
 *   @OA\Property(property="platform_id", type="integer", description="平台id"),
 *   @OA\Property(property="from", type="string", description="转出钱包"),
 *   @OA\Property(property="to", type="string", description="转入钱包"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="platform_order_no", type="string", description="平台订单号"),
 *   @OA\Property(property="type", type="integer", description="转账类型"),
 *   @OA\Property(property="user_currency", type="string", description="会员币别"),
 *   @OA\Property(property="platform_currency", type="string", description="平台币别"),
 *   @OA\Property(property="amount", type="number", description="转账金额"),
 *   @OA\Property(property="conversion_amount", type="number", description="转换后金额"),
 *   @OA\Property(property="from_before_balance", type="number", description="出账账户转账前金额"),
 *   @OA\Property(property="from_after_balance", type="number", description="出账账户转账后金额"),
 *   @OA\Property(property="to_before_balance", type="number", description="入账账户转账前金额"),
 *   @OA\Property(property="to_after_balance", type="number", description="入账账户转账后金额"),
 *   @OA\Property(property="bet_order_id", type="string", description="投注id"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="is_show_manual_button", type="boolean", description="是否显示审核按钮"),
 *   @OA\Property(property="is_show_check_button", type="boolean", description="是否添加审核队列按钮"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class GamePlatformTransferDetailTransformer extends Transformer
{
    public function transform(GamePlatformTransferDetail $detail)
    {
        return [
            'id'                    => $detail->id,
            'user_id'               => $detail->user_id,
            'user_name'             => $detail->user_name,
            'currency'              => $detail->user->currency,
            'user_ip'               => $detail->user_ip,
            'platform_id'           => $detail->platform_id,
            'from'                  => $detail->from,
            'to'                    => $detail->to,
            'order_no'              => $detail->order_no,
            'platform_order_no'     => $detail->platform_order_no,
            'type'                  => $detail->type,
            'user_currency'         => $detail->user_currency,
            'platform_currency'     => $detail->platform_currency,
            'amount'                => thousands_number($detail->amount),
            'conversion_amount'     => thousands_number($detail->conversion_amount),
            'from_before_balance'   => thousands_number($detail->from_before_balance),
            'from_after_balance'    => thousands_number($detail->from_after_balance),
            'to_before_balance'     => thousands_number($detail->to_before_balance),
            'to_after_balance'      => thousands_number($detail->to_after_balance),
            'remark'                => $detail->remark,
            'status'                => transfer_show_value($detail->status, GamePlatformTransferDetail::$statues),
            'admin_name'            => $detail->admin_name,
            'is_show_manual_button' => $detail->isWaitingConfirm(),
            'is_show_check_button'  => $detail->isChecking() && (now()->diffInMinutes($detail->created_at, true) > 5),
            'created_at'            => convert_time($detail->created_at),
        ];
    }
}