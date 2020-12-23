<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserMpayNumberRequest;
use App\Models\UserMpayNumber;
use App\Transformers\UserMpayNumberTransformer;

class UserMpayNumbersController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/user_mpay_numbers",
     *      operationId="api.user_mpay_numbers.index",
     *      tags={"Api-会员Mpay"},
     *      summary="会员Mpay列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserMpayNumber"),
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
        $userBankAccounts = $this->user->mpayNumbers()->active()->orderBy('last_used_at', 'desc')->get();

        return $this->response->collection($userBankAccounts, new UserMpayNumberTransformer());
    }

    /**
     * @OA\Post(
     *      path="/user_mpay_numbers",
     *      operationId="api.user_mpay_numbers.store",
     *      tags={"Api-会员Mpay"},
     *      summary="会员添加Mpay",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="area_code", type="integer", description="区码"),
     *                  @OA\Property(property="number", type="string", description="号码"),
     *                  required={"area_code", "number"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserMpayNumber"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(UserMpayNumberRequest $request)
    {
        $data = $request->all();

        $userMpayNumber = $this->user->mpayNumbers()->create($data);

        return $this->response->item($userMpayNumber, new UserMpayNumberTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/user_mpay_numbers/{user_mpay_number}",
     *      operationId="api.user_mpay_numbers.update",
     *      tags={"Api-会员Mpay"},
     *      summary="更新Mpay",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="area_code", type="integer", description="区码"),
     *                  @OA\Property(property="number", type="string", description="号码"),
     *                  required={"area_code", "number"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserMpayNumber"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(UserMpayNumber $userMpayNumber, UserMpayNumberRequest $request)
    {
        $data = $request->all(['area_code', 'number']);

        $userMpayNumber->update($data);

        return $this->response->item($userMpayNumber, new UserMpayNumberTransformer())->setStatusCode(201);
    }
}
