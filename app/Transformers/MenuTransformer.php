<?php

namespace App\Transformers;

use App\Models\Menu;
use App\Models\Model;

/**
 * @OA\Schema(
 *   schema="Menu",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="菜单d"),
 *   @OA\Property(property="name", type="string", description="菜单名称"),
 *   @OA\Property(property="parent_id", type="integer", description="上级菜单"),
 *   @OA\Property(property="path", type="string", description="地址"),
 *   @OA\Property(property="is_show", type="boolean", description="shi"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="description", type="string", description="描述"),
 *   @OA\Property(property="children", type="array", @OA\Items(ref="#/components/schemas/Menu"), description="子菜单"),
 * )
 */
class MenuTransformer extends Transformer
{
    protected $availableIncludes = ['children'];

    public function transform(Menu $menu)
    {
        return [
            'id'          => $menu->id,
            'name'        => $menu->name,
            'parent_id'   => $menu->parent_id,
            'path'        => $menu->path,
            'sort'        => $menu->sort,
            'description' => $menu->description,
            'is_show'     => transfer_show_value($menu->is_show, Model::$booleanDropList),
        ];
    }

    public function includeChildren(Menu $menu)
    {
        return $this->collection($menu->children, new self());
    }

}
