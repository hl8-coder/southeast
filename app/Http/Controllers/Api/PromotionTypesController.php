<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\PromotionType;
use App\Transformers\PromotionTypeTransformer;
use Illuminate\Http\Request;

class PromotionTypesController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/promotion_types",
     *      operationId="api.promotion_types.index",
     *      tags={"Api-优惠"},
     *      summary="优惠类型列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PromotionType"),
     *          ),
     *       ),
     *  )
     */
    public function index(Request $request)
    {
        $currency = $request->header('currency');
        $promotionTypes = PromotionType::getAll()->where('status', true)->sortByDesc('sort')->filter(function($value) use ($currency) {
            return $value->checkCurrencySet($currency);
        });

        return $this->response->collection($promotionTypes, new PromotionTypeTransformer('front_index', ['currency' => $currency]));
    }
}
