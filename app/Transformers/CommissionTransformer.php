<?php

namespace App\Transformers;

use App\Models\Commission;

/**
 * @OA\Schema(
 *   schema="Commission",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_id", type="integer", description="代理id"),
 *   @OA\Property(property="tier", type="integer", description="等级"),
 *   @OA\Property(property="active_number", type="integer", description="活跃人数"),
 *   @OA\Property(property="profit", type="number", description="盈亏"),
 *   @OA\Property(property="rate", type="integer", description="分红比例(%)"),
 *   @OA\Property(property="admin_name", type="string", description="管理员"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 *   @OA\Property(property="updated_at", type="string", description="更新时间", format="date-time"),
 * )
 */
class CommissionTransformer extends Transformer {

    public function transform(Commission $commission) {
        return [
            'id'            => $commission->id,
            'user_id'       => $commission->user_id,
            'tier'          => $commission->tier,
            'active_number' => $commission->active_number,
            'profit'        => $commission->profit,
            'rate'          => $commission->rate,
            'admin_name'    => $commission->admin_name,
            'created_at'    => convert_time($commission->created_at),
            'updated_at'    => convert_time($commission->updated_at),
        ];
    }
}