<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\RewardRequest;
use App\Models\Reward;
use App\Transformers\RewardTransformer;
use Illuminate\Http\Request;

class RewardsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/rewards",
     *      operationId="backstage.rewards.index",
     *      tags={"Backstage-积分等级"},
     *      summary="积分等级列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Reward"),
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
        $vips = Reward::getAll()->sortBy('level');

        return $this->response->collection($vips, new RewardTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/rewards",
     *      operationId="backstage.rewards.store",
     *      tags={"Backstage-积分等级"},
     *      summary="添加积分等级",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="level", type="string", description="等级"),
     *                  @OA\Property(property="rule", type="integer", description="等级条件"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"level", "rule"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Reward"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(RewardRequest $request)
    {
        $data = remove_null($request->all());
        $reward = Reward::query()->create($data);
        return $this->response->item(Reward::find($reward->id), new RewardTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/rewards/{reward}",
     *      operationId="backstage.rewards.update",
     *      tags={"Backstage-积分等级"},
     *      summary="更新积分等级",
     *      @OA\Parameter(
     *         name="reward",
     *         in="path",
     *         description="积分等级id",
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
     *                  @OA\Property(property="rule", type="integer", description="等级条件"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Reward"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(Reward $reward, RewardRequest $request)
    {
        $data = remove_null($request->all());
        $reward->update($data);
        return $this->response->item($reward, new RewardTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/rewards/{reward}",
     *      operationId="backstage.rewards.delete",
     *      tags={"Backstage-积分等级"},
     *      summary="删除积分等级",
     *      @OA\Parameter(
     *         name="reward",
     *         in="path",
     *         description="积分等级id",
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
    public function destroy(Reward $reward)
    {
        $reward->delete();
        return $this->response->noContent();
    }
}
