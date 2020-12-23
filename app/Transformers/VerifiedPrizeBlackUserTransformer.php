<?php

namespace App\Transformers;

use App\Models\VerifiedPrizeBlackUser;
use App\Transformers\Transformer;


/**
 * @OA\Schema(
 *   schema="VerifiedPrizeBlackUser",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="user_id", type="integer", description="用户ID"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="user_name", type="string", description="用户名称"),
 *   @OA\Property(property="add_by_admin_id", type="integer", description="管理员ID"),
 *   @OA\Property(property="add_by", type="string", description="管理员"),
 *   @OA\Property(property="add_at", type="string", description="时间"),
 * )
 */
class VerifiedPrizeBlackUserTransformer extends Transformer
{
    public function transform(VerifiedPrizeBlackUser $blackUser)
    {
        return [
            'id'              => $blackUser->id,
            'user_id'         => $blackUser->user_id,
            'currency'        => $blackUser->user->currency,
            'user_name'       => $blackUser->user_name,
            'add_by_admin_id' => $blackUser->add_by_admin_id,
            'add_by'          => $blackUser->add_by,
            'add_at'          => $blackUser->add_at,
            'created_at'      => convert_time($blackUser->created_at),
            'updated_at'      => convert_time($blackUser->updated_at),
        ];
    }
}
