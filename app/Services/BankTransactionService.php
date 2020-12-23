<?php

namespace App\Services;

use App\Jobs\AutoDepositJob;
use App\Models\BankTransaction;
use App\Models\CompanyBankAccount;
use App\Models\Deposit;
use App\Models\PaymentPlatform;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;

class BankTransactionService
{
    /**
     * 导入excel表
     *
     * @param $file
     * @param $fundInAccount
     * @return string
     */
    public function import($file, $fundInAccount, $lastBalance, $isForce)
    {
        $companyBankAccount = CompanyBankAccount::findByCode($fundInAccount);

        $cacheKey = 'bank_transactions_' . $companyBankAccount->code . '_' . time();

        $classStr = 'App\\Imports\\' . strtoupper($companyBankAccount->bank->code) . 'BankTransactionsImport';
        Excel::import(new $classStr($companyBankAccount, $companyBankAccount->currency, $cacheKey, $lastBalance, $isForce), $file);

        return $cacheKey;
    }

    /**
     * 导入text
     *
     * @param   string      $text               需要导入的文本
     * @param   string      $fundInAccount      导入账号
     * @param   float       $lastBalance        最后余额
     * @param   boolean     $isForce            是否强制导入
     * @return  string
     * @throws
     */
    public function importText($text, $fundInAccount, $lastBalance, $isForce)
    {
        $companyBankAccount = CompanyBankAccount::findByCode($fundInAccount);
        $cacheKey = 'bank_transactions_' . $companyBankAccount->code . '_' . time();
        $classStr = 'App\\Imports\\' . strtoupper($companyBankAccount->bank->code) . 'BankTransactionsImport';
        $class = new $classStr($companyBankAccount, $companyBankAccount->currency, $cacheKey, $lastBalance, $isForce);
        $class->importText($text);

        return $cacheKey;
    }

    /**
     * 获取transaction
     *
     * @param Deposit $deposit
     * @return mixed
     */
    public function getTransaction(Deposit $deposit)
    {
        $classStr = 'App\\MatchRules\\' . strtoupper($deposit->companyBankAccount->bank->code) . 'MatchRule';
        $class = new $classStr();

        return BankTransaction::query()->where('status', BankTransaction::STATUS_NOT_MATCH)
            ->where('fund_in_account', $deposit->companyBankAccount->code)
            ->where('transaction_date', Carbon::parse($deposit->deposit_at)->format('Y-m-d'))
            ->where(function($query) use ($deposit, $class) {
                return $class->getTransactionQuery($deposit, $query);
            })
            ->orderBy('created_at')
            ->first();
    }

    /**
     * 批量添加充值到auto deposit流程
     *
     * @param $fundInAccount
     */
    public function batchAddAutoDeposit($fundInAccount)
    {
        $companyBankAccount = CompanyBankAccount::findByCode($fundInAccount);

        if (!$companyBankAccount->bank->is_auto_deposit) {
            return;
        }

        $deposits = Deposit::query()->where('payment_type', PaymentPlatform::PAYMENT_TYPE_BANKCARD)
                    ->where('status', Deposit::STATUS_CREATED)
                    ->where('fund_in_account', $fundInAccount)
                    ->where(function($query) {
                        $query->whereNull('auto_status')->orWhere('auto_status', Deposit::AUTO_STATUS_FAIL);
                    })
                    ->get();

        foreach ($deposits as $deposit) {
            dispatch(new AutoDepositJob($deposit))->onQueue('auto_deposit');
        }

        return;
    }
}