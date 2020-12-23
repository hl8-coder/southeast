<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\VipRequest;
use App\Models\Vip;
use App\Transformers\VipTransformer;
use Illuminate\Http\Request;

class VipsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/vips",
     *      operationId="backstage.vips.index",
     *      tags={"Backstage-Vip"},
     *      summary="vip列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Vip"),
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
        return $this->response->collection(Vip::getAll()->sortBy('level'), new VipTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/vips",
     *      operationId="backstage.vips.store",
     *      tags={"Backstage-Vip"},
     *      summary="添加vip",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="level", type="string", description="等级"),
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="display_name", type="string", description="前端显示名称"),
     *                  @OA\Property(property="rule", type="integer", description="等级条件"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"level", "name", "display_name", "rule"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Vip"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(VipRequest $request)
    {
        $data = remove_null($request->all());
        $vip = Vip::query()->create($data);
        return $this->response->item(Vip::findByCache($vip->id), new VipTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/vips/{vip}",
     *      operationId="backstage.vips.update",
     *      tags={"Backstage-Vip"},
     *      summary="更新Vip信息",
     *      @OA\Parameter(
     *         name="vip",
     *         in="path",
     *         description="Vip id",
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
     *                  @OA\Property(property="level", type="string", description="等级"),
     *                  @OA\Property(property="name", type="string", description="名称"),
     *                  @OA\Property(property="display_name", type="string", description="前端显示名称"),
     *                  @OA\Property(property="rule", type="integer", description="等级条件"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Vip"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(Vip $vip, VipRequest $request)
    {
        $data = remove_null($request->all());
        $vip->update($data);

        return $this->response->item($vip, new VipTransformer());
    }
}
