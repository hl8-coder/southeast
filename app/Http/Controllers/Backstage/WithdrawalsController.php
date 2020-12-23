<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Handlers\ImageUploadHandler;
use App\Http\Requests\Backstage\WithdrawalRequest;
use App\Models\Adjustment;
use App\Models\Admin;
use App\Models\Image;
use App\Models\User;
use App\Models\Withdrawal;
use App\Repositories\ImageRepository;
use App\Repositories\RemarkRepository;
use App\Repositories\WithdrawalLogsRepository;
use App\Repositories\WithdrawalRepository;
use App\Transformers\AdjustmentTransformer;
use App\Transformers\ImageTransformer;
use App\Transformers\RemarkTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Models\Audit;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Transformers\WithdrawalTransformer;

class WithdrawalsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/withdrawals?include=user,bank",
     *      operationId="backstage.withdrawals.index",
     *      tags={"Backstage-提现"},
     *      summary="提现列表",
     *      @OA\Parameter(name="filter[user_id]", in="query", description="会员id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
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
    public function index(WithdrawalRequest $request)
    {
        $conditionArray = [
            'user_id' => Filter::exact('user_id'),
            'user_name' => Filter::scope('user_name'),
            'order_no' => Filter::exact('order_no'),
            'currency' => Filter::exact('currency'),
            'start_at' => Filter::scope('start_at'),
            'end_at' => Filter::scope('end_at'),
            'status' => Filter::exact('status'),
        ];
        $pagination = QueryBuilder::for (Withdrawal::class)->allowedFilters(array_values($conditionArray))->latest()->paginate($request->per_page);

        $statusArray = array_keys(Withdrawal::$statuses);
        array_push($statusArray, null);

        $conditionArray['status'] = Filter::exact('status')->ignore($statusArray);
        $total['transactions'] = QueryBuilder::for (Withdrawal::class)->allowedFilters(array_values($conditionArray))->where('status', Withdrawal::STATUS_SUCCESSFUL)->count();
        $successBuilder = QueryBuilder::for (Withdrawal::class)->allowedFilters(array_values($conditionArray))->where('status', Withdrawal::STATUS_SUCCESSFUL);
        $total['amount'] = thousands_number($successBuilder->sum('amount'));

        return $this->response->paginator($pagination, new WithdrawalTransformer('index'))->addMeta('total', $total);
    }

    /**
     * @OA\Get(
     *      path="/backstage/withdrawals/{withdrawal}/last_ten",
     *      operationId="backstage.withdrawals.last_ten",
     *      tags={"Backstage-提现"},
     *      summary="最近10条提现列表",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
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
    public function lastTenIndex(Withdrawal $withdrawal)
    {
        $withdrawals = Withdrawal::query()->where('user_id', $withdrawal->user_id)
            ->where('id', '!=', $withdrawal->id)
            ->latest()
            ->limit(10)
            ->get();

        return $this->response->collection($withdrawals, new WithdrawalTransformer('index'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/withdrawals/open?include=user,bank",
     *      operationId="backstage.withdrawals.open_index",
     *      tags={"Backstage-提现"},
     *      summary="提现未处理列表",
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
    public function openIndex(Request $request)
    {
        $withdrawQuery = Withdrawal::query()->whereNotIn('status', Withdrawal::$lastStatuses);

        if (!empty($request->filter['currency'])) {
            $withdrawQuery->where('currency', $request->filter['currency']);
        }

        $withdraws = $withdrawQuery->latest()->paginate($request->per_page);

        return $this->response->paginator($withdraws, new WithdrawalTransformer('open_index', ['admin' => $this->user]));
    }

    /**
     * @OA\Get(
     *      path="/backstage/withdrawals/fast?include=user,bank",
     *      operationId="backstage.withdrawals.fast_index",
     *      tags={"Backstage-提现"},
     *      summary="提现未处理列表",
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
    public function fastIndex(Request $request)
    {

        $withdraws = QueryBuilder::for (Withdrawal::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::exact('order_no'),
                Filter::exact('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status'),
            ])
            ->latest()
            ->paginate($request->per_page);

        $amount = QueryBuilder::for (Withdrawal::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::exact('order_no'),
                Filter::exact('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status'),
            ])->sum('amount');


        # 获取总条数
        $total_txn = QueryBuilder::for (Withdrawal::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::exact('order_no'),
                Filter::exact('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status'),
            ])->count();

        # unique member
        $unique_member = QueryBuilder::for (Withdrawal::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::exact('order_no'),
                Filter::exact('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status'),
            ])->distinct('user_id')
            ->count('user_id');
        $info = [['key' => 'total_amount', 'value' => thousands_number($amount)],['key' => 'total_txn', 'value' => $total_txn],['key' => 'unique_member','value' => $unique_member]];
        return $this->response->paginator($withdraws, new WithdrawalTransformer('index'))->setMeta(['info' => $info]);
    }


    /**
     * @OA\Get(
     *      path="/backstage/withdrawals/{withdrawal}?include=bank,user.info,user.account,images,userWithdrawals",
     *      operationId="api.backstage.withdrawals.show",
     *      tags={"Backstage-提现"},
     *      summary="提现详情",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show(Withdrawal $withdrawal)
    {
        # 判断claim权限
        if (!empty($withdrawal->claim_admin_name) && !WithdrawalRepository::isClaimAdmin($withdrawal, $this->user) && !in_array($withdrawal->status, Withdrawal::$lastStatuses)) {
            return $this->returnClaimAdminResponse();
        }

        $withdrawal->updateLastAccess($this->user->name);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/users/{user}/withdrawals/remarks",
     *      operationId="backstage.users.withdrawals.remarks.index",
     *      tags={"Backstage-提现"},
     *      summary="获取会员最近两年的hold Withdrawal的remark",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="会员id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Remark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function remarkIndex(User $user, Request $request)
    {
        $remarks = RemarkRepository::getByYear($user->id, 2)
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($remarks, new RemarkTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/claim",
     *      operationId="backstage.withdrawals.claim",
     *      tags={"Backstage-提现"},
     *      summary="锁定提现",
     *      @OA\Parameter(
     *         name="withdrawal",
     *         in="path",
     *         description="提现id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Withdrawal"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function claim(Withdrawal $withdrawal)
    {
        if (!empty($withdrawal->claim_admin_name)) {
            return $this->returnClaimAdminResponse();
        }

        $withdrawal->update([
            'claim_admin_name' => $this->user->name,
        ]);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/unclaim",
     *      operationId="backstage.withdrawals.unclaim",
     *      tags={"Backstage-提现"},
     *      summary="取消锁定提现",
     *      @OA\Parameter(
     *         name="withdrawal",
     *         in="path",
     *         description="提现id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Withdrawal"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function unclaim(Withdrawal $withdrawal)
    {
        if (!in_array($withdrawal->status, Withdrawal::$canUnclaimStatuses) || !WithdrawalRepository::isClaimAdmin($withdrawal, $this->user)) {
            return $this->returnClaimAdminResponse();
        }

        $withdrawal->update([
            'claim_admin_name' => null,
            'status' => Withdrawal::STATUS_PENDING,
        ]);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/users/{user}/withdrawals/adjustments",
     *      operationId="backstage.users.withdrawals.adjustments.index",
     *      tags={"Backstage-提现"},
     *      summary="获取会员最近两年的adjustment",
     *      @OA\Parameter(
     *         name="user",
     *         in="path",
     *         description="会员id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Adjustment"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function adjustmentIndex(User $user, Request $request)
    {
        $adjustments = Adjustment::query()->where('user_id', $user->id)
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($adjustments, new AdjustmentTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/withdrawals/{withdrawal}",
     *      operationId="backstage.withdrawals.reject",
     *      tags={"Backstage-提现"},
     *      summary="拒绝",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="reject_reason", type="integer", description="拒绝理由"),
     *                  required={"reject_reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function reject(Withdrawal $withdrawal, WithdrawalRequest $request)
    {
        if (!$withdrawal->checkCanRejectStatus()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal = WithdrawalRepository::reject($withdrawal, $request->reject_reason);

        # 关闭前端表单
        $withdrawal->closeForm();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/hold",
     *      operationId="backstage.withdrawals.hold",
     *      tags={"Backstage-提现"},
     *      summary="hold",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="hold_reason", type="integer", description="hold理由"),
     *                  required={"reject_reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function hold(Withdrawal $withdrawal, WithdrawalRequest $request)
    {
        if (!$withdrawal->checkCanHoldStatus()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->hold($request->hold_reason);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/release_hold",
     *      operationId="backstage.withdrawals.release_hold",
     *      tags={"Backstage-提现"},
     *      summary="解除hold状态",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function releaseHold(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isHold()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->releaseHold();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/review",
     *      operationId="backstage.withdrawals.review",
     *      tags={"Backstage-提现"},
     *      summary="初步审核",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function review(Withdrawal $withdrawal)
    {
        $key = 'withdrawal-lock-key-' . $withdrawal->id;

        if (Cache::has($key)) {
            return $this->response->error('status error.', 422);
        }

        Cache::put($key, '', now()->addHour());

        if (!$withdrawal->isPending()) {
            return $this->response->error('status error.', 422);
        }
        # 检查是否存在remark
        if (RemarkRepository::isHasWithdrawalNotRemoveRemark($withdrawal->user_id)) {
            return $this->response->error('please remove remark first.', 422);
        }

        $withdrawal->review();

        Cache::forget($key);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/process",
     *      operationId="backstage.withdrawals.process",
     *      tags={"Backstage-提现"},
     *      summary="复审",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function process(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isPending()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->process();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/defer",
     *      operationId="backstage.withdrawals.defer",
     *      tags={"Backstage-提现"},
     *      summary="延缓提现",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function defer(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isProcess()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->defer();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/add_records",
     *      operationId="backstage.withdrawals.defer",
     *      tags={"Backstage-提现"},
     *      summary="添加提款单",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="records", type="array", description="出账交易记录", @OA\Items(
     *                      @OA\Property(property="company_bank_account_code", type="string", description="公司银行卡code"),
     *                      @OA\Property(property="amount", type="number", description="出账金额"),
     *                      @OA\Property(property="fee", type="number", description="手续费"),
     *                  )),
     *                  required={"records"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function addRecords(Withdrawal $withdrawal, WithdrawalRequest $request)
    {
        if (!$withdrawal->isProcess()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->update([
            'records' => $request->records,
        ]);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/release_defer",
     *      operationId="backstage.withdrawals.release_defer",
     *      tags={"Backstage-提现"},
     *      summary="解除延缓状态",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function releaseDefer(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isDeferred()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->releaseDefer();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/approve",
     *      operationId="backstage.withdrawals.approve",
     *      tags={"Backstage-提现"},
     *      summary="完成出款",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approve(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isProcess()) {
            return $this->response->error('status error.', 422);
        }

        $totalAmount = collect($withdrawal->records)->sum('amount');
        # 检查金额是否出完
        if ($withdrawal->amount != $totalAmount) {
            return $this->response->error('The value is incorrect', 422);
        }

        # 更新状态
        WithdrawalRepository::approve($withdrawal, $this->user);

        # 关闭前端表单
        $withdrawal->refresh()->closeForm();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/second_approve",
     *      operationId="backstage.withdrawals.second_approve",
     *      tags={"Backstage-提现"},
     *      summary="二次审核通过",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function secondApprove(Withdrawal $withdrawal)
    {
        if (!$withdrawal->checkSecondVerifyStatus()) {
            return $this->response->error('status error.', 422);
        }

        # 如果初审是拒绝状态，审核失败
        # 如果初审是同意状态，审核成功
        if ($withdrawal->isApproved()) {
            WithdrawalRepository::success($withdrawal, $this->user);
        } else {
            WithdrawalRepository::fail($withdrawal);
        }

        # 关闭前端表单
        $withdrawal->refresh()->closeForm();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/second_reject",
     *      operationId="backstage.withdrawals.second_reject",
     *      tags={"Backstage-提现"},
     *      summary="二次审核拒绝",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function secondReject(Withdrawal $withdrawal)
    {
        if (!$withdrawal->checkSecondVerifyStatus()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->process();

        return $this->response->item($withdrawal->refresh(), new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/escalate",
     *      operationId="backstage.withdrawals.escalate",
     *      tags={"Backstage-提现"},
     *      summary="提升RM审核",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="escalate_reason", type="integer", description="提升理由"),
     *                  required={"escalate_reason"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function escalate(Withdrawal $withdrawal, WithdrawalRequest $request)
    {
        if (!$withdrawal->isPending()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->escalate($request->escalate_reason);

        # 关闭前端表单
        $withdrawal->closeForm();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/rm_approve",
     *      operationId="backstage.withdrawals.rm_approve",
     *      tags={"Backstage-提现"},
     *      summary="rm审核通过",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function rmApprove(Withdrawal $withdrawal)
    {
        if (!$withdrawal->isEscalate()) {
            return $this->response->error('status error.', 422);
        }

        $withdrawal->pending();

        # 关闭前端表单
        $withdrawal->closeForm();

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/withdrawals/{withdrawal}/remark",
     *      operationId="backstage.withdrawals.remark",
     *      tags={"Backstage-提现"},
     *      summary="添加备注",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="remark", type="integer", description="备注"),
     *                  required={"remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Withdrawal"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function remark(Withdrawal $withdrawal, WithdrawalRequest $request)
    {
        $withdrawal->update([
            'remark' => $request->remark,
        ]);

        return $this->response->item($withdrawal, new WithdrawalTransformer());
    }

    /**
     * @OA\POST(
     *      path="/backstage/withdrawals/{withdrawal}/images",
     *      operationId="backstage.withdrawals.images.store",
     *      tags={"Backstage-提现"},
     *      summary="上传凭证",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="image", description="图片", type="file", format="file"),
     *                  required={"image"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Image"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function image(Withdrawal $withdrawal, WithdrawalRequest $request, ImageUploadHandler $uploader)
    {
        $user = $this->user;

        $result = $uploader->save($request->image, $user->id);

        $image = ImageRepository::create($user, $result['path'], $request->image->getClientOriginalName(), $withdrawal);

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Delete(
     *      path="/backstage/withdrawals/{withdrawal}/images/{image}",
     *      operationId="backstage.withdrawals.images.delete",
     *      tags={"Backstage-提现"},
     *      summary="移除凭证",
     *      @OA\Parameter(name="withdrawal", in="path", description="提现id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="image", in="path", description="凭证id", @OA\Schema(type="integer")),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function removeImage(Withdrawal $withdrawal, Image $image)
    {
        $withdrawal->images()->where('id', $image->id)->delete();

        return $this->response->noContent();
    }

    /**
     * 检查claim admin权限
     *
     * @param Withdrawal $withdrawal
     */
    public function checkClaimAdmin(Withdrawal $withdrawal)
    {
        if (WithdrawalRepository::isClaimAdmin($withdrawal, $this->user)) {
            return $this->returnClaimAdminResponse();
        }
    }

    /**
     * 返回claim admin权限
     */
    public function returnClaimAdminResponse()
    {
        return $this->response->error('You do not have permission to operate.', 422);
    }

    /**
     * @OA\Get(
     *     path="/backstage/withdrawals/{withdrawal}/logs",
     *     operationId="backstage.Withdrawals.logs",
     *     tags={"Backstage-提现"},
     *     summary="提现操作",
     *     @OA\Parameter(name="Withdrawal", in="path", description="提现id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="请求成功"),
     *     @OA\Response(response=401, description="授权不通过"),
     *     @OA\Response(response=422, description="验证错误"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function logIndex(Withdrawal $withdrawal)
    {
        $logs = WithdrawalRepository::getAudits($withdrawal, true);
        $preLog = null;
        $firstAccessLog = null;
        $firstHoldLog = null;
        $firstReleaseLog = null;

        foreach ($logs as $index => &$log) {

            if ('hold' == $log['action']) {
                if ($firstHoldLog) {
                    unset($logs[$index]);
                    continue;
                } else {
                    $firstHoldLog = $log;
                }
            }

            if ('release hold' == $log['action']) {
                if ($firstReleaseLog) {
                    unset($logs[$index]);
                    continue;
                } else {
                    $firstReleaseLog = $log;
                }
            }

            if (!empty($preLog)) {
                $log['interval'] = Carbon::parse($log['created_at'])->diffInSeconds(Carbon::parse($preLog['created_at']));
            } else {
                $log['interval'] = Carbon::parse($log['created_at'])->diffInSeconds($withdrawal->created_at);
            }
            unset($log['description']);
            $preLog = $log;
        }

        return $this->response->array([
            'data' => $logs
        ]);
    }

    /**
     * @OA\Get(
     *      path="backstage/export/withdrawals/logs",
     *      operationId="backstage.withdrawals.export.logs",
     *      tags={"Backstage-提现"},
     *      summary="提现 Log 报表",
     *      @OA\Response(response=200, description="请求成功"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function exportWithdrawalLogs(Request $request)
    {
        # 过滤条件.
        $filter = remove_null($request->filter);

        if (empty($filter)) {
            $message = 'Please enter at least one query condition.';
            return $this->response->error($message, 422)->withHeader('X-header-message', $message);
        }

        $headings = [
            'Admin',
            'Transaction ID',
            'Currency',
            'Member Code',
            'Transaction Date',
            'Fund out Account',    # 公司银行账户
            'Member Account No. ', # 会员提现账户
            'Amount',
            'Status',              # 提现单最后的状态
            'Processing Time',     # Admin操作的总时间 - Holding Time - Escalating Time  （Admin操作的总时间是有效记录中，Admin第一次Access到最后一次不是Hold或者Release Hold或者Escalate的时间）
            'Holding Time',        # Hold到Release Hold的时间，如果不是同一个人，两个都计算
            'Escalating Time',     # 从Escalate到最后的时间
            'Remarks',
        ];

        $withdrawalList = QueryBuilder::for (Withdrawal::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::exact('order_no'),
                Filter::exact('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status'),
            ])
            ->latest()
            ->get(['id', 'order_no', 'currency', 'user_name', 'created_at', 'records', 'account_no', 'amount', 'status', 'records', 'remark'])
            ->toArray();

        $withdrawalIds = array(); // 提款的id
        $adminIds = array(); // 参与审核人员的id


        if (empty($withdrawalList)) {
            $message = 'no data need to be downloaded!';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        // 获取这些提款记录的id
        if (!empty($withdrawalList)) {
            $withdrawalList = collect($withdrawalList)->keyBy('id');
            $withdrawalIds = $withdrawalList->keys();

            $withdrawalList = $withdrawalList->toArray();
        }

        // 获取这些提款记录的操作日志  此处whereIn有命中索引 并非全表扫描.
        if (!empty($withdrawalIds)) {
            $withdrawalAuditLogList = QueryBuilder::for(Audit::class)
                ->whereIn('auditable_id', $withdrawalIds)
                ->where('auditable_type', 'App\Models\Withdrawal')
                ->where('user_type', 'App\Models\Admin')
                ->get(['id','auditable_id as withdrawal_id', 'new_values', 'user_id', 'created_at'])->toArray();

            $adminIds = array_unique(array_column($withdrawalAuditLogList, 'user_id'));

            foreach ($withdrawalAuditLogList as $withdrawalAuditLogInfo) {
                $user_id = $withdrawalAuditLogInfo['user_id'];
                $withdrawalId = $withdrawalAuditLogInfo['withdrawal_id'];

                if (empty($withdrawalList[$withdrawalId]['admins'])) {
                    $withdrawalList[$withdrawalId]['admins'][] = $user_id;
                } elseif (!empty($withdrawalList[$withdrawalId]['admins']) && !in_array($user_id,$withdrawalList[$withdrawalId]['admins'])) {
                    $withdrawalList[$withdrawalId]['admins'][] = $user_id;
                }

                // 该笔订单涉及的所有操作日志.
                $withdrawalList[$withdrawalId]['actions'][] = $withdrawalAuditLogInfo;
            }
        }

        // 获取admin信息.
        $adminInfoList = array();
        if(!empty($adminIds)) {
            $adminInfoList = QueryBuilder::for(Admin::query())
                ->whereIn('id', $adminIds)
                ->get()->toArray();
            $adminInfoList = collect($adminInfoList)->keyBy('id');
            $adminInfoList = $adminInfoList->toArray();
        }

        // 待导出数据
        $exportData = array();

        if (!empty($withdrawalList) && !empty($adminIds)) { // 操作人存在  提款记录存在
            foreach ($withdrawalList as $withdrawInfo) { // 计入kpi的提款日志.
                $withdrawalTime = $withdrawInfo['created_at'];
                $orderNo = $withdrawInfo['order_no'];
                $currency = $withdrawInfo['currency'];
                $userName = $withdrawInfo['user_name'];
                $tDate = $withdrawInfo['created_at'];
                $accountNo = $withdrawInfo['account_no'];
                $amount = $withdrawInfo['amount'];
                $status = $withdrawInfo['status'];
                $remarks = $withdrawInfo['remark'];
                if (!empty($withdrawInfo['records'])) {
                    $accounts = collect($withdrawInfo['records'])->pluck('company_bank_account_code')->toArray();
                    $fundOutAccount = implode(',', $accounts);
                } else {
                    $fundOutAccount = '';
                }

                if (!empty($withdrawInfo['admins']) && !empty($withdrawInfo['actions'])) { // 该笔订单下的操作日志

                    $admins = $withdrawInfo['admins']; // 参与该笔订单的所有工作人员.
                    $actionLogs = $withdrawInfo['actions']; // 该笔订单所有的操作日志.

                    foreach ($admins as $userId) { // 参与操作该笔订单的工作人员.
                        $adminName = !empty($adminInfoList[$userId]) ? $adminInfoList[$userId]['name'] : "";

                        $time = WithdrawalLogsRepository::calculateTime($actionLogs,$userId,$withdrawalTime);

                        $processing_time = $time['processing_time'];
                        $holding_time = $time['holding_time'];
                        $escalating_time = $time['escalating_time'];

                        $exportData[] = array(
                            'admin' => $adminName,
                            'order_no' => $orderNo,
                            'currency' => $currency,
                            'name' => $userName,
                            't_date' => $tDate,
                            'fund' => $fundOutAccount,
                            'account_no' => $accountNo,
                            'amount' => thousands_number($amount),
                            'status' => $status,
                            'processing_time' => $processing_time,
                            'holding_time' => $holding_time,
                            'escalating_time' => $escalating_time,
                            'remarks' => $remarks,
                        );
                    }
                }
            }
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'message.xlsx');
    }
}