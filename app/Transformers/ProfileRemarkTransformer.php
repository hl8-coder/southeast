<?php
namespace App\Transformers;

use App\Models\ProfileRemark;

/**
 * @OA\Schema(
 *   schema="ProfileRemark",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="会员id"),
 *   @OA\Property(property="category", type="integer", description="分类"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="admin_name", type="string", description="管理员姓名"),
 *   @OA\Property(property="created_at", type="boolean", description="创建时间"),
 * )
 */
class ProfileRemarkTransformer extends Transformer
{
    public function transform(ProfileRemark $profileRemark)
    {
        return [
            'id'            => $profileRemark->id,
            'user_id'       => $profileRemark->user_id,
            'category'      => transfer_show_value($profileRemark->category, ProfileRemark::$categories),
            'remark'        => $profileRemark->remark,
            'admin_name'    => $profileRemark->admin_name,
            'created_at'    => convert_time($profileRemark->created_at),
        ];
    }
}
