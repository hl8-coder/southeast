<?php
namespace App\Transformers;

use App\Models\Vip;

/**
 * @OA\Schema(
 *   schema="Vip",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="level", type="string", description="等级"),
 *   @OA\Property(property="name", type="string", description="名称"),
 *   @OA\Property(property="display_name", type="string", description="前端显示名称"),
 *   @OA\Property(property="rule", type="integer", description="等级条件"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 * )
 */
class VipTransformer extends Transformer
{
    public function transform(Vip $vip)
    {
        return [
            'id'            => $vip->id,
            'level'         => $vip->level,
            'name'          => $vip->name,
            'display_name'  => $vip->display_name,
            'rule'          => $vip->rule,
            'remark'        => $vip->remark,
        ];
    }
}