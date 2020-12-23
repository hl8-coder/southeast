<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\Bank;
use App\Models\CompanyBankAccount;
use App\Models\CompanyBankAccountRemark;
use App\Models\CompanyBankAccountTransaction;
use App\Models\PaymentGroup;
use App\Repositories\CompanyBankAccountRepository;
use App\Repositories\CompanyBankAccountTransactionRepository;
use App\Repositories\ReportRepository;
use App\Repositories\ImageRepository;
use App\Transformers\AuditTransformer;
use App\Transformers\CompanyBankAccountRemarkTransformer;
use App\Transformers\CompanyBankAccountReportTransformer;
use App\Transformers\CompanyBankAccountTransactionTransformer;
use App\Transformers\CompanyBankAccountTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\Backstage\CompanyBankAccountRequest;

class CompanyBankAccountsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/company_bank_accounts",
     *      operationId="backstage.company_bank_accounts.index",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡列表",
     *      @OA\Parameter(name="filter[payment_group_id]", in="query", description="支付组别", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[bank_code]", in="query", description="银行辨识码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CompanyBankAccount"),
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
        $paymentGroup = PaymentGroup::all()->toArray();

        $ORM = CompanyBankAccount::query();

        if ($request->order) {
            $order    = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $filters = [
            Filter::exact('payment_group_id'),
            Filter::exact('type'),
            Filter::exact('status'),
            Filter::exact('bank_code'),
            Filter::exact('currency'),
        ];

        $companyBankAccounts = QueryBuilder::for($ORM)
            ->allowedFilters($filters)
            ->defaultSort('created_at')
            ->paginate($request->per_page);

        $totalBalance = QueryBuilder::for(CompanyBankAccount::class)->allowedFilters($filters)->sum('balance');

        return $this->response->paginator($companyBankAccounts, new CompanyBankAccountTransformer('index', $paymentGroup))->setMeta([
            'info' => ['total_balance' => $totalBalance],
        ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/company_bank_accounts/{company_bank_account}?include=paymentGroup,images",
     *      operationId="backstage.company_bank_accounts.show",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡详情",
     *      @OA\Parameter(
     *         name="company_bank_account",
     *         in="path",
     *         description="公司银行卡id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/CompanyBankAccount"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show(CompanyBankAccount $companyBankAccount)
    {
        $paymentGroup = PaymentGroup::all()->toArray();
        return $this->response->item($companyBankAccount, new CompanyBankAccountTransformer('show', $paymentGroup));
    }

    /**
     * @OA\Get(
     *      path="/backstage/company_bank_accounts/code",
     *      operationId="backstage.company_bank_accounts.code.show",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡详情",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string", description="公司银行卡code"),
     *                  required={"code"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/CompanyBankAccount"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function showByCode(Request $request)
    {
        $paymentGroup = PaymentGroup::all()->toArray();

        if (empty($request->code)) {
            return $this->response->noContent();
        }

        $companyBankAccount = CompanyBankAccount::query()->where('code', $request->code)->first();
        return $this->response->item($companyBankAccount, new CompanyBankAccountTransformer('show', $paymentGroup));
    }

    /**
     * @OA\Post(
     *      path="/backstage/company_bank_accounts",
     *      operationId="backstage.company_bank_accounts.store",
     *      tags={"Backstage-公司银行卡"},
     *      summary="添加公司银行卡",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="account_name", type="string", description="开户人姓名"),
     *                  @OA\Property(property="account_no", type="string", description="开户账号"),
     *                  @OA\Property(property="payment_group_id", type="string", description="支付组别id"),
     *                  @OA\Property(property="type", type="string", description="类型"),
     *                  @OA\Property(property="bank_id", type="integer", description="银行id"),
     *                  @OA\Property(property="branch", type="string", description="分行"),
     *                  @OA\Property(property="province", type="string", description="省"),
     *                  @OA\Property(property="app_related", type="string", description="关联app"),
     *                  @OA\Property(property="first_balance", type="number", description="初始余额"),
     *                  @OA\Property(property="balance", type="number", description="余额"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="phone_asset", type="string", description="电话编号"),
     *                  @OA\Property(property="safe_key_pass", type="string", description="app密码"),
     *                  @OA\Property(property="otp", type="integer", description="关联密码"),
     *                  @OA\Property(property="user_name", type="string", description="登录账号"),
     *                  @OA\Property(property="password", type="string", description="登录密码"),
     *                  @OA\Property(property="min_balance", type="number", description="最小金额"),
     *                  @OA\Property(property="max_balance", type="number", description="最大金额"),
     *                  @OA\Property(property="daily_fund_out_limit", type="number", description="日出款限制"),
     *                  @OA\Property(property="daily_fund_in_limit", type="number", description="日存款限制"),
     *                  @OA\Property(property="daily_transaction_limit", type="number", description="日交易次数限制"),
     *                  @OA\Property(property="image", type="string", description="图片id"),
     *                  required={"account_name", "account_no", "payment_group_id", "type", "bank_id", "code", "province", "branch", "user_name", "password"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/CompanyBankAccount"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(CompanyBankAccountRequest $request)
    {
        $data               = remove_null($request->all());
        $bank               = Bank::findByCache($data['bank_id']);
        $data['currency']   = $bank->currency;
        $data['bank_code']  = $bank->code;
        $data['admin_name'] = $this->user->name;
        $companyBankAccount = CompanyBankAccount::query()->create($data);

        # 关联图片
        if ($companyBankAccount->image) {
            $companyBankAccount->images()->delete();
            $ids = explode(',', $companyBankAccount->image);
            ImageRepository::updatePatch($this->user, $ids, $companyBankAccount);
        }
        $paymentGroup = PaymentGroup::all()->toArray();
        return $this->response->item($companyBankAccount, new CompanyBankAccountTransformer('', $paymentGroup))->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/company_bank_accounts/{company_bank_account}",
     *      operationId="backstage.company_bank_accounts.update",
     *      tags={"Backstage-公司银行卡"},
     *      summary="更新公司银行卡",
     *      @OA\Parameter(
     *         name="company_bank_account",
     *         in="path",
     *         description="公司银行卡id",
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
     *                  @OA\Property(property="payment_group_id", type="string", description="支付组别id"),
     *                  @OA\Property(property="type", type="string", description="类型"),
     *                  @OA\Property(property="branch", type="string", description="分行"),
     *                  @OA\Property(property="province", type="string", description="省"),
     *                  @OA\Property(property="app_related", type="string", description="关联app"),
     *                  @OA\Property(property="password", type="string", description="登录密码"),
     *                  @OA\Property(property="phone", type="string", description="电话号码"),
     *                  @OA\Property(property="phone_asset", type="string", description="电话编号"),
     *                  @OA\Property(property="safe_key_pass", type="string", description="app密码"),
     *                  @OA\Property(property="otp", type="integer", description="关联密码"),
     *                  @OA\Property(property="min_balance", type="number", description="最小金额"),
     *                  @OA\Property(property="max_balance", type="number", description="最大金额"),
     *                  @OA\Property(property="daily_fund_out_limit", type="number", description="日出款限制"),
     *                  @OA\Property(property="daily_fund_in_limit", type="number", description="日存款限制"),
     *                  @OA\Property(property="daily_transaction_limit", type="number", description="日交易次数限制"),
     *                  @OA\Property(property="image", type="string", description="图片id"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
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
    public function update(CompanyBankAccount $companyBankAccount, CompanyBankAccountRequest $request)
    {
        $data = remove_null($request->except('remark'));

        # 关联图片
        if ($companyBankAccount->image != $request->image) {
            $companyBankAccount->images()->delete();
            $ids = explode(',', $request->image);
            ImageRepository::updatePatch($this->user, $ids, $companyBankAccount);
        }

        $companyBankAccount->update($data);

        foreach ($data as $key => $value) {
            $category = $key;
        }

        CompanyBankAccountRemark::add($companyBankAccount->id, $request->remark, $category, $this->user->name);
        $paymentGroup = PaymentGroup::all()->toArray();
        return $this->response->item($companyBankAccount, new CompanyBankAccountTransformer('', $paymentGroup));
    }

    /**
     * @OA\Get(
     *      path="/backstage/company_bank_accounts/{company_bank_account}/audits",
     *      operationId="backstage.company_bank_accounts.audits",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡修改记录",
     *      @OA\Parameter(
     *         name="company_bank_account",
     *         in="path",
     *         description="公司银行卡id",
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
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function audits(CompanyBankAccount $companyBankAccount, Request $request)
    {
        $field = $request->field;

        $audits = $companyBankAccount->audits()->whereRaw("FIND_IN_SET(?, tags)", $field)->get();

        foreach ($audits as &$audit) {
            $audit->new_value = CompanyBankAccountRepository::transformAudit($field, $audit->new_values[$field]);
            $audit->old_value = CompanyBankAccountRepository::transformAudit($field, $audit->old_values[$field]);
        }

        return $this->response->collection($audits, new AuditTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/company_bank_accounts/{company_bank_account}/remarks",
     *      operationId="backstage.company_bank_accounts.remarks",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡备注",
     *      @OA\Parameter(
     *         name="company_bank_account",
     *         in="path",
     *         description="公司银行卡id",
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
     *          @OA\JsonContent(ref="#/components/schemas/CompanyBankAccountRemark"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function remark(CompanyBankAccount $companyBankAccount, CompanyBankAccountRequest $request)
    {
        $category = null;
        $remark = CompanyBankAccountRemark::add($companyBankAccount->id, $request->remark, $category, $this->user->name);

        return $this->response->item($remark, new CompanyBankAccountRemarkTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/company_bank_accounts/{company_bank_account}/remarks",
     *      operationId="backstage.company_bank_accounts.remarks.index",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡备注列表",
     *      @OA\Parameter(
     *         name="company_bank_account",
     *         in="path",
     *         description="公司银行卡id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CompanyBankAccountRemark"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function remarkIndex(CompanyBankAccount $companyBankAccount, Request $request)
    {
        $remarks = $companyBankAccount->remarks()->latest()->paginate($request->per_page);

        return $this->response->paginator($remarks, new CompanyBankAccountRemarkTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/company_bank_accounts/adjust",
     *      operationId="backstage.company_bank_accounts.adjust",
     *      tags={"Backstage-公司银行卡"},
     *      summary="调整银行卡余额",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="account_id", type="integer", description="公司银行卡id"),
     *                  @OA\Property(property="is_income", type="integer", description="是否是进账 1:进账 0:出账"),
     *                  @OA\Property(property="amount", type="number", description="调整金额"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"company_bank_account_id", "is_income", "amount", "remark"},
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
    public function adjust(CompanyBankAccountRequest $request)
    {
        $companyBankAccount = CompanyBankAccount::find($request->account_id);

        try {

            $transaction = DB::transaction(function() use ($request, $companyBankAccount) {


                $remark = '[' . transfer_show_value($request->reason, CompanyBankAccountTransaction::$reasons) . '] ' . $request->remark;

                return CompanyBankAccountTransactionRepository::add(
                    $companyBankAccount,
                    CompanyBankAccountTransaction::TYPE_ADJUSTMENT,
                    $request->is_income,
                    $request->amount,
                    $request->fee,
                    $this->user->name,
                    '',
                    null,
                    '',
                    '',
                    $remark,
                    $request->reason
                );
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($transaction, new CompanyBankAccountTransactionTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/company_bank_accounts/internal_transfer",
     *      operationId="backstage.company_bank_accounts.internal_transfer",
     *      tags={"Backstage-公司银行卡"},
     *      summary="内部转账",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="from_account_id", type="integer", description="出账公司银行卡id"),
     *                  @OA\Property(property="to_account_id", type="integer", description="入账公司银行卡id"),
     *                  @OA\Property(property="amount", type="number", description="调整金额"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"from_account_id", "to_account_id", "amount", "remark"},
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
    public function internalTransfer(CompanyBankAccountRequest $request)
    {
        $fromAccount = CompanyBankAccount::find($request->from_account_id);
        $toAccount   = CompanyBankAccount::find($request->to_account_id);

        try {
            DB::transaction(function() use ($request, $fromAccount, $toAccount) {

                $remark  = '[Internal Transfer] From ' . $fromAccount->code . ' To ' . $toAccount->code . '.' . $request->remark;
                # 添加出账
                $fromTransaction = CompanyBankAccountTransactionRepository::add(
                    $fromAccount,
                    CompanyBankAccountTransaction::TYPE_ADJUSTMENT,
                    false,
                    $request->amount,
                    $request->fee,
                    $this->user->name,
                    '',
                    null,
                    $fromAccount->code,
                    $toAccount->code,
                    $remark
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
                    $fromAccount->code,
                    $toAccount->code,
                    $remark
                );
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/company_bank_accounts/buffer_transfer",
     *      operationId="backstage.company_bank_accounts.buffer_transfer",
     *      tags={"Backstage-公司银行卡"},
     *      summary="buffer转账",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="account_id", type="integer", description="公司银行卡id"),
     *                  @OA\Property(property="is_income", type="integer", description="是否是进账 1:进账 0:出账"),
     *                  @OA\Property(property="amount", type="number", description="调整金额"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"company_bank_account_id", "is_income", "amount", "remark"},
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
    public function bufferTransfer(CompanyBankAccountRequest $request)
    {
        try {
            $transaction = DB::transaction(function () use ($request) {

                $companyBankAccount = CompanyBankAccount::find($request->account_id);

                return CompanyBankAccountTransactionRepository::add(
                    $companyBankAccount,
                    CompanyBankAccountTransaction::TYPE_BUFFER,
                    $request->is_income,
                    $request->amount,
                    0,
                    $this->user->name,
                    '',
                    null,
                    '',
                    '',
                    $request->remark
                );
            });
        } catch (\Exception $e) {
            return $this->response->error($e->getMessage(), 422);
        }

        return $this->response->item($transaction, new CompanyBankAccountTransactionTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/company_bank_accounts/reports",
     *      operationId="backstage.company_bank_accounts.reports.index",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡报表",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="date")),
     *       @OA\Response(
     *          response=200,
     *          description="获取成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CompanyBankAccountReport"),
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
        $startAt = isset($request->filter['start_at']) ? $request->filter['start_at'] : null;
        $endAt   = isset($request->filter['end_at']) ? $request->filter['end_at'] : null;

        $reports = ReportRepository::getCompanyBankAccountReports($request->per_page, $startAt, $endAt);


        return $this->response->paginator($reports, new CompanyBankAccountReportTransformer());
    }

}
