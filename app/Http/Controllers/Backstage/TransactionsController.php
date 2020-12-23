<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Models\Transaction;
use App\Transformers\TransactionTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class TransactionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_account_transactions?include=user",
     *      operationId="backstage.transactions.index",
     *      tags={"Backstage-会员账户"},
     *      summary="会员帐变记录列表",
     *      @OA\Parameter(name="user_name", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_id", in="query", description="用户ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="type_group", in="query", description="帐变大分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="type", in="query", description="帐变类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="status", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="currency", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="order_no", in="query", description="订单号", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Transaction"),
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
        $filters = [
            Filter::scope('user_name'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::exact('user_id'),
            Filter::exact('order_no'),
            Filter::exact('type_group'),
            Filter::exact('is_income'),
            Filter::exact('type'),
            Filter::exact('status'),
            Filter::exact('currency'),
        ];

        $transactions = QueryBuilder::for(Transaction::class)
                        ->allowedFilters($filters)
                        ->isUser()
                        ->latest()
                        ->paginate($request->per_page);

        $totalAmount = QueryBuilder::for(Transaction::class)
                        ->allowedFilters($filters)
                        ->isUser()
                        ->sum('amount');

        $uniqueUserCount = QueryBuilder::for(Transaction::class)
                        ->allowedFilters($filters)
                        ->isUser()
                        ->count(DB::raw('DISTINCT user_id'));

        return $this->response->paginator($transactions, new TransactionTransformer())->setMeta([
            'info' => [
                'Total Amount' => thousands_number($totalAmount),
                'Unique Member' => $uniqueUserCount,
            ],
        ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_account_transactions/export",
     *      operationId="backstage.transactions.index",
     *      tags={"Backstage-会员账户"},
     *      summary="会员帐变记录列表-导出",
     *      @OA\Parameter(name="user_name", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="user_id", in="query", description="用户ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="type_group", in="query", description="帐变大分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="type", in="query", description="帐变类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="status", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="currency", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="order_no", in="query", description="订单号", @OA\Schema(type="string")),
     *      @OA\Response(response=200, description="请求成功"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function exportTransaction(Request $request)
    {
        $exportData = array(); // 等导出数据.

        $headings = [
            'Transaction ID',
            'Category',
            'Member Code',
            'Currency',
            'Type',
            'Amount',
            'Before Balance',
            'After Balance',
            'Transaction Date',
            'Status',
        ];

        QueryBuilder::for(Transaction::class)
            ->allowedFilters([
                Filter::scope('user_name'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('user_id'),
                Filter::exact('order_no'),
                Filter::exact('type_group'),
                Filter::exact('is_income'),
                Filter::exact('type'),
                Filter::exact('status'),
                Filter::exact('currency'),
            ])
            ->isUser()
            ->latest()
            ->chunk(5000,function ($transactions) use (&$exportData) {
                foreach ($transactions as $transaction) {
                    $exportData[] = [
                        'transaction_id' => $transaction->order_no,
                        'category' => transfer_show_value($transaction->type_group, Transaction::$typeGroups),
                        'member_code' => !empty($transaction->user->name) ? $transaction->user->name : "-",
                        'currency' => $transaction->currency,
                        'type'  => transfer_show_value($transaction->is_income, Transaction::$isIncomes),
                        'amount'             => $transaction->amount,
                        'before_balance'     => $transaction->before_balance,
                        'after_balance'      => $transaction->after_balance,
                        'created_at' => convert_time($transaction->created_at),
                        'display_status'     => transfer_show_value($transaction->status, Transaction::$statuses),
                    ];
                }
            });

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'payment_translation.xlsx');

    }
}
