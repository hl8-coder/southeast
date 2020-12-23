<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\ExchangeRateRequest;
use App\Models\ExchangeRate;
use App\Transformers\ExchangeRateTransformer;
use Illuminate\Http\Request;

class ExchangeRatesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/exchange_rates",
     *      operationId="backstage.exchange_rates.index",
     *      tags={"Backstage-币别"},
     *      summary="获取汇率列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/ExchangeRate"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index()
    {
        return $this->response->collection(ExchangeRate::getAll(), new ExchangeRateTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/exchange_rates",
     *      operationId="backstage.exchange_rates.store",
     *      tags={"Backstage-币别"},
     *      summary="添加汇率",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_currency", type="string", description="币别1"),
     *                  @OA\Property(property="platform_currency", type="string", description="币别2"),
     *                  @OA\Property(property="conversion_value", type="number", description="正向汇率"),
     *                  @OA\Property(property="inverse_conversion_value", type="number", description="逆向汇率"),
     *                  required={"currency_code_from", "currency_code_to", "conversion_value", "inverse_conversion_value"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/ExchangeRate"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(ExchangeRateRequest $request)
    {
        $data = remove_null($request->all());

        if (ExchangeRate::findRate($data['user_currency'], $data['platform_currency'])) {
            return $this->response->error('Exchange rate already exists.', 422);
        }
        $rate = ExchangeRate::query()->create($data);

        return $this->response->item(ExchangeRate::find($rate), new ExchangeRateTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/exchange_rates/{exchange_rate}",
     *      operationId="backstage.exchange_rates.update",
     *      tags={"Backstage-币别"},
     *      summary="更新汇率",
     *       @OA\Parameter(
     *         name="exchange_rate",
     *         in="path",
     *         description="汇率id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_currency", type="string", description="币别1"),
     *                  @OA\Property(property="platform_currency", type="string", description="币别2"),
     *                  @OA\Property(property="conversion_value", type="number", description="正向汇率"),
     *                  @OA\Property(property="inverse_conversion_value", type="number", description="逆向汇率"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/ExchangeRate"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(ExchangeRate $exchangeRate, ExchangeRateRequest $request)
    {
        $data = remove_null($request->all());

        if (isset($data['user_currency']) && isset($data['platform_currency'])) {
            $rate = ExchangeRate::findRate($data['user_currency'], $data['platform_currency']);
            if ($rate && $rate->id != $exchangeRate->id) {
                return $this->response->error('Exchange rate already exists.', 422);
            }
        }

        $exchangeRate->update($data);
        return $this->response->item($exchangeRate, new ExchangeRateTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/exchange_rates/{exchange_rate}",
     *      operationId="backstage.exchange_rates.delete",
     *      tags={"Backstage-币别"},
     *      summary="删除汇率",
     *      @OA\Parameter(
     *         name="exchange_rate",
     *         in="path",
     *         description="汇率id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function destroy(ExchangeRate $exchangeRate)
    {
        $exchangeRate->delete();
        return $this->response->noContent();
    }
}
