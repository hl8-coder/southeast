<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Requests\Backstage\BankTransactionRequest;
use App\Imports\BankTransactionsImport;
use App\Services\BankTransactionService;
use App\Http\Controllers\BackstageController;
use App\Models\BankTransaction;
use App\Transformers\BankTransactionAuditTransformer;
use App\Transformers\BankTransactionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use DB;

class BankTransactionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/bank_transactions",
     *      operationId="backstage.bank_transactions.index",
     *      tags={"Backstage-银行交易记录"},
     *      summary="银行交易记录列表",
     *      @OA\Parameter(name="filter[id]", in="query", description="id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[order]", in="query", description="statement id", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[bank_code]", in="query", description="银行code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[fund_in_account]", in="query", description="入账公司银行卡辨识码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[description]", in="query", description="描述", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[fund_in_account]", in="query", description="公司银行卡code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[account_no]", in="query", description="入账账号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[transaction_start_at]", in="query", description="交易查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[transaction_end_at]", in="query", description="交易查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[housekeep]", in="query", description="是否删除", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/BankTransaction"),
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
        $this->removeThousandsNumber($request, 'amount');

        $ORM = QueryBuilder::for(BankTransaction::class);

        if(!$request->input('filter.status')) {
            $ORM->where('status', BankTransaction::STATUS_NOT_MATCH);
        }

        if(!$request->input('filter.housekeep')) {
            $ORM->whereNull('deleted_at');
        }

        $transactions = $ORM->allowedFilters([
                            Filter::exact('id'),
                            Filter::exact('order_no'),
                            Filter::exact('currency'),
                            Filter::exact('bank_code'),
                            Filter::exact('fund_in_account'),
                            'description',
                            'account_no',
                            Filter::scope('status'),
                            Filter::scope('transaction_start_at'),
                            Filter::scope('transaction_end_at'),
                            Filter::scope('amount'),
                            Filter::scope('housekeep'),
                        ])
                        ->latest('transaction_date')
                        ->latest('transaction_at')
                        ->latest('id')
                        ->paginate($request->per_page);

        return $this->response->paginator($transactions, new BankTransactionTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/bank_transactions/{bank_transaction}",
     *      operationId="backstage.bank_transactions.show",
     *      tags={"Backstage-银行交易记录"},
     *      summary="银行交易记录详情",
     *      @OA\Parameter(
     *         name="bank_transaction",
     *         in="path",
     *         description="银行交易记录ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/BankTransaction"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function show(BankTransaction $bankTransaction)
    {
        return $this->response->item($bankTransaction, new BankTransactionTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/bank_transactions/{bank_transaction}/credit",
     *      operationId="backstage.bank_transactions.credit",
     *      tags={"Backstage-银行交易记录"},
     *      summary="修改credit的值",
     *      @OA\Parameter(
     *         name="bank_transaction",
     *         in="path",
     *         description="银行交易记录id",
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
     *                  @OA\Property(property="credit", type="float", description="修改后的值"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"credit", "remark"}
     *              ),
     *          ),
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
     * )
     */
    public function updateCredit(BankTransaction $bankTransaction, BankTransactionRequest $request)
    {
        $bankTransaction->update([
            'credit'        => $request->credit,
            'remark'        => $request->remark,
            'admin_name'    => $this->user->name,
        ]);

        return $this->response->item($bankTransaction, new BankTransactionTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/bank_transactions/{bank_transaction}",
     *      operationId="backstage.bank_transactions.destroy",
     *      tags={"Backstage-银行交易记录"},
     *      summary="软删除银行交易记录",
     *      @OA\Parameter(
     *         name="bank_transaction",
     *         in="path",
     *         description="银行交易记录id",
     *         @OA\Schema(
     *             type="integer"
     *         )
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
    public function destroy(BankTransaction $bankTransaction)
    {
        if ($bankTransaction->isMatched()) {
            return $this->response->error('Matched is not housekeep', 422);
        }

        if ($bankTransaction->isDeleted()) {
            $bankTransaction->update(['deleted_at' => null]);
        } else {
            $bankTransaction->update(['deleted_at' => now()]);
        }

        return $this->response->noContent();
    }

    /**
     * @OA\Post(
     *      path="/backstage/bank_transactions/excel",
     *      operationId="backstage.bank_transactions.excel",
     *      tags={"Backstage-银行交易记录"},
     *      summary="导入银行交易记录excel表",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="excel", type="string", description="excel文件", format="file"),
     *                  @OA\Property(property="fund_in_account", type="string", description="公司银行卡辨识码"),
     *                  @OA\Property(property="is_force", type="boolean", description="是否强行导入"),
     *                  required={"excel", "currency", "fund_in_account"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="导入成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="cache_key", description="缓存key", type="string"),
     *          ),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function importExcel(BankTransactionRequest $request, BankTransactionService $service)
    {
        $isForce = $request->input('is_force', false);
        try {
            $cacheKey = $service->import($request->file('excel'), $request->fund_in_account, 0, 1);
            DB::statement("update `bank_transactions` set order_no = concat('5',LPAD(id, 10, 0)) where order_no = '';");
        } catch (\Exception $e) {
            return $this->response->error('Error format.', 422);
        }

        # 触发自动充值
        (new BankTransactionService())->batchAddAutoDeposit($request->fund_in_account);

        return $this->response->array([
            'cache_key' => $cacheKey,
        ]);
    }

    /**
     * @OA\Post(
     *      path="/backstage/bank_transactions/text",
     *      operationId="backstage.bank_transactions.text",
     *      tags={"Backstage-银行交易记录"},
     *      summary="导入银行交易记录text",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="multipart/form-data",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="text", type="string", description="复制的内容"),
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="fund_in_account", type="string", description="公司银行卡辨识码"),
     *                  @OA\Property(property="last_balance", type="number", description="最后余额"),
     *                  @OA\Property(property="is_force", type="boolean", description="是否强行导入"),
     *                  required={"excel", "currency", "fund_in_account"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="导入成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="cache_key", description="缓存key", type="string"),
     *          ),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function importText(BankTransactionRequest $request, BankTransactionService $service)
    {
        $isForce = $request->input('is_force', false);

        # 检查last balance是否匹配
        if (!$isForce && !BankTransactionsImport::isMatchLastTransaction($request->fund_in_account, $request->last_balance)) {
            return $this->response->error('-1', 415);
        }

        try {
            $cacheKey = $service->importText(
                $request->input('text'),
                $request->fund_in_account,
                $request->last_balance,
                $isForce
            );
            DB::statement("update `bank_transactions` set order_no = concat('5',LPAD(id, 10, 0)) where order_no = '';");
        } catch (\Exception $e) {
            return $this->response->error('Err format.', 422);
        }

        # 触发自动充值
        (new BankTransactionService())->batchAddAutoDeposit($request->fund_in_account);

        return $this->response->array([
            'cache_key' => $cacheKey,
        ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/bank_transactions/duplicate_transactions",
     *      operationId="backstage.bank_transactions.duplicate_transactions",
     *      tags={"Backstage-银行交易记录"},
     *      summary="获取excel表重复记录",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="cache_key", type="string", description="缓存key"),
     *                  required={"cache_key"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(
     *                  @OA\Property(property="fund_in_account", type="string", description="公司银行卡"),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *                  @OA\Property(property="debit", type="number", description="取款金额"),
     *                  @OA\Property(property="credit", type="number", description="存款金额"),
     *                  @OA\Property(property="balance", type="number", description="余额"),
     *                  @OA\Property(property="transaction_date", type="string", description="交易日期", format="date"),
     *              ),
     *          ),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function getDuplicateTransactions(Request $request)
    {
        $cacheKey = $request->input('cache_key', '');

        $result = [];

        if (Cache::has($cacheKey)) {
            $result = Cache::get($cacheKey);
        }

        return $this->response->array($result);
    }

    /**
     * @OA\Delete(
     *      path="/backstage/bank_transactions/duplicate_transactions",
     *      operationId="backstage.bank_transactions.destroy_duplicate_transactions",
     *      tags={"Backstage-银行交易记录"},
     *      summary="移除余额重复的银行交易记录",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="fund_in_account", type="string", description="公司银行卡"),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *                  @OA\Property(property="debit", type="number", description="取款金额"),
     *                  @OA\Property(property="credit", type="number", description="存款金额"),
     *                  @OA\Property(property="balance", type="number", description="余额"),
     *                  @OA\Property(property="transaction_date", type="string", description="交易日期", format="date"),
     *                  required={"fund_in_account", "description", "debit", "credit", "balance", "transaction_date"}
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
    public function destroyDuplicateTransactions(BankTransactionRequest $request)
    {
        BankTransaction::query()->where('fund_in_account', $request->fund_in_account)
                        ->where('debit', $request->debit)
                        ->where('credit', $request->credit)
                        ->where('balance', $request->balance)
                        ->where('transaction_date', $request->transaction_date)
                        ->where('description', $request->description)
                        ->forceDelete();

        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *      path="/backstage/bank_transactions/{bank_transaction}/audit",
     *      operationId="backstage.bank_transactions.audit",
     *      tags={"Backstage-银行交易记录"},
     *      summary="银行交易记录操作日志",
     *      @OA\Parameter(name="bank_transaction", in="path", description="银行交易ID", @OA\Schema(type="integer")),
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
    public function audit(BankTransaction $bankTransaction, Request $request)
    {
        $audits = $bankTransaction->audits()->latest()->paginate($request->per_page);

        return $this->response->paginator($audits, new BankTransactionAuditTransformer());
    }

}
