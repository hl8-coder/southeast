<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Currency;
use App\Transformers\CurrencyTransformer;
use Illuminate\Http\Request;

class CurrenciesController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/currencies",
     *      operationId="api.currencies.index",
     *      tags={"Api-平台"},
     *      summary="币别列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Currency"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function index()
    {
        $currencies = Currency::getAll()->filter(function($value) {
                if ($value->status != true) {
                    return false;
                }

                if (!in_array($value->code, ['THB', 'VND'])) {
                    return false;
                }

                return true;
            })->sortByDesc('sort');
        return $this->response->collection($currencies, new CurrencyTransformer());
    }


    /**
     * @OA\Get(
     *      path="/currencies/current",
     *      operationId="api.currencies.currency",
     *      tags={"Api-平台"},
     *      summary="单个币别设置列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              @OA\Items(ref="#/components/schemas/Currency"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function findCurrency(Request $request)
    {
        $currencyCode = $request->header('currency');
        $currency     = Currency::findByCodeFromCache($currencyCode);
        return $this->response->item($currency, new CurrencyTransformer('front_show'));
    }
}
