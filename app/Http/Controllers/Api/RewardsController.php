<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Reward;
use App\Transformers\RewardTransformer;
use Illuminate\Http\Request;

class RewardsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/rewards",
     *      operationId="api.rewards.index",
     *      tags={"Api-平台"},
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
        $rewards = Reward::getAll();

        return $this->response->collection($rewards, new RewardTransformer());
    }
}
