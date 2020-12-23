<?php

namespace App\Transformers;

use App\Models\AdminRole;

/**
 * @OA\Schema(
 *   schema="AdminRole",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="角色id"),
 *   @OA\Property(property="name", type="string", description="角色"),
 *   @OA\Property(property="description", type="string", description="描述"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="status", type="boolean", description="状态"),
 * )
 */
class AdminRoleTransformer extends Transformer
{
    public $availableIncludes = ['actions'];

    public function transform(AdminRole $adminRole)
    {
        return [
            'id'          => $adminRole->id,
            'name'        => $adminRole->name,
            'description' => $adminRole->description,
            'sort'        => $adminRole->sort,
            'status'      => $adminRole->status,
        ];
    }

    public function includeActions(AdminRole $adminRole)
    {
        return $this->collection($adminRole->actions, new ActionTransformer());
    }
}
