<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\UserBankAccountRequest;
use App\Models\UserBankAccount;
use App\Transformers\UserBankAccountTransformer;

class UserBankAccountsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/user_bank_accounts?include=bank",
     *      operationId="api.user_bank_accounts.index",
     *      tags={"Api-会员银行卡"},
     *      summary="会员银行卡列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserBankAccount"),
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
        $userBankAccounts = $this->user->bankAccounts()->active()->latest('last_used_at')->get();

        if (!$userBankAccounts) {
            return $this->response->noContent();
        }

        return $this->response->collection($userBankAccounts, new UserBankAccountTransformer());
    }

    /**
     * @OA\Post(
     *      path="/user_bank_accounts?include=bank",
     *      operationId="api.user_bank_accounts.store",
     *      tags={"Api-会员银行卡"},
     *      summary="会员添加银行卡",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="bank_id", type="integer", description="银行id"),
     *                  @OA\Property(property="province", type="string", description="省"),
     *                  @OA\Property(property="city", type="string", description="市"),
     *                  @OA\Property(property="branch", type="string", description="支行"),
     *                  @OA\Property(property="account_name", type="string", description="户名"),
     *                  @OA\Property(property="account_no", type="string", description="卡号"),
     *                  @OA\Property(property="is_preferred", type="string", description="是否首選"),
     *                  required={"bank_id", "branch", "account_name", "account_no"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(UserBankAccountRequest $request)
    {
        $data = $request->all();

        // 如果设定首选，将取消其它银行卡首选设定
        if ($data['is_preferred']) {
            $this->user->bankAccounts()->update(['is_preferred' => 0]);
        }
        $data = remove_null($data);
        $userBankAccount  = $this->user->bankAccounts()->create($data);
        return $this->response->item($userBankAccount, new UserBankAccountTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/user_bank_accounts/{user_bank_account}?include=bank",
     *      operationId="api.user_bank_accounts.update",
     *      tags={"Api-会员银行卡"},
     *      summary="会员更新银行卡",
     *      @OA\Parameter(
     *         name="user_bank_account",
     *         in="path",
     *         description="会员银行卡id",
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
     *                  @OA\Property(property="bank_id", type="integer", description="银行id"),
     *                  @OA\Property(property="province", type="string", description="省"),
     *                  @OA\Property(property="city", type="string", description="市"),
     *                  @OA\Property(property="branch", type="string", description="支行"),
     *                  @OA\Property(property="is_preferred", type="integer", description="是否是默认银行卡"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/UserBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(UserBankAccount $userBankAccount, UserBankAccountRequest $request)
    {
        $this->authorize('own', $userBankAccount);

        if (!$userBankAccount->isActive()) {
            return $this->response->error(__('userAccount.BANK_ACCOUNT_NOT_EXISTS'), 422);
        }

        $data = $request->only([
            'bank_id', 'province', 'city', 'branch', 'is_preferred',
        ]);
        
        $data = collect($data)->map(function ($value) {
            return !is_null($value) ? $value : '';
        })->toArray();

        $userBankAccount->update($data);

        # 将其他银行卡设置为非默认
        if (!empty($request->is_preferred)) {
            $this->user->bankAccounts()->where('id', '!=', $userBankAccount->id)->update(['is_preferred' => 0]);
        }

        return $this->response->item($userBankAccount, new UserBankAccountTransformer());
    }
}
