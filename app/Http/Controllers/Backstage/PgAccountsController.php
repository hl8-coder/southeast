<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PgAccountRequest;
use App\Models\CompanyBankAccount;
use App\Models\CompanyBankAccountTransaction;
use App\Models\PaymentGroup;
use App\Models\PgAccount;
use App\Models\PgAccountRemark;
use App\Models\PgAccountReport;
use App\Models\PgAccountTransaction;
use App\Repositories\CompanyBankAccountTransactionRepository;
use App\Repositories\PgAccountTransactionRepository;
use App\Repositories\ReportRepository;
use App\Transformers\PaymentPlatformTransformer;
use App\Transformers\PgAccountRemarkTransformer;
use App\Transformers\PgAccountReportTransformer;
use App\Transformers\PgAccountTransactionTransformer;
use App\Transformers\PgAccountTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class PgAccountsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/pg_account_management",
     *      operationId="backstage.pg_account_management.index",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="pg account 列表",
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询变动额度 开始时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询变动额度 结束时间", @OA\Schema(type="date")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PgAccount"),
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
        $startAt = !empty($request->filter['start_at']) ? $request->filter['start_at'] : null;
        $endAt   = !empty($request->filter['end_at']) ? $request->filter['end_at'] : null;

        $pgAccounts = QueryBuilder::for(PgAccount::class)
                ->allowedFilters([
                    Filter::exact('status'),
                    Filter::scope('currency'),
                    Filter::scope('start_at'),
                    Filter::scope('end_at'),
                ])
                ->with(['paymentPlatform'])

                ->paginate($request->per_page);

        $totalBalance = QueryBuilder::for(PgAccount::class)
            ->allowedFilters([
                Filter::exact('status'),
                Filter::scope('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
            ])->sum('current_balance');

        $info = [['key' => 'total_balance', 'value' => thousands_number($totalBalance)]];

        $pgAccountSumReportQuery = PgAccountReport::query();

        if ($startAt) {
            $pgAccountSumReportQuery->where('date', '>=', date('Y-m-d',strtotime($startAt)));
        }

        if ($endAt) {
            $pgAccountSumReportQuery->where('date', '>=', date('Y-m-d',strtotime($endAt)));
        }
        $pgAccountSumReports = $pgAccountSumReportQuery->groupBy('payment_platform_code')
            ->get([
                'payment_platform_code',
                DB::raw('sum(deposit) as deposit'),
                DB::raw('sum(deposit_fee) as deposit_fee'),
                DB::raw('sum(withdraw) as withdraw'),
                DB::raw('sum(withdraw_fee) as withdraw_fee'),
            ]);

        foreach ($pgAccounts as $pgAccount) {
            $sum = $pgAccountSumReports->where('payment_platform_code', $pgAccount->payment_platform_code)->first();
            if ($sum) {
                $pgAccount->deposit      = $sum->deposit;
                $pgAccount->deposit_fee  = $sum->deposit_fee;
                $pgAccount->withdraw     = $sum->withdraw;
                $pgAccount->withdraw_fee = $sum->withdraw_fee;
            } else {
                $pgAccount->deposit      = 0;
                $pgAccount->deposit_fee  = 0;
                $pgAccount->withdraw     = 0;
                $pgAccount->withdraw_fee = 0;
            }
        }

        if (!empty($request->order)) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            if ('desc' == $sortType) {
                $pgAccounts = $pgAccounts->sortByDesc(implode('_', $order));
            } else {
                $pgAccounts = $pgAccounts->sortBy(implode('_', $order));
            }
        }


        $pgAccounts = $this->paginate($request, $pgAccounts);

        $paymentGroup = PaymentGroup::all()->toArray();

        return $this->response->paginator($pgAccounts, new PgAccountTransformer('index', ['payment_group' => $paymentGroup]))->setMeta(['info' => $info]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/pg_account_management/{pg_account}",
     *      operationId="backstage.pg_account_management.show",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="pg account 详情",
     *      @OA\Parameter(
     *         name="pg_account",
     *         in="path",
     *         description="pg account id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/PgAccount"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show(PgAccount $pgAccount)
    {
        $paymentPlatform = $pgAccount->paymentPlatform;
        return $this->response->item($paymentPlatform, new PaymentPlatformTransformer('pg'));
    }

    /**
     * @OA\Post(
     *      path="/backstage/pg_account_management/{pg_account}/remark",
     *      operationId="backstage.pg_account_management.remarks",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="Pg Account 执行备注",
     *      @OA\Parameter(
     *         name="pg_account",
     *         in="path",
     *         description="pg account id",
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
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"remark"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/PgAccountRemark"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function remark(PgAccount $pgAccount, PgAccountRequest $request)
    {
        $remark = PgAccountRemark::add($pgAccount->payment_platform_code, $request->remark, $this->user->name);

        return $this->response->item($remark, new PgAccountRemarkTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/pg_account_management/{pg_account}/remarks",
     *      operationId="backstage.pg_account_management.remarks.index",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="Pg Account 备注列表",
     *      @OA\Parameter(
     *         name="pg_account",
     *         in="path",
     *         description="pg account id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PgAccountRemark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function remarkIndex(PgAccount $pgAccount, Request $request)
    {
        $remarks = $pgAccount->remarks()->latest()->paginate($request->per_page);

        return $this->response->paginator($remarks, new PgAccountRemarkTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/pg_account_management/adjust",
     *      operationId="backstage.pg_account_management.adjust",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="调整第三方余额",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="account_id", type="integer", description="pg account id"),
     *                  @OA\Property(property="is_income", type="integer", description="是否是进账 1:进账 0:出账"),
     *                  @OA\Property(property="amount", type="number", description="调整金额"),
     *                  @OA\Property(property="fee", type="number", description="手续费"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  @OA\Property(property="txn_id", type="integer", description="关联txnId"),
     *                  required={"company_bank_account_id", "is_income", "amount", "remark"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/PgAccountTransaction"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function adjust(PgAccountRequest $request)
    {

        $pgAccount = PgAccount::find($request->account_id);
        $fee = !empty($request->fee) ? $request->fee : 0;

        if (!$request->is_income && $pgAccount->current_balance < $request->amount + $fee) {
            $message = "balance not enough";
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        try {
            $transaction = DB::transaction(function() use ($request,$pgAccount,$fee) {

                return PgAccountTransactionRepository::add(
                    $pgAccount,
                    PgAccountTransaction::TYPE_ADJUSTMENT,
                    $request->is_income,
                    $request->amount,
                    $fee,
                    $this->user->name,
                    '',
                    $request->txn_id,
                    '',
                    '',
                    !empty($request->txn_id) ? "Related Txn ID:" . $request->txn_id ." | ".$request->remark : $request->remark
                );
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($transaction, new PgAccountTransactionTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/pg_account_management/internal_transfer",
     *      operationId="backstage.pg_account_management.internal_transfer",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="从第三方支付通道转账到公司银行卡",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="from_account_id", type="integer", description="第三方通道id"),
     *                  @OA\Property(property="to_account_id", type="integer", description="入账公司银行卡id"),
     *                  @OA\Property(property="amount", type="number", description="调整金额"),
     *                  @OA\Property(property="fee", type="number", description="手续费"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"from_account_id", "to_account_id", "amount", "fee", "remark"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function internalTransfer(PgAccountRequest $request)
    {
        # 检查金额是否足够.
        $fromAccount = PgAccount::find($request->from_account_id);

        $fee = !empty($request->fee) ? (float)$request->fee : 0;

        if ($fromAccount->current_balance < $request->amount + $fee) {
            $message = "balance not enough";
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        try {
            DB::transaction(function() use ($request, $fromAccount,$fee) {

                $toAccount   = CompanyBankAccount::find($request->to_account_id);

                # 添加出账
                $fromTransaction = PgAccountTransactionRepository::add(
                    $fromAccount,
                    PgAccountTransaction::TYPE_COMPANY_WITHDRAWAL,
                    false,
                    $request->amount,
                    $fee,
                    $this->user->name,
                    '',
                    null,
                    $fromAccount->payment_platform_code,
                    $toAccount->code,
                    $request->remark
                );

                # 添加入账
                $toTransaction = CompanyBankAccountTransactionRepository::add(
                    $toAccount,
                    CompanyBankAccountTransaction::TYPE_ADJUSTMENT,
                    true,
                    $request->amount,
                    0,
                    $this->user->name,
                    '',
                    null,
                    $fromAccount->payment_platform_code,
                    $toAccount->code,
                    $request->remark
                );
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/pg_account_management/reports",
     *      operationId="backstage.pg_account_management.reports.index",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="第三方支付通道报表",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="date")),
     *       @OA\Response(
     *          response=200,
     *          description="获取成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PgAccountReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function reportIndex(Request $request)
    {

        $startAt = !empty($request->filter['start_at']) ? $request->filter['start_at'] : null;
        $endAt = !empty($request->filter['end_at']) ? $request->filter['end_at'] : null;

        $reports = ReportRepository::getPgAccountReports($request->per_page, $startAt, $endAt);


        return $this->response->paginator($reports, new PgAccountReportTransformer());
    }


    /**
     * @OA\Patch(
     *      path="/backstage/pg_account_management/{pg_account}",
     *      operationId="backstage.pg_account_management.update",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="更新第三方通道",
     *      @OA\Parameter(
     *         name="pg_account",
     *         in="path",
     *         description="pg账号id",
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
     *                  @OA\Property(property="customer_id", type="string", description="商户id"),
     *                  @OA\Property(property="username", type="string", description="Username"),
     *                  @OA\Property(property="password", type="string", description="Password"),
     *                  @OA\Property(property="email", type="string", format="email", description="email"),
     *                  @OA\Property(property="email_password", type="string", description="关联app"),
     *                  @OA\Property(property="otp", type="integer", description="关联密码"),
     *                  required={"remark"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/CompanyBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(PgAccount $pgAccount, PgAccountRequest $request)
    {
        $data = remove_null($request->except('remark'));

        if (!empty($data['customer_id'])) {
            $paymentPlatform = $pgAccount->paymentPlatform;
            $paymentPlatform->customer_id = $data['customer_id'];
            $paymentPlatform->save();
            unset($data['customer_id']);
        }

        $pgAccount->update($data);


        PgAccountRemark::add($pgAccount->payment_platform_code, $request->remark, $this->user->name);

        $paymentPlatform = $pgAccount->paymentPlatform;

        return $this->response->item($paymentPlatform, new PaymentPlatformTransformer('pg'));
    }

}
