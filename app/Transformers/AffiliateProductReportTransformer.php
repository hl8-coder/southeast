<?php


namespace App\Transformers;


use App\Models\GamePlatformProduct;

/**
 * @OA\Schema(
 *   schema="AffiliateProductReport",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="code", type="string", description="产品Code"),
 *   @OA\Property(property="total_stake", type="string", description="总投注"),
 *   @OA\Property(property="total_profit", type="string", description="总盈亏"),
 *   @OA\Property(property="total_rakes", type="integer", description="总返点"),
 * )
 */
class AffiliateProductReportTransformer extends Transformer
{
    public function transform(GamePlatformProduct $product)
    {
        $data         = [];
        $data['id'] = $product->id;
        $data['code'] = $product->code;
        switch ($this->type) {
            case 'company_product_report':
                $data['total_stake']  = thousands_number($product->total_stake);
                $data['total_profit'] = thousands_number($product->total_profit);
                $data['total_rakes']  = thousands_number($product->total_rakes);
                break;
        }
        return $data;
    }
}