<?php
namespace App\Transformers;

use App\Models\AffiliateRemark;

/**
 * @OA\Schema(
 *   schema="AffiliateRemark",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="reason", type="string", description="原因"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="created_at", type="string", description="建立时间"),
 * )
 */
class AffiliateRemarkTransformer extends Transformer
{
    public function transform(AffiliateRemark $affiliateRemark)
    {
        return [
            'id'            => $affiliateRemark->id,
            'admin_name'    => $affiliateRemark->admin_name,
            'reason'        => $affiliateRemark->reason,
            'remark'        => $affiliateRemark->remark,
            'created_at'    => convert_time($affiliateRemark->created_at),
        ];
    }
}