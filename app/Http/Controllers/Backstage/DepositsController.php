<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Handlers\ImageUploadHandler;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\DepositRequest;
use App\Jobs\TransactionProcessJob;
use App\Models\BankTransaction;
use App\Models\PaymentPlatform;
use App\Models\Transaction;
use App\Models\Deposit;
use App\Models\DepositLog;
use App\Models\User;
use App\Repositories\DepositRepository;
use App\Repositories\ImageRepository;
use App\Repositories\RemarkRepository;
use App\Services\DepositService;
use App\Services\TransactionService;
use App\Services\DepositBacksideService;
use App\Transformers\DepositLogTransformer;
use App\Transformers\DepositTransformer;
use App\Transformers\ImageTransformer;
use App\Transformers\RemarkTransformer;
use App\Transformers\BankTransactionTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class DepositsController extends BackstageController
{
    /**
     * @OA\Get(
     *     path="/backstage/deposits?include=user,paymentPlatform",
     *      operationId="backstage.users.deposits.index",
     *     tags={"Backstage-充值"},
     *     summary="充值历史记录",
     *     @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[admin_name]", in="query", description="管理员名称", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[order_no]", in="query", description="订单ID", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[start_at]", in="query", description="充值开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="充值结束日期", @OA\Schema(type="string")),
     *     @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function index(DepositRequest $request)
    {
        $condition = [
            'user_name'  => Filter::scope('user_name'),
            'order_no'   => Filter::exact('order_no'),
            'start_at'   => Filter::scope('start_at'),
            'status'     => Filter::exact('status'),
            'end_at'     => Filter::scope('end_at'),
            'admin_name' => Filter::scope('admin_name'),
            'is_agent'   => Filter::exact('is_agent')->ignore([true, false, null]),
        ];

        $userName = $request->input('filter.user_name');
        $isAgent  = $request->input('filter.is_agent', false);

        $pagination = QueryBuilder::for(Deposit::class)
            ->allowedFilters(array_values($condition))
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                return $query->where('name', $userName)
                    ->where('is_agent', $isAgent);
            })
            ->latest()
            ->paginate($request->per_page);

        $status = array_keys(Deposit::$statues);
        array_push($status, null);

        $condition['status'] = Filter::exact('status')->ignore($status);

        $amount = QueryBuilder::for(Deposit::class)
            ->allowedFilters(array_values($condition))
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                return $query->where('name', $userName)
                    ->where('is_agent', $isAgent);
            })
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->sum('arrival_amount');

        $total['amount'] = thousands_number($amount);

        $total['transactions'] = QueryBuilder::for(Deposit::class)
            ->allowedFilters(array_values($condition))
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                return $query->where('name', $userName)
                    ->where('is_agent', $isAgent);
            })
            ->count();

        return $this->response->paginator($pagination, new DepositTransformer())->setMeta(['total' => $total]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/{deposit}?include=user,accessLogs,operationLogs,userBank,userAccount,userDeposits,bankTransaction,images,userInfo",
     *      operationId="backstage.users.show",
     *      tags={"Backstage-充值"},
     *      summary="充值详情",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function show(Deposit $deposit)
    {
        DepositLog::add($this->user()->name, $deposit->id, DepositLog::TYPE_ACCESS);

        return $this->response->item($deposit, new DepositTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/open_deposit?include=user,paymentPlatform",
     *      operationId="backstage.users.deposits.open_deposit.index",
     *      tags={"Backstage-充值"},
     *      summary="所有充值单",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function openDeposit(Request $request)
    {
        # 取得Open
        $deposit = DepositRepository::getOpenDeposit($request);

        # 設定分頁
        $deposits = QueryBuilder::for($deposit)
            ->allowedFilters([
                Filter::exact('currency'),
            ])
            ->latest('updated_at')
            ->paginate($request->per_page);

        return $this->response->paginator($deposits, new DepositTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/fast_deposit?include=user,paymentPlatform",
     *      operationId="backstage.users.deposits.fast_deposit.index",
     *      tags={"Backstage-充值"},
     *      summary="网银充值单",
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[fund_in_account]", in="query", description="收款帐号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[reference]", in="query", description="参考(NO./CURRENCY/MEMBERCODE/TRANSACTION DATE/TRANSACTION ID ETC)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="充值开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="充值结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[order_no]", in="query", description="交易id", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function fastDeposit(Request $request)
    {
        $condition = [
            Filter::scope('status'),
            Filter::exact('currency'),
            Filter::exact('fund_in_account'),
            Filter::exact('online_banking_channel'),
            Filter::scope('reference'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('user_name'),
            Filter::exact('order_no'),
        ];

        # 取得Open
        $deposit = DepositRepository::getFastDeposit($request);

        # 設定分頁
        $deposits = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->paginate($request->per_page);

        # 获取总共成功的金额
        $total = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->sum('arrival_amount');

        # 获取总条数
        $total_txn = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->count();

        # unique member
        $unique_member = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->distinct('user_id')
            ->count('user_id');

        $total = thousands_number($total);

        return $this->response->paginator($deposits, new DepositTransformer())->setMeta(['total' => $total, 'total_txn' => $total_txn, 'unique_member' => $unique_member]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/gateway?include=user,paymentPlatform",
     *      operationId="backstage.users.deposits.gateway.index",
     *      tags={"Backstage-充值"},
     *      summary="第三方充值单",
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[payment_platform_id]", in="query", description="Channel", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[reference]", in="query", description="参考(NO./CURRENCY/MEMBERCODE/TRANSACTION DATE/TRANSACTION ID ETC)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="充值开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="充值结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[order_no]", in="query", description="交易id", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function gateway(Request $request)
    {

        $condition = [
            Filter::scope('status'),
            Filter::exact('currency'),
            Filter::exact('payment_platform_id'),
            Filter::scope('reference'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('user_name'),
            Filter::exact('order_no'),
        ];

        # 取得Open
        $deposit = DepositRepository::getGateway($request);

        # 設定分頁
        $deposits = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->paginate($request->per_page);

        $total = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->sum('arrival_amount');

        $total = thousands_number($total);


        # 获取总条数
        $total_txn = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->count();

        # unique member
        $unique_member = QueryBuilder::for($deposit)
            ->allowedFilters($condition)
            ->distinct('user_id')
            ->count('user_id');


        return $this->response->paginator($deposits, new DepositTransformer())->setMeta(['total' => $total, 'total_txn' => $total_txn, 'unique_member' => $unique_member]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/advance_credit?include=user,paymentPlatform",
     *      operationId="backstage.users.deposits.advance_credit.index",
     *      tags={"Backstage-充值"},
     *      summary="可上分充值单",
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[reference]", in="query", description="参考(NO./CURRENCY/MEMBERCODE/TRANSACTION DATE/TRANSACTION ID ETC)", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="充值开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="充值结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[order_no]", in="query", description="交易id", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function advanceCredit(Request $request)
    {
        # 取得Open
        $deposit = DepositRepository::getAdvanceCredit($request);

        # 設定分頁
        $deposits = QueryBuilder::for($deposit)
            ->allowedFilters([
                Filter::scope('status'),
                Filter::exact('tag'),
                Filter::exact('currency'),
                Filter::scope('payment_platform_id'),
                Filter::scope('reference'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('user_name'),
                Filter::exact('order_no'),
            ])
            ->paginate($request->per_page);

        return $this->response->paginator($deposits, new DepositTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/user/{user_name}?include=user",
     *      operationId="backstage.users.deposits.index",
     *      tags={"Backstage-充值"},
     *      summary="会员充值记录",
     *      @OA\Parameter(
     *         name="user_name",
     *         in="path",
     *         description="会员帐号",
     *         @OA\Schema(
     *             type="string"
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
     *              @OA\Items(ref="#/components/schemas/Deposit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function byUser($user_name, Request $request)
    {
        # 取得会员的充值记录
        $deposit = Deposit::whereHas('user', function ($q) use ($user_name) {
            $q->where("name", $user_name);
        });

        # 設定分頁
        $deposits = QueryBuilder::for($deposit)
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($deposits, new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/amount_detail",
     *      operationId="backstage.deposits.amount_detail.update",
     *      tags={"Backstage-充值"},
     *      summary="更改金額細節",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
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
     *                  @OA\Property(property="fund_in_account", type="string", description="实际收款帐号"),
     *                  @OA\Property(property="receive_amount", type="integer", description="实际收款金额"),
     *                  @OA\Property(property="arrival_amount", type="integer", description="实际上分金额"),
     *                  @OA\Property(property="bank_fee", type="integer", description="银行手续费"),
     *                  @OA\Property(property="reimbursement_fee", type="integer", description="报销费"),
     *                  required={"fund_in_account","receive_amount","arrival_amount","bank_fee","reimbursement_fee"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateAmountDetail(Deposit $deposit, DepositRequest $request)
    {
        # 新建状态才能修改
        if ($deposit->status != Deposit::STATUS_CREATED) {
            return $this->response->error('The status has been pending.', 422);
        }

        if ($paymentPlatform = PaymentPlatform::findByCode($request->fund_in_account)) {
            $deposit->payment_platform_id = $paymentPlatform->id;
        }

        $deposit->fund_in_account   = $request->fund_in_account;
        $deposit->arrival_amount    = $request->arrival_amount;
        $deposit->receive_amount    = $request->receive_amount;
        $deposit->bank_fee          = $request->bank_fee;
        $deposit->reimbursement_fee = $request->reimbursement_fee;

        # 同步更新关闭充值流水条件
        $deposit->turnover_closed_value = $request->arrival_amount + $request->bank_fee;

        (new DepositBacksideService())->updateAmountDetail($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/remarks",
     *      operationId="backstage.deposits.remarks.update",
     *      tags={"Backstage-充值"},
     *      summary="更改备注",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
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
     *                  @OA\Property(property="remarks", type="string", description="内部备注"),
     *                  required={"remarks"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updateRemarks(Deposit $deposit, DepositRequest $request)
    {
        if ($deposit->update(['remarks' => $request->remarks])) {
            DepositLog::add($this->user()->name, $deposit->id, DepositLog::TYPE_REMARKS);
        }

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\POST(
     *      path="/backstage/deposits/{deposit}/receipt",
     *      operationId="backstage.deposits.receipt",
     *      tags={"Backstage-充值"},
     *      summary="上传凭证",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
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
    public function receipt(Deposit $deposit, DepositRequest $request, ImageUploadHandler $uploader)
    {
        $user = $this->user;

        $result = $uploader->save($request->image, $user->id);

        $image = ImageRepository::create($user, $result['path'], $request->image->getClientOriginalName(), $deposit);

        $deposit->update([
            'receipt_count' => DB::raw('receipt_count + 1'),
            'receipt_img_created_at' => now(),
        ]);

        DepositLog::add($this->user()->name, $deposit->id, DepositLog::TYPE_RECEIPT_UPLOAD);

        return $this->response->item($image, new ImageTransformer())->setStatusCode(201);
    }

    /**
     * @OA\DELETE(
     *      path="/backstage/deposits/{deposit}/receipt/{image_id}",
     *      operationId="backstage.deposits.receipt.delete",
     *      tags={"Backstage-充值"},
     *      summary="刪除凭证",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值id",
     *         @OA\Schema(
     *             type="integer"
     *         ),
     *         name="image_id",
     *         in="path",
     *         description="图片id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="请求成功",
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function receiptDelete(Deposit $deposit, $image_id)
    {
        $deposit->images()->where("id", $image_id)->delete();

        $deposit->update([
            'receipt_count' => $deposit->images()->count(),
        ]);

        DepositLog::add($this->user()->name, $deposit->id, DepositLog::TYPE_RECEIPT_REMOVE);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/reject",
     *      operationId="backstage.deposits.reject",
     *      tags={"Backstage-充值"},
     *      summary="拒绝",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
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
     *                  @OA\Property(property="reject_reason", type="string", description="拒绝原因"),
     *                  required={"reject_reason", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function reject(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkReject($error)) {
            return $this->response->error($error, 422);
        }

        $deposit->reject_reason = $request->reject_reason;

        app(DepositBacksideService::class)->reject($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/cancel",
     *      operationId="backstage.deposits.cancel",
     *      tags={"Backstage-充值"},
     *      summary="取消",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function cancel(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkCancel($error)) {
            return $this->response->error($error, 422);
        }

        app(DepositBacksideService::class)->cancel($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/approve_changes",
     *      operationId="backstage.deposits.approve_changes",
     *      tags={"Backstage-充值"},
     *      summary="二次批淮",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approveChanges(Deposit $deposit, DepositRequest $request, DepositBacksideService $depositBacksideService)
    {
        if (!$deposit->checkApproveChanges($error)) {
            return $this->response->error($error, 422);
        }

        $transactions = [];

        try {
            DB::transaction(function () use ($deposit, &$transactions, $depositBacksideService) {
                $transactions = $depositBacksideService->approveChanges($this->user(), $deposit);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/hold",
     *      operationId="backstage.deposits.hold",
     *      tags={"Backstage-充值"},
     *      summary="保留",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
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
     *                  @OA\Property(property="hold_reason", type="string", description="保留原因"),
     *                  required={"reject_reason", "remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function hold(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkHold($error)) {
            return $this->response->error($error, 422);
        }

        $deposit->hold_reason = $request->hold_reason;

        app(DepositBacksideService::class)->hold($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/release_hold",
     *      operationId="backstage.deposits.release_hold",
     *      tags={"Backstage-充值"},
     *      summary="取消保留",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function releaseHold(Deposit $deposit)
    {
        if (!$deposit->checkReleaseHold($error)) {
            return $this->response->error($error, 422);
        }

        app(DepositBacksideService::class)->releaseHold($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/request_advance",
     *      operationId="backstage.deposits.request_advance",
     *      tags={"Backstage-充值"},
     *      summary="上分类型选择",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function requestAdvance(Deposit $deposit)
    {
        if (!$deposit->checkRequestAdvance($error)) {
            return $this->response->error($error, 422);
        }

        app(DepositBacksideService::class)->requestAdvance($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/deposits/bank_transactions/{order_no}",
     *      operationId="backstage.deposits.bank_transactions.show",
     *      tags={"Backstage-充值"},
     *      summary="银行交易记录详情",
     *      @OA\Parameter(
     *         name="order_no",
     *         in="path",
     *         description="订单号",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/BankTransaction"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function bankTransaction($order_no)
    {
        $bankTransaction = BankTransaction::where("order_no", $order_no)->first();

        if ($bankTransaction) {

            return $this->response->item($bankTransaction, new BankTransactionTransformer());
        } else {
            return $this->response->error('no transaction', 422);
        }
    }


    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/match/bank_transactions/{bank_transaction}",
     *      operationId="backstage.deposits.receive.bank_transaction",
     *      tags={"Backstage-充值"},
     *      summary="充值领取银行交易记录",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="bank_transaction",
     *         in="path",
     *         description="银行交易id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function match(Deposit $deposit, BankTransaction $bankTransaction)
    {
        $transactions = [];

        try {
            DB::transaction(function () use ($deposit, $bankTransaction, &$transactions) {
                $transactions = (new DepositBacksideService)->match($this->user(), $deposit, $bankTransaction);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        $this->updateDepositTime($deposit->refresh(), $deposit->user);

        return $this->response->item($deposit, new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/unmatch",
     *      operationId="backstage.deposits.unmatch.bank_transaction",
     *      tags={"Backstage-充值"},
     *      summary="取消充值领取银行交易记录",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function unmatch(Deposit $deposit)
    {
        (new DepositBacksideService)->unmatch($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/final_approve",
     *      operationId="backstage.deposits.final_approve",
     *      tags={"Backstage-充值"},
     *      summary="zpay, mpay, linepay部分上分使用",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function finalApprove(Deposit $deposit)
    {
        $transactions = [];

        try {
            DB::transaction(function () use ($deposit, &$transactions) {
                $transactions = (new DepositBacksideService)->finalApprove($this->user(), $deposit);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        $this->updateDepositTime($deposit->refresh(), $deposit->user);

        return $this->response->item($deposit, new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/approve",
     *      operationId="backstage.deposits.approve",
     *      tags={"Backstage-充值"},
     *      summary="批淮",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approve(Deposit $deposit, DepositRequest $request, DepositBacksideService $depositBacksideService)
    {
        if (!$deposit->checkApprove($error)) {
            return $this->response->error($error, 422);
        }

        $transactions = [];

        $bankTransactionId = $request->bank_transaction_id;

        try {
            DB::transaction(function () use ($deposit, &$transactions, $bankTransactionId, $depositBacksideService) {

                # 自動match
                if ($bankTransactionId && $bankTransaction = BankTransaction::find($bankTransactionId)) {
                    $transactions = $depositBacksideService->match($this->user(), $deposit, $bankTransaction);
                } else {
                    $transactions = $depositBacksideService->approve($this->user(), $deposit);
                }
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        $this->updateDepositTime($deposit->refresh(), $deposit->user);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/approve_adv",
     *      operationId="backstage.deposits.approve_adv",
     *      tags={"Backstage-充值"},
     *      summary="请求全额上分",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approveAdv(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkApproveAdv($error, true)) {
            return $this->response->error($error, 422);
        }

        app(DepositBacksideService::class)->approveAdv($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/approve_partial",
     *      operationId="backstage.deposits.approve_partial",
     *      tags={"Backstage-充值"},
     *      summary="请求部份上分",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
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
     *                  @OA\Property(property="partial_amount", type="string", description="部份上分金額"),
     *                  required={"partial_amount"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approvePartial(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkApprovePartial($error, $request->partial_amount)) {
            return $this->response->error($error, 422);
        }

        $deposit->partial_amount = $request->partial_amount;

        app(DepositBacksideService::class)->approvePartial($this->user(), $deposit);


        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/revert_action",
     *      operationId="backstage.deposits.revert_action",
     *      tags={"Backstage-充值"},
     *      summary="取消请求",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function revertAction(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkRevertAction($error)) {
            return $this->response->error($error, 422);
        }

        app(DepositBacksideService::class)->revertAction($this->user(), $deposit);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/approve_advance_credit",
     *      operationId="backstage.deposits.approve_advance_credit",
     *      tags={"Backstage-充值"},
     *      summary="完整上分",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approveAdvanceCredit(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkApproveAdvanceCredit($error)) {
            return $this->response->error($error, 422);
        }

        $transactions = [];

        try {
            DB::transaction(function () use ($deposit, &$transactions) {
                $transactions = app(DepositBacksideService::class)->approveAdvanceCredit($this->user(), $deposit);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        $this->updateDepositTime($deposit->refresh(), $deposit->user);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/deposits/{deposit}/approve_partial_advance_credit",
     *      operationId="backstage.deposits.approve_partial_advance_credit",
     *      tags={"Backstage-充值"},
     *      summary="部份上分",
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Deposit"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function approvePartialAdvanceCredit(Deposit $deposit, DepositRequest $request)
    {
        if (!$deposit->checkApprovePartialAdvanceCredit($error)) {
            return $this->response->error($error, 422);
        }

        $transactions = [];

        try {
            DB::transaction(function () use ($deposit, &$transactions) {
                $transactions = app(DepositBacksideService::class)->approvePartialAdvanceCredit($this->user(), $deposit);
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        $this->updateDepositTime($deposit->refresh(), $deposit->user);

        return $this->response->item($deposit->refresh(), new DepositTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/users/{user}/deposits/remarks",
     *      operationId="backstage.users.deposits.remarks.index",
     *      tags={"Backstage-充值"},
     *      summary="获取会员最近两年的remark",
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
     *      path="/backstage/deposits/{deposit}/lose",
     *      operationId="backstage.deposits.lose",
     *      tags={"Backstage-充值"},
     *      summary="遗失",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Parameter(
     *         name="deposit",
     *         in="path",
     *         description="充值ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="no content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function lose(Deposit $deposit, DepositRequest $request)
    {
        app(DepositBacksideService::class)->lose($this->user(), $deposit, $request->remark);

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *     path="/backstage/deposits/{deposit}/logs",
     *     operationId="backstage.deposits.logs",
     *     tags={"Backstage-充值"},
     *     summary="充值管理元操作",
     *     @OA\Parameter(name="deposit", in="path", description="充值id", required=true, @OA\Schema(type="integer")),
     *     @OA\Response(response=200, description="请求成功"),
     *     @OA\Response(response=401, description="授权不通过"),
     *     @OA\Response(response=422, description="验证错误"),
     *     security={
     *         {"bearer": {}}
     *     }
     * )
     */
    public function logIndex(Deposit $deposit)
    {
        $logs = $deposit->logs->sortBy('created_at');

        $preLog          = null;
        $firstAccessLog  = null;
        $firstHoldLog    = null;
        $firstReleaseLog = null;

        foreach ($logs as $index => $log) {

            if (DepositLog::TYPE_ACCESS == $log->type) {
                if ($firstAccessLog) {
                    unset($logs[$index]);
                    continue;
                } else {
                    $firstAccessLog = $log;
                }
            }

            if (DepositLog::TYPE_HOLD == $log->type) {
                if ($firstHoldLog) {
                    unset($logs[$index]);
                    continue;
                } else {
                    $firstHoldLog = $log;
                }
            }

            if (DepositLog::TYPE_RELEASE_HOLD == $log->type) {
                if ($firstReleaseLog) {
                    unset($logs[$index]);
                    continue;
                } else {
                    $firstReleaseLog = $log;
                }
            }

            if (!empty($preLog)) {
                $log->interval = $log->created_at->diffInSeconds($preLog->created_at);
            } else {
                $log->interval = $log->created_at->diffInSeconds($deposit->created_at);
            }

            $preLog = $log;
        }

        return $this->response->collection($logs, new DepositLogTransformer());
    }

    /**
     * @OA\Get(
     *      path="backstage/export/deposit/log",
     *      operationId="backstage.export.deposit.log",
     *      tags={"Backstage-充值"},
     *      summary="充值 Log 报表",
     *      @OA\Response(response=200, description="请求成功"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function exportDepositLogs(Request $request)
    {
        # 时间计算问题： 从admin第一次Access开始算起，admin必须操作除Access以外的其他操作，才算有效的admin。
        # 1. 从数据表中找到所有的不是access的有效操作记录，提取被操作过的表单id，
        # 2. 根据有效操作记录的admin，找到他的Access时间，计算出processing_time, holding_time, un_match_time
        # 特殊情况：某一个admin执行了Hold操作，另一个admin执行了release hold 操作，这个间隔时间两个admin都需要计算
        # Un Match Time 只计算Un 的人从上次match到他Un match的时间，这个时间不计算在processing_time

        if (empty($request->filter['start_at']) || empty($request->filter['end_at'])) {
            $message = 'You must  input start date and  end date';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        $start_at = $request->filter['start_at'];
        $end_at = $request->filter['end_at'];

        if (Carbon::parse($start_at)->diffInSeconds($end_at) > 3600 * 24 * 31) { // 限制查询30天.
            $message = 'Export data for up to 1 month !';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }


        // 获取对应时间段所有存款记录.
        $depositList = QueryBuilder::for(Deposit::class)
            ->where(function ($query) use ($request) {
                if (isset($request->filter['start_at']) && !empty($request->filter['start_at'])) {
                    $query->where('created_at', '>=', $request->filter['start_at']);
                }
                if (isset($request->filter['end_at']) && !empty($request->filter['end_at'])) {
                    $query->where('created_at', '<=', $request->filter['end_at']);
                }
                if (isset($request->filter['currency']) && !empty($request->filter['currency'])) {
                    $query->where('currency', $request->filter['currency']);
                }
            })
            ->orderByDesc('id')
            ->get(['id', 'order_no', 'remarks', 'created_at']);

        if (empty($depositList)) {
            $message = 'no data need to be downloaded!';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        $depositIds = [];
        if (!empty($depositList)) {
            $depositIds = $depositList->pluck('id')->toArray();
        }

        // 待导出数据
        $exportData = [];

        // 获取这些提款记录的操作日志  此处whereIn有命中索引 并非全表扫描.
        if (!empty($depositIds)) {
            $depositActionLogList = DepositLog::query()
                ->whereIn('deposit_id', $depositIds)
                ->get(['id','type', 'deposit_id', 'admin_name', 'created_at']);

            foreach ($depositList as $deposit) {
                $logs = $depositActionLogList->where('deposit_id', $deposit->id)->sortBy('created_at');

                $preLog          = null;
                $firstAccessLog  = null;
                $firstHoldLog    = null;
                $firstReleaseLog = null;

                foreach ($logs as $index => $log) {

                    if (DepositLog::TYPE_ACCESS == $log->type) {
                        if ($firstAccessLog) {
                            unset($logs[$index]);
                            continue;
                        } else {
                            $firstAccessLog = $log;
                        }
                    }

                    if (DepositLog::TYPE_HOLD == $log->type) {
                        if ($firstHoldLog) {
                            unset($logs[$index]);
                            continue;
                        } else {
                            $firstHoldLog = $log;
                        }
                    }

                    if (DepositLog::TYPE_RELEASE_HOLD == $log->type) {
                        if ($firstReleaseLog) {
                            unset($logs[$index]);
                            continue;
                        } else {
                            $firstReleaseLog = $log;
                        }
                    }

                    if (!empty($preLog)) {
                        $log->interval = $log->created_at->diffInSeconds($preLog->created_at);
                    } else {
                        $log->interval = $log->created_at->diffInSeconds($deposit->created_at);
                    }

                    $preLog = $log;
                }

                foreach ($logs as $log) {
                    $exportData[] = [
                        'order_no'          => $deposit->order_no,
                        'admin'             => $log->admin_name,
                        'created_at'        => convert_time($log->created_at),
                        'interval'          => $log->interval,
                        'type'              => transfer_show_value($log->type, DepositLog::$types),
                        'remarks'           => $deposit->remarks,
                    ];
                }
            }
        }

        $headings = [
            'Transaction ID',
            'Update By',
            'Update At',
            'Interval',
            'Type',
            'Remarks',
        ];


        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'message.xlsx');
    }


    public function callBack($code, Request $request)
    {
        $paymentPlatform = PaymentPlatform::findByCode($code);

        if (!$paymentPlatform) {
            $this->response->errorInternal();
        }

        $data    = $request->all();
        $service = new DepositService($paymentPlatform->id);
        Log::stack(['deposit_log'])->info($code . ' call back data:' . json_encode($data));
        try {
            $transaction = DB::transaction(function () use ($service, $data) {

                $deposit = $service->callBack($data);

                if ($deposit) {
                    $user = $deposit->user;

                    # 帐变记录
                    $transaction = (new TransactionService())->addTransaction(
                        $user,
                        $deposit->arrival_amount,
                        Transaction::TYPE_THIRD_PARTY_SAVE,
                        $deposit->id,
                        $deposit->order_no
                    );

                    return $transaction;
                }

                return null;
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        if ($transaction) {
            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }
    }

    public function updateDepositTime(Deposit $deposit, User $user)
    {
        if ($deposit->status == Deposit::STATUS_RECHARGE_SUCCESS && ($user->first_deposit_at == '' || $user->first_deposit_at == null)) {
            $user->updateFirstDepositTime($deposit->deposit_at);
        }
    }

}
