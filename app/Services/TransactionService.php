<?php

namespace App\Services;

use App\Models\Transaction;
use App\Models\User;
use App\Models\UserAccount;
use App\Repositories\TransactionRepository;
use App\Repositories\UserAccountRepository;
use Illuminate\Support\Facades\DB;

class TransactionService
{
    protected $transaction;

    /**
     * 添加帐变记录
     *
     * @param User      $user
     * @param float     $amount         转账金额
     * @param integer   $type           帐变类型
     * @param integer   $tranceId       追踪id
     * @param string    $orderNo        订单号
     * @param null      $adminName      管理员名称
     * @param string    $adminRemark    管理员备注
     * @param string    $sysRemark      系统备注
     * @param bool      $defaultSucceed
     * @return Transaction
     * @throws \Exception
     */
    public function addTransaction(
        User $user,
        $amount,
        $type,
        $tranceId,
        $orderNo='',
        $defaultSucceed = false,
        $adminName=null,
        $adminRemark='',
        $sysRemark=''
    )
    {
        # 取得是否入账
        $isIncome       = Transaction::$typeIncomes[$type];
        $amount         = abs($amount);
        $userAccount    = $user->account->refresh();

        # 扣款时记住扣款钱帐变
        $beforeBalance = $userAccount->total_balance;

        # 检查trace_id是否已经存在

        # 扣款立马更新账户余额
        # 加钱需要job来处理
        if (!$isIncome) {
            UserAccount::delTotalBalance($userAccount, $amount);
        }

        $transaction = TransactionRepository::create(
            $user->id,
            $tranceId,
            $orderNo,
            $type,
            $isIncome,
            $user->currency,
            $beforeBalance,
            $amount,
            $adminName,
            $adminRemark,
            $sysRemark
        );

        # 默认成功直接异动主账户
        if ($defaultSucceed) {
            $transaction = $this->process($transaction);
        }
        return $transaction;
    }

    /**
     * 帐变处理流程
     *
     * @param Transaction $transaction
     * @return Transaction
     * @throws \Exception
     */
    public function process(Transaction $transaction)
    {
        $user = $transaction->user;
        $userAccount = $user->account;

        # 记录加款前的账户金额
        $beforeBalance = $userAccount->total_balance;

        if ($transaction->isIncome()) {
            UserAccount::addTotalBalance($userAccount, $transaction->amount);
        }

        $transaction->success($beforeBalance);

        # 统计
        if (isset(Transaction::$mappingReportType[$transaction->type])) {
            (new ReportService)->platformReport(
                $transaction->user,
                UserAccount::MAIN_WALLET,
                Transaction::$mappingReportType[$transaction->type],
                $transaction->amount,
                $transaction->end_process_at
            );
        }

        return $transaction;
    }

    /**
     * 解冻并添加帐变记录
     *
     * @param   UserAccount   $userAccount          会员账户
     * @param   float         $amount               金额
     * @param   int           $transactionType      帐变类型
     * @param   int           $freezeType           冻结类型
     * @param   int           $traceId              追踪id
     * @param   string        $orderNo              订单号
     * @param   boolean        $isUseTransaction    是否使用事务
     * @return  mixed
     * @throws
     */
    public function unfreezeAndAddTransaction(UserAccount $userAccount, $amount, $transactionType, $freezeType, $traceId, $orderNo='', $isUseTransaction=true)
    {
        if ($isUseTransaction) {
            return DB::transaction(function() use ($userAccount, $amount, $transactionType, $freezeType, $traceId, $orderNo) {
                # 先解冻
                UserAccountRepository::unfreeze($userAccount, $amount, $freezeType, $traceId);

                # 添加帐变
                return $this->addTransaction($userAccount->user, $amount, $transactionType, $traceId, $orderNo);
            });
        } else {
            # 先解冻
            UserAccountRepository::unfreeze($userAccount, $amount, $freezeType, $traceId);

            # 添加帐变
            return $this->addTransaction($userAccount->user, $amount, $transactionType, $traceId, $orderNo);
        }
    }
}