<?php

namespace App\Transformers;

use App\Models\Adjustment;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="Adjustment",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="order_no", type="string", description="订单号"),
 *   @OA\Property(property="transaction_id", type="string", description="交易ID"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="type", type="string", description="调整类型"),
 *   @OA\Property(property="category", type="string", description="分类"),
 *   @OA\Property(property="platform_code", type="string", description="第三方平台"),
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="related_order_no", type="string", description="关联订单号[目前只关联adjustment]"),
 *   @OA\Property(property="amount", type="number", description="调整金额"),
 *   @OA\Property(property="status", type="string", description="状态"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="created_admin_name", type="string", description="创建管理员"),
 *   @OA\Property(property="verified_admin_name", type="string", description="审核管理员"),
 *   @OA\Property(property="verified_at", type="string", description="审核时间", format="date-time"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="reason", type="string", description="理由"),
 *   @OA\Property(property="is_can_close", type="boolean", description="是否可以关闭"),
 *   @OA\Property(property="is_turnover_closed", type="boolean", description="流水限制是否关闭"),
 *   @OA\Property(property="display_is_turnover_closed", type="string", description="流水限制是否关闭显示"),
 *   @OA\Property(property="turnover_closed_value", type="string", description="所需流水总数"),
 *   @OA\Property(property="turnover_current_value", type="string", description="当前流水数值"),
 *   @OA\Property(property="turnover_closed_at", type="string", description="关闭时间"),
 *   @OA\Property(property="turnover_closed_admin_name", type="string", description="关闭管理员"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class AdjustmentTransformer extends Transformer
{
    public function transform(Adjustment $adjustment)
    {
        # 转化显示系统审核adjustment
        $verifiedAdminName = $adjustment->verified_admin_name;
        $verifiedAt        = $adjustment->verified_at;
        if (in_array($adjustment->status, Adjustment::$showSystemStatuses) && empty($adjustment->verified_admin_name)) {
            $verifiedAdminName = 'System';
            $verifiedAt        = $adjustment->created_at;
        }

        return [
            'id'                         => $adjustment->id,
            'order_no'                   => $adjustment->order_no,
            'transaction_id'             => $adjustment->transaction_id,
            'user_id'                    => $adjustment->user_id,
            'user_name'                  => $adjustment->user_name,
            'currency'                   => $adjustment->user->currency,
            'type'                       => transfer_show_value($adjustment->type, Adjustment::$types),
            'category'                   => transfer_show_value($adjustment->category, Adjustment::$categories),
            'platform_code'              => $adjustment->platform_code,
            'product_code'               => $adjustment->product_code,
            'related_order_no'           => $adjustment->related_order_no,
            'amount'                     => thousands_number($adjustment->amount),
            'status'                     => transfer_show_value($adjustment->status, Adjustment::$statuses),
            'created_admin_name'         => $adjustment->created_admin_name,
            'verified_admin_name'        => $verifiedAdminName,
            'verified_at'                => convert_time($verifiedAt),
            'remark'                     => $adjustment->remark,
            'reason'                     => $adjustment->reason,
            'is_can_close'               => !$adjustment->is_turnover_closed,
            'is_turnover_closed'         => $adjustment->is_turnover_closed,
            'display_is_turnover_closed' => transfer_show_value($adjustment->is_turnover_closed, Model::$booleanDropList),
            'turnover_closed_value'      => thousands_number($adjustment->turnover_closed_value),
            'turnover_current_value'     => thousands_number($adjustment->turnover_current_value),
            'turnover_closed_at'         => convert_time($adjustment->turnover_closed_at),
            'turnover_closed_admin_name' => $adjustment->turnover_closed_admin_name,
            'updated_at'                 => convert_time($adjustment->updated_at),
            'created_at'                 => convert_time($adjustment->created_at),
        ];
    }
}
