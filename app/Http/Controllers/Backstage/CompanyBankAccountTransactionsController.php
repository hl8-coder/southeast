<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\CompanyBankAccountTransaction;
use App\Transformers\CompanyBankAccountTransactionTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\ExcelTemplateExport;

class CompanyBankAccountTransactionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/company_bank_account_transactions",
     *      operationId="backstage.company_bank_account_transactions.index",
     *      tags={"Backstage-公司银行卡"},
     *      summary="公司银行卡帐变明细",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="date")),
     *      @OA\Parameter(name="filter[company_bank_account_code]", in="query", description="公司银行卡code", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CompanyBankAccountTransaction"),
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
        $transactions = QueryBuilder::for(CompanyBankAccountTransaction::class)
            ->allowedFilters(
                Filter::exact('company_bank_account_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($transactions, new CompanyBankAccountTransactionTransformer('index'));
    }

    public function exportBankAccountManagement(Request $request)
    {
        $headings = [
            'Date/ Time',
            'From Account',
            'To Account',
            'Member Code',
            'Fee',
            'Debit',
            'Credit',
            'Current Balance',
            'Processor',
            'Transaction ID',
            'Remark',
        ];

        $transactions = QueryBuilder::for(CompanyBankAccountTransaction::class)
            ->allowedFilters(
                Filter::exact('company_bank_account_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->latest()
            ->paginate($request->per_page);

        $exportData = [];

        foreach ($transactions as $transaction) {
            $exportData[] = [
            'created_at'            => convert_time($transaction->created_at),
            'from_account'          => $transaction->from_account,
            'to_account'            => $transaction->to_account,
            'user_name'             => $transaction->user_name,
            'fee'                   => thousands_number($transaction->fee),
            'debit'                 => $transaction->is_income ? 0 : $transaction->amount,
            'credit'                => $transaction->is_income ? $transaction->amount : 0,
            'after_balance'         => thousands_number($transaction->after_balance),
            'admin_name'            => $transaction->admin_name,
            'trace_id'              => $transaction->trace_id,
            'remark'                => $transaction->remark,
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'pg_account_transactions.xlsx');
    }
}
