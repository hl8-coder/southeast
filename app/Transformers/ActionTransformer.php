<?php

namespace App\Transformers;

use App\Models\Action;

/**
 * @OA\Schema(
 *   schema="Action",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="操作id"),
 *   @OA\Property(property="menu_id", type="integer", description="菜单id"),
 *   @OA\Property(property="name", type="string", description="操作名称"),
 *   @OA\Property(property="method", type="string", description="方式"),
 *   @OA\Property(property="action", type="string", description="操作"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="url", type="string", description="请求地址"),
 *   @OA\Property(property="drop_list_url", type="string", description="下拉菜单地址"),
 *   @OA\Property(property="sort", type="integer", description="排序"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间"),
 * )
 */
class ActionTransformer extends Transformer
{
    public $availableIncludes = ['menu'];
    public function transform(Action $action)
    {
        return [
            'id'            => $action->id,
            'menu_id'       => $action->menu_id,
            'name'          => $action->name,
            'method'        => $action->method,
            'action'        => $action->action,
            'remark'        => $action->remark,
            'url'           => $action->url,
            'drop_list_url' => $action->drop_list_url,
            'sort'          => $action->sort,
            'created_at'    => convert_time($action->created_at),
            'updated_at'    => convert_time($action->updated_at),
        ];
    }

    public function includeMenu(Action $action)
    {
        return $this->item($action->menu, New MenuTransformer());
    }

}
