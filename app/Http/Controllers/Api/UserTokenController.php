<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;

class UserTokenController extends ApiController
{

    /**
     * @OA\Get(
     *      path="/token",
     *      operationId="api.user.token.index",
     *      tags={"Api-授权"},
     *      summary="获取token有效性",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#")
     *       ),
     *      @OA\Response(
     *          response=401,
     *          description="token失效",
     *          @OA\JsonContent(ref="#")
     *       )
     *     )
     */
    public function index()
    {
        $user = auth('api')->user();
        if ($user){
            return $this->response()->noContent();
        }else{
            return $this->response()->error('Forbidden', 403);
        }
    }
}
