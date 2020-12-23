<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\UserAccount;
use App\Repositories\BonusRepository;
use App\Transformers\BonusTransformer;
use Illuminate\Http\Request;

class BonusesController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/bonuses",
     *      operationId="api.bonuses.index",
     *      tags={"Api-优惠"},
     *      summary="红利列表",
     *      @OA\Parameter(name="from_platform_code", in="query", description="转出平台code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="to_platform_code", in="query", description="转入平台code", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Bonus"),
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
        if (empty($request->from_platform_code)
            || !UserAccount::isMainWallet($request->from_platform_code)
            || UserAccount::isMainWallet($request->to_platform_code)
        ) {
            return $this->response->array(['data' => []]);
        }

        $bonuses = BonusRepository::getUserBonusesByCache($this->user, $request->to_platform_code);

        return $this->response->collection($bonuses, new BonusTransformer('front_index'));
    }
}
