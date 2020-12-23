<?php
namespace App\Transformers;

use App\Models\BonusGroup;

/**
 * @OA\Schema(
 *   schema="BonusGroup",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="admin_name", type="string", description="管理员名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 * )
 */
class BonusGroupTransformer extends Transformer
{
    public function transform(BonusGroup $group)
    {
        return [
            'id'            => $group->id,
            'name'          => $group->name,
            'admin_name'    => $group->admin_name,
            'created_at'    => convert_time($group->created_at),
            'updated_at'    => convert_time($group->updated_at),
        ];
    }
}