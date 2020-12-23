<?php
namespace App\Transformers;

use App\Models\Promotion;
use App\Models\PromotionClaimUser;

/**
 * @OA\Schema(
 *   schema="PromotionClaimUser",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="promotion_id", type="integer", description="优惠id"),
 *   @OA\Property(property="promotion_code", type="string", description="优惠code"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="related_type", type="integer", description="关联类型"),
 *   @OA\Property(property="display_related_type", type="string", description="关联类型显示"),
 *   @OA\Property(property="related_id", type="integer", description="关联id"),
 *   @OA\Property(property="related_code", type="integer", description="关联code"),
 *   @OA\Property(property="is_verified", type="boolean", description="是否需要审核"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="integer", description="状态显示"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="front_remark", type="string", description="前端备注"),
 *   @OA\Property(property="remark", type="string", description="后端备注"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 *   @OA\Property(property="user", ref="#/components/schemas/User"),
 * )
 */
class PromotionClaimUserTransformer extends Transformer
{
    protected $availableIncludes = ['user'];

    public function transform(PromotionClaimUser $promotionClaimUser)
    {
        return [
            'id'                    => $promotionClaimUser->id,
            'promotion_id'          => $promotionClaimUser->promotion_id,
            'promotion_code'        => $promotionClaimUser->promotion_code,
            'user_id'               => $promotionClaimUser->user_id,
            'user_name'             => $promotionClaimUser->user_name,
            'related_type'          => $promotionClaimUser->related_type,
            'display_related_type'  => transfer_show_value($promotionClaimUser->related_type, Promotion::$relatedTypes),
            'related_id'            => $promotionClaimUser->related_id,
            'related_code'          => $promotionClaimUser->related_code,
            'is_verified'           => $promotionClaimUser->promotion ? $promotionClaimUser->promotion->is_verified : false,
            'admin_name'            => $promotionClaimUser->admin_name,
            'status'                => $promotionClaimUser->status,
            'display_status'        => transfer_show_value($promotionClaimUser->status, PromotionClaimUser::$statuses),
            'front_remark'          => $promotionClaimUser->front_remark,
            'remark'                => $promotionClaimUser->remark,
            'created_at'            => convert_time($promotionClaimUser->created_at),
            'updated_at'            => convert_time($promotionClaimUser->updated_at),
        ];
    }

    public function includeUser(PromotionClaimUser $promotionClaimUser)
    {
        return $this->item($promotionClaimUser->user, new UserTransformer());
    }
}