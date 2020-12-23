<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PointRuleRequest;
use App\Models\BetToRewardRule;
use App\Transformers\BetToRewardRuleTransformer;
use Illuminate\Http\Request;

class BetToRewardRulesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/bet_to_reward_rules",
     *      operationId="backstage.bet_to_reward_rules.index",
     *      tags={"Backstage-Vip"},
     *      summary="积分兑换规则",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/BetToRewardRule"),
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
        $rules = BetToRewardRule::getAll();
        return $this->response->collection($rules, new BetToRewardRuleTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/bet_to_reward_rules",
     *      operationId="backstage.bet_to_reward_rules.store",
     *      tags={"Backstage-Vip"},
     *      summary="添加积分兑换规则",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="rule", type="integer", description="1积分所需金额"),
     *                  required={"currency", "rule"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/BetToRewardRule"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function store(PointRuleRequest $request)
    {
        $data = remove_null($request->all());
        $rule = BetToRewardRule::query()->create($data);
        return $this->response->item(BetToRewardRule::find($rule->id), new BetToRewardRuleTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/bet_to_reward_rules/{bet_to_reward_rule}",
     *      operationId="backstage.bet_to_reward_rules.update",
     *      tags={"Backstage-Vip"},
     *      summary="添加积分兑换规则",
     *      @OA\Parameter(
     *         name="bet_to_reward_rule",
     *         in="path",
     *         description="兑换id",
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
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="rule", type="integer", description="1积分所需金额"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/BetToRewardRule"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function update(BetToRewardRule $pointRule, PointRuleRequest $request)
    {
        $data = remove_null($request->all());
        $pointRule->update($data);
        return $this->response->item($pointRule, new BetToRewardRuleTransformer());
    }
}
