<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Requests\Backstage\RebateRequest;
use App\Models\Rebate;
use App\Transformers\RebateTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\BackstageController;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class RebatesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/rebates",
     *      operationId="backstage.rebates.index",
     *      tags={"Backstage-返点"},
     *      summary="返点列表",
     *      @OA\Parameter(name="filter[code]", in="query", description="返点code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="第三方游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Rebate"),
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
        $rebates = QueryBuilder::for(Rebate::class)
                    ->allowedFilters(
                        Filter::exact('code'),
                        Filter::exact('product_code'),
                        Filter::exact('status')
                    )
                    ->latest()
                    ->get();

        return $this->response->collection($rebates, new RebateTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/rebates",
     *      operationId="backstage.rebates.store",
     *      tags={"Backstage-返点"},
     *      summary="添加返点",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string", description="辨识码"),
     *                  @OA\Property(property="product_code", type="string", description="产品code"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                     @OA\Property(property="currency", type="string", description="币别"),
     *                     @OA\Property(property="min_prize", type="number", description="最小奖励值"),
     *                     @OA\Property(property="max_prize", type="number", description="最大奖励值"),
     *                  )),
     *                  @OA\Property(property="risk_group_id", type="integer", description="风控组别id"),
     *                  @OA\Property(property="vips", type="array", description="vip", @OA\Items(
     *                     @OA\Property(property="vip_id", type="integer", description="vip ID"),
     *                     @OA\Property(property="multipiler", type="number", description="奖励计算百分比"),
     *                   )),
     *                  @OA\Property(property="is_manual_send", type="boolean", description="是否需要手动派发奖励"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"code", "product_code", "currencies", "vips"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *         response=201,
     *         description="创建成功",
     *         @OA\JsonContent(ref="#/components/schemas/Rebate"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function store(RebateRequest $request)
    {
        $data = remove_null($request->all());
        $data['admin_name'] = $this->user->name;
        $rebate = Rebate::query()->create($data);

        return $this->response->item($rebate, new RebateTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/rebates/{rebate}",
     *      operationId="backstage.rebates.store",
     *      tags={"Backstage-返点"},
     *      summary="更新返点",
     *      @OA\Parameter(name="rebate", in="path", description="返点id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="product_code", type="string", description="产品code"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items(
     *                     @OA\Property(property="currency", type="string", description="币别"),
     *                     @OA\Property(property="min_prize", type="number", description="最小奖励值"),
     *                     @OA\Property(property="max_prize", type="number", description="最大奖励值"),
     *                  )),
     *                  @OA\Property(property="risk_group_id", type="integer", description="风控组别id"),
     *                  @OA\Property(property="vips", type="array", description="vip", @OA\Items(
     *                     @OA\Property(property="vip_id", type="integer", description="vip ID"),
     *                     @OA\Property(property="multipiler", type="number", description="奖励计算百分比"),
     *                   )),
     *                  @OA\Property(property="is_manual_send", type="boolean", description="是否需要手动派发奖励"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/Rebate"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Rebate $rebate, RebateRequest $request)
    {
        $data = remove_null($request->except([
            'code',
            'admin_name',
        ]));
        $data['admin_name'] = $this->user->name;
        $rebate->update($data);
        return $this->response->item($rebate, new RebateTransformer());
    }

}
