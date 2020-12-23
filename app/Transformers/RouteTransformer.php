<?php

namespace App\Transformers;


use App\Models\Route;

/**
 * @OA\Schema(
 *   schema="Route",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="操作id"),
 *   @OA\Property(property="name", type="string", description="操作名称"),
 *   @OA\Property(property="method", type="string", description="方式"),
 *   @OA\Property(property="action", type="string", description="操作"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="url", type="string", description="请求地址"),
 *   @OA\Property(property="location", type="string", description="位置"),
 *   @OA\Property(property="version", type="string", description="版本"),
 *   @OA\Property(property="created_at", type="string", description="创建时间"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间"),
 * )
 */
class RouteTransformer extends Transformer
{
    public function transform(Route $route)
    {
        return [
            'id'         => $route->id,
            'name'       => $route->name,
            'method'     => $route->method,
            'action'     => $route->action,
            'remark'     => $route->remark,
            'url'        => $route->url,
            'location'   => $route->location,
            'version'    => $route->version,
            'created_at' => convert_time($route->created_at),
            'updated_at' => convert_time($route->updated_at),
        ];
    }

}
