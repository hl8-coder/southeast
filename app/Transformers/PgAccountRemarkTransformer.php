<?php
namespace App\Transformers;

use App\Models\PgAccountRemark;

/**
 * @OA\Schema(
 *   schema="PgAccountRemark",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="payment_platform_code", type="string", description="第三方支付通道code"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class PgAccountRemarkTransformer extends Transformer
{
    public function transform(PgAccountRemark $remark)
    {
        return [
            'id'                        => $remark->id,
            'payment_platform_code'   => $remark->payment_platform_code,
            'remark'                    => $remark->remark,
            'admin_name'                => $remark->admin_name,
            'created_at'                => convert_time($remark->created_at),
        ];
    }
}