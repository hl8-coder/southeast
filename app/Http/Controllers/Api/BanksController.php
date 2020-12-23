<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Bank;
use App\Transformers\BankTransformer;
use Illuminate\Http\Request;

class BanksController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/banks",
     *      operationId="api.banks.index",
     *      tags={"Api-平台"},
     *      summary="获取当前币种银行列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Bank"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $currency       = $request->header('currency');
        $bankCollection = Bank::getAll()->where('status', true)->where('currency', $currency);
        return $this->response->collection($bankCollection, new BankTransformer());
    }

    /**
     * @OA\Get(
     *      path="/banks/maintenance",
     *      operationId="api.banks.maintenance.index",
     *      tags={"Api-平台"},
     *      summary="获取维护银行列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Bank"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function maintenanceIndex(Request $request)
    {

        $bankCollection = Bank::getAll()->where('status', true)
                        ->where('currency', $this->user->currency)
                        ->filter(function($value) {
                            $languageSet = $value->getLanguageSet(app()->getLocale());
                            return !empty($languageSet['maintenance_schedules']);

                        });
        return $this->response->collection($bankCollection, new BankTransformer('front'));
    }
}
