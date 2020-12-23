<?php
namespace App\Repositories;

use App\Models\CompanyBankAccount;
use App\Models\CompanyBankAccountTransaction;
use App\Services\ReportService;
use Illuminate\Support\Facades\Log;

class CompanyBankAccountTransactionRepository
{
    /**
     * 添加公司银行卡交易记录并更新银行卡余额
     *

     * @param   CompanyBankAccount    $companyBankAccount         公司银行卡
     * @param   integer               $type                       类型
     * @param   boolean               $isIncome                   是否是入账  true:入账 false:出账
     * @param   float                 $amount                     金额
     * @param   float                 $fee                        手续费
     * @param   string                $adminName                  操作人员(会员或者管理员)
     * @param   string                $userName                   会员名称
     * @param   integer               $traceId                    追踪id
     * @param   string                $fromAccount                来源银行卡
     * @param   string                $toAccount                  去向银行卡
     * @param   string                $remark                     备注
     * @param   string                $reason                     理由
     * @param   string                $orderNo                    订单号
     * @return  CompanyBankAccountTransaction
     * @throws
     */
    public static function add(
        CompanyBankAccount $companyBankAccount,
        $type,
        $isIncome,
        $amount,
        $fee,
        $adminName='',
        $userName='',
        $traceId=null,
        $fromAccount='',
        $toAccount='',
        $remark='',
        $reason=null,
        $orderNo=''
    )
    {
        $amount = abs($amount);
        $fee    = abs($fee);

        # 更新银行卡余额
        $companyBankAccount = $companyBankAccount->updateBalance($amount, $isIncome, $fee);

        # 创建交易记录
        $transaction = static::create(
            $companyBankAccount,
            $type,
            $isIncome,
            $amount,
            $fee,
            $adminName,
            $userName,
            $traceId,
            $fromAccount,
            $toAccount,
            $remark,
            $reason,
            $orderNo
        );

        # 添加报表记录
        (new ReportService())->companyBankAccountReport($transaction);

        return $transaction;
    }


    /**
     * 创建公司银行卡交易记录
     *
     * @param   CompanyBankAccount    $companyBankAccount         公司银行卡
     * @param   integer               $type                       类型
     * @param   boolean               $isIncome                   是否是入账  true:入账 false:出账
     * @param   float                 $amount                     金额
     * @param   float                 $fee                        手续费
     * @param   string                $adminName                  操作人员(会员或者管理员)
     * @param   string                $userName                   会员名称
     * @param   integer               $traceId                    追踪id
     * @param   string                $fromAccount                来源银行卡
     * @param   string                $toAccount                  去向银行卡
     * @param   string                $remark                     备注
     * @param   string                $reason                     理由
     * @param   string                $orderNo                    订单号
     * @return  CompanyBankAccountTransaction
     */
    public static function create(
        CompanyBankAccount $companyBankAccount,
        $type,
        $isIncome,
        $amount,
        $fee,
        $adminName='',
        $userName='',
        $traceId=null,
        $fromAccount='',
        $toAccount='',
        $remark='',
        $reason=null,
        $orderNo=''
    ) {

        $transaction = new CompanyBankAccountTransaction();

        $transaction->type          = $type;
        $transaction->is_income     = $isIncome;
        $transaction->total_amount  = $amount + $fee;
        $transaction->amount        = $amount;
        $transaction->after_balance = $companyBankAccount->balance;
        $transaction->fee           = $fee;
        $transaction->trace_id      = !empty($traceId) ? $traceId : null;
        $transaction->from_account  = $fromAccount;
        $transaction->to_account    = $toAccount;
        $transaction->user_name     = $userName;
        $transaction->admin_name    = $adminName;
        $transaction->remark        = $remark;
        $transaction->reason        = $reason;
        $transaction->order_no      = $orderNo;
        $transaction->company_bank_account_code = $companyBankAccount->code;

        $transaction->save();

        return $transaction;
    }
}