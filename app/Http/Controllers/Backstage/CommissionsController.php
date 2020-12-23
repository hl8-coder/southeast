<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\Commission;
use App\Models\User;
use App\Transformers\CommissionTransformer;
use Illuminate\Http\Request;

class CommissionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/users/{user}/commissions",
     *      operationId="backstage.users.commissions.index",
     *      tags={"Backstage-代理"},
     *      summary="代理分红",
     *      @OA\Parameter(name="user", in="path", description="代理id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Commission"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userIndex(Request $request, User $user)
    {
        $commissions = Commission::getByUserId($user->id);

        return $this->response->collection($commissions, new CommissionTransformer());
    }
}
