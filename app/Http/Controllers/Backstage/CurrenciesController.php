<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\CurrencyRequest;
use App\Models\Currency;
use App\Transformers\CurrencyTransformer;

class CurrenciesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/currencies",
     *      operationId="backstage.currencies.index",
     *      tags={"Backstage-平台"},
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
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index()
    {
        return $this->response->collection(Currency::getAll(), new CurrencyTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/currencies",
     *      operationId="backstage.currencies.store",
     *      tags={"Backstage-平台"},
     *      summary="添加币别",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="code", type="string", description="代码"),
     *                  @OA\Property(property="preset_language", type="string", description="预设语言"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="country", type="string", description="所属国家"),
     *                  @OA\Property(property="country_code", type="string", description="国家电话编码"),
     *                  @OA\Property(property="is_remove_three_zeros", type="boolean", description="是否去掉三个零"),
     *                  @OA\Property(property="deposit_second_approve_amount", type="number", description="充值需要二次审核金额"),
     *                  @OA\Property(property="withdrawal_second_approve_amount", type="number", description="提现需要二次审核金额"),
     *                  @OA\Property(property="bank_account_verify_amount", type="number", description="个人银行卡验证金额"),
     *                  @OA\Property(property="info_verify_prize_amount", type="number", description="资料验证完成奖金"),
     *                  @OA\Property(property="max_deposit", type="number", description="最高充值限制"),
     *                  @OA\Property(property="min_deposit", type="number", description="最低充值限制"),
     *                  @OA\Property(property="max_withdrawal", type="number", description="最高出款限制"),
     *                  @OA\Property(property="min_withdrawal", type="number", description="最低出款限制"),
     *                  @OA\Property(property="max_daily_withdrawal", type="number", description="日出款总金额限制"),
     *                  @OA\Property(property="min_transfer", type="number", description="最小转账限制"),
     *                  @OA\Property(property="commission", type="number", description="代理抽成百分比"),
     *                  @OA\Property(property="payout_comm_mini_limit", type="string", description="代理盈亏最小出款金额"),
     *                  @OA\Property(property="deposit_pending_limit", type="number", description="允许订单pending数量最大值"),
     *                  @OA\Property(property="withdrawal_pending_limit", type="number", description="允许订单pending数量最大值"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"name", "code", "preset_language", "country", "country_code"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Currency"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function store(CurrencyRequest $request)
    {
        $data = remove_null($request->all());
        $currency = Currency::query()->create($data);
        return $this->response->item(Currency::find($currency->id), new CurrencyTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/currencies/{currency}",
     *      operationId="backstage.currencies.update",
     *      tags={"Backstage-平台"},
     *      summary="更新币别",
     *      @OA\Parameter(
     *         name="currency",
     *         in="path",
     *         description="币别id",
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
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="code", type="string", description="代码"),
     *                  @OA\Property(property="preset_language", type="string", description="预设语言"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="country", type="string", description="所属国家"),
     *                  @OA\Property(property="country_code", type="string", description="国家电话编码"),
     *                  @OA\Property(property="is_remove_three_zeros", type="boolean", description="是否去掉三个零"),
     *                  @OA\Property(property="deposit_second_approve_amount", type="number", description="充值需要二次审核金额"),
     *                  @OA\Property(property="withdrawal_second_approve_amount", type="number", description="提现需要二次审核金额"),
     *                  @OA\Property(property="bank_account_verify_amount", type="number", description="个人银行卡验证金额"),
     *                  @OA\Property(property="info_verify_prize_amount", type="number", description="资料验证完成奖金"),
     *                  @OA\Property(property="max_deposit", type="number", description="最高充值限制"),
     *                  @OA\Property(property="min_deposit", type="number", description="最低充值限制"),
     *                  @OA\Property(property="max_withdrawal", type="number", description="最高出款限制"),
     *                  @OA\Property(property="min_withdrawal", type="number", description="最低出款限制"),
     *                  @OA\Property(property="max_daily_withdrawal", type="number", description="日出款总金额限制"),
     *                  @OA\Property(property="min_transfer", type="number", description="最小转账限制"),
     *                  @OA\Property(property="commission", type="number", description="代理抽成百分比"),
     *                  @OA\Property(property="payout_comm_mini_limit", type="string", description="代理盈亏最小出款金额"),
     *                  @OA\Property(property="deposit_pending_limit", type="number", description="允许订单pending数量最大值"),
     *                  @OA\Property(property="withdrawal_pending_limit", type="number", description="允许订单pending数量最大值"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Currency"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function update(Currency $currency, CurrencyRequest $request)
    {
        $data = remove_null($request->all());
        $currency->update($data);
        return $this->response->item($currency, new CurrencyTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/currencies/{currency}",
     *      operationId="backstage.currencies.delete",
     *      tags={"Backstage-平台"},
     *      summary="删除币别",
     *      @OA\Parameter(
     *         name="currency",
     *         in="path",
     *         description="币别id",
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
    public function destroy(Currency $currency)
    {
        $currency->delete();
        return $this->response->noContent();
    }
}
