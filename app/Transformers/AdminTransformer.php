<?php

namespace App\Transformers;

use App\Models\Admin;

/**
 * @OA\Schema(
 *   schema="Admin",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="name", type="string", description="账号"),
 *   @OA\Property(property="nick_name", type="string", description="昵称"),
 *   @OA\Property(property="avatar", type="string", description="头像"),
 *   @OA\Property(property="language", type="string", description="语言"),
 *   @OA\Property(property="description", type="string", description="描述"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date"),
 *   @OA\Property(property="updated_at", type="string", description="修改时间", format="date"),
 *   @OA\Property(property="roles", type="array", @OA\Items(ref="#/components/schemas/AdminRole"), description="角色"),
 * )
 */
class AdminTransformer extends Transformer
{
    protected $availableIncludes = ['roles'];

    public function transform(Admin $admin)
    {
        return [
            'id'             => $admin->id,
            'name'           => $admin->name,
            'nick_name'      => $admin->nick_name,
            'avatar'         => $admin->avatar,
            'language'       => $admin->language,
            'status'         => Admin::$statuses[$admin->status],
            'is_super_admin' => $admin->is_super_admin,
            'sort'           => $admin->sort,
            'description'    => $admin->description,
            'created_at'     => convert_time($admin->created_at),
            'updated_at'     => convert_time($admin->updated_at),
        ];

    }

    public function includeRoles(Admin $admin)
    {
        return $this->collection($admin->roles, new AdminRoleTransformer());
    }
}
