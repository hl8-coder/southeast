<?php
namespace App\Repositories;

use App\Models\Deposit;
use App\Models\PgAccount;
use App\Models\PgAccountTransaction;
use App\Services\ReportService;

class PgAccountTransactionRepository
{
    /**
     * 添加第三方通道的交易记录并更新第三方通道余额
     *

     * @param   PgAccount    $pgAccount         公司银行卡
     * @param   integer      $type              类型
     * @param   boolean      $isIncome          是否是入账  true:入账 false:出账
     * @param   float        $amount            金额 不包含手续费.
     * @param   float        $fee               手续费
     * @param   string       $adminName         操作人员(会员或者管理员)
     * @param   string       $userName          会员名称
     * @param   integer      $traceId           追踪id
     * @param   string       $fromAccount       来源通道
     * @param   string       $toAccount         去向通道或银行卡
     * @param   string       $remark            备注
     * @return  PgAccountTransaction
     * @throws
     */
    public static function add(
        PgAccount $pgAccount,
        $type,
        $isIncome,
        $amount,
        $fee,
        $adminName='',
        $userName='',
        $traceId = null,
        $fromAccount='',
        $toAccount='',
        $remark=''
    )
    {
        $amount = abs($amount);
        $fee    = abs($fee);

        if ($type == PgAccountTransaction::TYPE_USER_DEPOSIT) { # 用户存款
            $totalAmount = $amount; // 如果为用户存款 current_balance = currenct_balance +deposit - fee
        } elseif ($type == PgAccountTransaction::TYPE_COMPANY_WITHDRAWAL) { # 公司从第三方提款
            $totalAmount = $amount + $fee;
        } else { # 手动调整金额.
            # 与 user 确认，扣款仅扣出款的账户，入款的时候不扣入款账户的余额
            $isIncome ? $totalAmount = $amount : $totalAmount = $amount + $fee;
        }

        # 更新pg account余额
        $pgAccount = $pgAccount->updateBalance($totalAmount, $isIncome);

        # 创建交易记录
        $transaction = static::create(
            $pgAccount,
            $type,
            $isIncome,
            $amount,
            $fee,
            $adminName,
            $userName,
            $traceId,
            $fromAccount,
            $toAccount,
            $remark
        );

        # 添加报表记录
        (new ReportService())->pgAccountReport($transaction);

        return $transaction;
    }


    /**
     * 创建pg account 第三方通道交易记录
     *
     * @param   PgAccount    $pgAccount         公司银行卡
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
     * @return  PgAccountTransaction
     */
    public static function create(
        PgAccount $pgAccount,
        $type,
        $isIncome,
        $amount,
        $fee,
        $adminName='',
        $userName='',
        $traceId=null,
        $fromAccount='',
        $toAccount='',
        $remark=''
    ) {

        $transaction = new PgAccountTransaction();

        $transaction->type          = $type;
        $transaction->is_income     = $isIncome;
        $transaction->total_amount  = $amount + $fee;
        $transaction->amount        = $amount;
        $transaction->after_balance = $pgAccount->current_balance;
        $transaction->fee           = $fee;
        $transaction->trace_id      = !empty($traceId) ? $traceId : null;
        $transaction->from_account  = $fromAccount;
        $transaction->to_account    = $toAccount;
        $transaction->user_name     = $userName;
        $transaction->admin_name    = $adminName;
        $transaction->remark        = $remark;
        $transaction->payment_platform_code = $pgAccount->payment_platform_code;

        $transaction->save();

        return $transaction;
    }

    # 用户存款引起的第三方帐变.
    public static function findOrCreatePgAccountTranslationByUserDeposit(Deposit $deposit)
    {
        $deposit->refresh();
        $paymentPlatform = $deposit->paymentPlatform;

        $paymentPlatformCode = $paymentPlatform->code;

        $user = $deposit->user;

        # 检查是否已经写入过用户存款产生的帐变.
        $isExistPgAccountTranslation = PgAccountTransaction::query()->where('trace_id', '=', $deposit->order_no)->where('type', '=', PgAccountTransaction::TYPE_USER_DEPOSIT)->exists();

        # 如果未计入过帐变.
        if (!$isExistPgAccountTranslation) {

            $pgAccount = PgAccount::where('payment_platform_code', $paymentPlatformCode)->first();

            $type = PgAccountTransaction::TYPE_USER_DEPOSIT; # 用户存款产生的帐变.

            $isIncome = 1;

            $fromAccount = '';

            $toAccount = $paymentPlatformCode;

            $userName = $user->name;

            # 第三方通道的帐变金额 = 用户充值的实际金额 -该笔充值第三方收取我方平台的手续费 比如用户充值100  实际到账100 但是第三方平台收取我们的手续费2  实际我方在第三方的余额是98.
            $amount = $deposit->arrival_amount - $deposit->reimbursement_fee;

            # 用户存款 平台收取公司的手续费.
            $fee = $deposit->reimbursement_fee;

            $traceId = $deposit->order_no;

            $lastLog = $deposit->logs()->latest()->first();

            $adminName = $lastLog ? $lastLog->admin_name : "";

            $remark = $deposit->remarks;

            # 执行写入帐变.
            self::add(
                $pgAccount,
                $type,
                $isIncome,
                $amount,
                $fee,
                $adminName,
                $userName,
                $traceId,
                $fromAccount,
                $toAccount,
                $remark
            );

        }

    }
}
