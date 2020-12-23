<?php
namespace App\Transformers;

use App\Models\Reward;

/**
 * @OA\Schema(
 *   schema="Reward",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="level", type="string", description="等级"),
 *   @OA\Property(property="rule", type="integer", description="等级条件"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 * )
 */
class RewardTransformer extends Transformer
{
    public function transform(Reward $remark)
    {
        return [
            'id'            => $remark->id,
            'level'         => $remark->level,
            'rule'          => $remark->rule,
            'remark'        => $remark->remark,
        ];
    }
}