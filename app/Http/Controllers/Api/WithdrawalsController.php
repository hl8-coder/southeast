<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\WithdrawalRequest;
use App\Models\FreezeLog;
use App\Models\UserBankAccount;
use App\Models\Withdrawal;
use App\Repositories\UserAccountRepository;
use App\Repositories\WithdrawalRepository;
use App\Services\WithdrawalService;
use App\Transformers\WithdrawalTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class WithdrawalsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/withdrawals",
     *      operationId="api.withdrawals.index",
     *      tags={"Api-提现"},
     *      summary="提现列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Withdrawal"),
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
        $withdraws = QueryBuilder::for(Withdrawal::class)
            ->where('user_id', $this->user()->id)
            ->allowedFilters(
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($withdraws, new WithdrawalTransformer('front'));
    }

    /**
     * @OA\Get(
     *      path="/withdrawals/{withdrawal}?include=bank",
     *      operationId="api.withdrawals.show",
     *      tags={"Api-提现"},
     *      summary="提现详情",
     *       @OA\Parameter(
     *         name="withdrawal",
     *         in="path",
     *         description="提现id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="创建成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Withdrawal"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="无效token"),
     *      @OA\Response(response=422, description="验证错误"),
     *      @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function show(Withdrawal $withdrawal)
    {
        $this->authorize('own', $withdrawal);

        return $this->response->item($withdrawal, new WithdrawalTransformer('front'));

    }

    /**
     * @OA\Post(
     *      path="/withdrawals",
     *      operationId="api.withdrawals.store",
     *      tags={"Api-提现"},
     *      summary="申请提现",
     *      @OA\Parameter(
     *         name="device",
     *         in="header",
     *         description="装置",
     *         required=true,
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
     *                  @OA\Property(property="user_bank_account_id", type="integer", description="会员银行卡id"),
     *                  @OA\Property(property="amount", type="number", description="提现金额"),
     *                  required={"user_bank_account_id", "amount"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Withdrawal"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(WithdrawalRequest $request, WithdrawalService $service)
    {
        $user = $this->user();

        # 各种判断
        $data = $request->only(['user_bank_account_id', 'amount']);

        if (is_array($data['user_bank_account_id'])) {
            $data['user_bank_account_id'] = $data['user_bank_account_id'][0];
        }

        $userBankAccount = UserBankAccount::find($data['user_bank_account_id']);

        # 检查系统配置限制
        WithdrawalRepository::checkConfigWithdrawalLimit($user, $userBankAccount->bank_id, $data['amount']);
        WithdrawalRepository::checkWithdrawalPendingLimit($user);

        if (!$userBankAccount || !$userBankAccount->isActive()) {
            return $this->response->error(__('withdrawal.INVALID_BANK_CARD'), 422);
        }

        $this->authorize('own', $userBankAccount);

        try{
            $withdraw = DB::transaction(function () use ($service, $user, $userBankAccount, $data, $request) {
                return $service->store($user, $userBankAccount, $data['amount'], $request->header('device'), $request->getClientIp());
            });
        }catch (\Exception $e){
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($withdraw, new WithdrawalTransformer('front'))->setStatusCode(201);
    }

    /**
     * @OA\Delete(
     *      path="/withdrawals/{withdrawal}",
     *      operationId="api.withdrawals.cancel",
     *      tags={"Api-提现"},
     *      summary="取消提现",
     *       @OA\Parameter(
     *         name="withdrawal",
     *         in="path",
     *         description="提现id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=401, description="无效token"),
     *      @OA\Response(response=422, description="验证错误"),
     *      @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(Withdrawal $withdrawal)
    {
        $this->authorize('own', $withdrawal);

        if (!$withdrawal->isPending()) {
            return $this->response->error(__('public.STATUS_ERROR'), 422);
        }

        # 解冻
        if ($withdrawal->cancel()) {
            UserAccountRepository::unfreeze($this->user->account, $withdrawal->amount, FreezeLog::TYPE_WITHDRAW, $withdrawal->id);
        }

        return $this->response->noContent();
    }
}
