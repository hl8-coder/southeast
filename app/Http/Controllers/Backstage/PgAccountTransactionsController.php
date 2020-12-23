<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\PgAccountTransaction;
use App\Transformers\PgAccountTransactionTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExcelTemplateExport;


class PgAccountTransactionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/pg_account_transactions",
     *      operationId="backstage.pg_account_transactions.index",
     *      tags={"Backstage-Pg Account Management"},
     *      summary="第三方支付通道帐变明细",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[payment_platform_code]", in="query", description="支付通道code", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PgAccountTransaction"),
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
        $transactions = QueryBuilder::for(PgAccountTransaction::class)
            ->allowedFilters(
                Filter::exact('payment_platform_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->with('deposit')
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($transactions, new PgAccountTransactionTransformer('backstage_index'));
    }

    public function exportPgAccountTransaction(Request $request)
    {
        $headings = [
            'Date/ Time',
            'From Account',
            'To Account',
            'Member Code',
            'Debit',
            'Credit',
            'Fee',
            'Current Balance',
            'Processor',
            'Transaction ID',
            'Remark',
        ];

        $transactions = QueryBuilder::for(PgAccountTransaction::class)
            ->allowedFilters(
                Filter::exact('payment_platform_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->with('deposit')
            ->latest()
            ->paginate($request->per_page);

        $exportData = [];

        foreach ($transactions as $transaction) {
            $exportData[] = [
            'created_at'            => convert_time($transaction->created_at),
            'from_account'          => $transaction->from_account,
            'to_account'            => $transaction->to_account,
            'user_name'             => $transaction->user_name,
            'debit'                 => $transaction->is_income ? 0 : thousands_number($transaction->amount),
            'credit'                => $transaction->is_income ? thousands_number($transaction->amount) : 0,
            'fee'                   => thousands_number($transaction->fee),
            'after_balance'         => thousands_number($transaction->after_balance),
            'admin_name'            => $transaction->admin_name,
            'trace_id'              => $transaction->trace_id,
            'remark'                => $transaction->remark,
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'pg_account_transactions.xlsx');
    }
}
