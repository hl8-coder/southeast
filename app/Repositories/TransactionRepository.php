<?php
namespace App\Repositories;

use App\Models\Transaction;

class TransactionRepository
{
    /**
     * 添加帐变记录
     *
     * @param integer   $userId         会员id
     * @param integer   $traceId        追踪id
     * @param string    $orderNo        订单号
     * @param integer   $type           帐变类型
     * @param bool      $isIncome       是否入账 true:入账 false:出帐
     * @param string    $currency       币别
     * @param float     $beforeBalance  扣款前记录的账户金额
     * @param float     $amount         帐变金额
     * @param null      $adminName      管理员名称
     * @param string    $adminRemark    管理员备注
     * @param string    $sysRemark      系统备注
     * @return Transaction
     */
    public static function create(
        $userId,
        $traceId,
        $orderNo,
        $type,
        $isIncome,
        $currency,
        $beforeBalance,
        $amount,
        $adminName=null,
        $adminRemark='',
        $sysRemark=''
    )
    {
        $transaction = new Transaction();

        $transaction->user_id       = $userId;
        $transaction->trace_id      = $traceId;
        $transaction->order_no      = $orderNo;
        $transaction->type_group    = Transaction::$mappingTypeGroup[$type];
        $transaction->type          = $type;
        $transaction->is_income     = $isIncome;
        $transaction->currency      = $currency;
        $transaction->amount        = $amount;
        $transaction->admin_name    = $adminName;
        $transaction->admin_remark  = $adminRemark;
        $transaction->sys_remark    = $sysRemark;

        //加钱的話, job成功后才更新
        //扣钱的話, 建立時更新
        if (!$isIncome) {
            $transaction->before_balance = $beforeBalance;
            $transaction->after_balance  = $beforeBalance - $amount;
        }

        $transaction->save();

        return $transaction;
    }
}