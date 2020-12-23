<?php
namespace App\Transformers;

use App\Models\ExchangeRate;

/**
 * @OA\Schema(
 *   schema="ExchangeRate",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="会员id"),
 *   @OA\Property(property="user_currency", type="string", description="会员币别"),
 *   @OA\Property(property="platform_currency", type="string", description="第三方币别"),
 *   @OA\Property(property="conversion_value", type="number", description="正向汇率"),
 *   @OA\Property(property="inverse_conversion_value", type="number", description="逆向汇率"),
 * )
 */
class ExchangeRateTransformer extends Transformer
{
    public function transform(ExchangeRate $rate)
    {
        return [
            'id'                        => $rate->id,
            'user_currency'             => $rate->user_currency,
            'platform_currency'         => $rate->platform_currency,
            'conversion_value'          => $rate->conversion_value,
            'inverse_conversion_value'  => $rate->inverse_conversion_value,
        ];
    }
}