<?php
namespace App\Transformers;

use App\Models\BetToRewardRule;

/**
 * @OA\Schema(
 *   schema="BetToRewardRule",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="rule", type="integer", description="1积分兑换所需金额"),
 * )
 */
class BetToRewardRuleTransformer extends Transformer
{
    public function transform(BetToRewardRule $rule)
    {
        return [
            'id'        => $rule->id,
            'currency'  => $rule->currency,
            'rule'      => $rule->rule,
        ];
    }
}