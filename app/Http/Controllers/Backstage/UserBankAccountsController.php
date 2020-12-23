<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\UserBankAccount;
use App\Transformers\AuditTransformer;
use App\Transformers\UserBankAccountTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UserBankAccountsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_bank_accounts",
     *      operationId="backstage.user_bank_accounts.index",
     *      tags={"Backstage-会员银行卡"},
     *      summary="会员银行卡列表",
     *      @OA\Parameter(name="filter[user_id]", in="query", description="会员id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[account_no]", in="query", description="账号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[account_name]", in="query", description="开户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserBankAccount"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $bankAccounts = QueryBuilder::for(UserBankAccount::class)
                        ->allowedFilters([
                            Filter::scope('account_no'),
                            'account_name',
                            Filter::exact('user_id'),
                            Filter::scope('user_name'),
                            Filter::scope('currency'),
                        ])
                        ->latest('updated_at')
                        ->with(['user', 'bank'])
                        ->paginate($request->per_page);

        return $this->response->paginator($bankAccounts,  new UserBankAccountTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/user_bank_accounts/{user_bank_account}/status",
     *      operationId="backstage.user_bank_accounts.status",
     *      tags={"Backstage-会员银行卡"},
     *      summary="更改会员银行卡状态",
     *      @OA\Parameter(
     *          name="user_bank_account",
     *          in="path",
     *          description="银行卡id",
     *          @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateStatus(UserBankAccount $userBankAccount)
    {
        if ($userBankAccount->isActive()) {
            $userBankAccount->update(['status' => UserBankAccount::STATUS_INACTIVE]);
        } elseif ($userBankAccount->isInActive()) {
            $userBankAccount->update(['status' => UserBankAccount::STATUS_ACTIVE]);
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Delete(
     *      path="/backstage/user_bank_accounts/{user_bank_account}",
     *      operationId="backstage.user_bank_accounts.delete",
     *      tags={"Backstage-会员银行卡"},
     *      summary="删除会员银行卡",
     *      @OA\Parameter(
     *         name="user_bank_account",
     *         in="path",
     *         description="会员银行卡id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(UserBankAccount $userBankAccount)
    {
        $userBankAccount->delete();

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_bank_accounts/{user_bank_account}/audit",
     *      operationId="backstage.user_bank_account.audit",
     *      tags={"Backstage-会员银行卡"},
     *      summary="会员银行卡修改记录",
     *      @OA\Parameter(
     *         name="user_bank_account",
     *         in="path",
     *         description="会员银行卡ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="field",
     *         in="query",
     *         description="查询字段",
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Audit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *           {"bearer": {}}
     *       }
     *  )
     */
    public function audit(UserBankAccount $userBankAccount, Request $request)
    {
        $field = $request->field;
        $audits = $userBankAccount->audits()->whereRaw("FIND_IN_SET(?, tags)", $field)->get();

        foreach ($audits as $audit) {
            $audit->new_value = $audit->new_values[$field];
            $audit->old_value = $audit->old_values[$field];
        }

        return $this->response->collection($audits, new AuditTransformer());
    }

}
