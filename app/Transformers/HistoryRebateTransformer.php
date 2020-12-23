<?php

namespace App\Transformers;

use App\Models\UserRebatePrize;
use App\Models\GamePlatformProduct;

/**
 * @OA\Schema(
 *   schema="HistoryRebate",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="type", type="string", description="类型"),
 *   @OA\Property(property="product", type="string", description="产品"),
 *   @OA\Property(property="calculate_rebate_bet", type="string", description="投注金额"), 
 *   @OA\Property(property="multipiler", type="string", description="返点率"), 
 *   @OA\Property(property="amount", type="string", description="金额"),
 *   @OA\Property(property="created_at", type="string", description="建立日期") 
 * )
 */
class HistoryRebateTransformer extends Transformer
{
    public function transform($data)
    {
        $type = GamePlatformProduct::$types[$data->gamePlatformProduct->type];
        $product = $data->gamePlatformProduct->platform->name;
        
        return [
            'id'        	        => $data->id,
            'type'                  => $type,
            'product'               => $product,
            'calculate_rebate_bet'  => $data->calculate_rebate_bet,
            'multipiler'    	    => $data->multipiler,
            'amount'    	        => thousands_number($data->prize),
            'created_at'            => convert_time($data->created_at),
        ];
    }
}