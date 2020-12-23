<?php

namespace App\Repositories;

use App\Jobs\TransactionProcessJob;
use App\Models\Admin;
use App\Models\Bank;
use App\Models\CompanyBankAccountTransaction;
use App\Models\Currency;
use App\Models\FreezeLog;
use App\Models\Report;
use App\Models\Transaction;
use App\Models\TransferDetail;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserBankAccount;
use App\Models\Withdrawal;
use App\Services\ReportService;
use App\Services\TransactionService;
use Illuminate\Support\Facades\DB;

class WithdrawalRepository
{
    /**
     * 创建提现
     *
     * @param User $user
     * @param UserBankAccount $userBankAccount
     * @param float $amount 金额
     * @param integer $device 装置
     * @param   $userIp
     * @return  Withdrawal
     */
    public static function create(User $user, UserBankAccount $userBankAccount, $amount, $device, $userIp)
    {
        $withdrawal = new Withdrawal([
            'bank_id'       => $userBankAccount->bank_id,
            'province'      => $userBankAccount->province,
            'city'          => $userBankAccount->city,
            'branch'        => $userBankAccount->branch,
            'account_name'  => $userBankAccount->account_name,
            'account_no'    => $userBankAccount->account_no,
            'user_ip'       => $userIp,
            'user_id'       => $user->id,
            'user_name'     => $user->name,
            'currency'      => $user->currency,
            'vip_id'        => $user->vip_id,
            'device'        => $device,
            'amount'        => $amount,
            'remain_amount' => $amount,
            'fee'           => 0,
            'status'        => Withdrawal::STATUS_PENDING,
        ]);

        # 判断remark是否存在auto withdrawal类型, 如存在, 将状态设置为提升
        if (RemarkRepository::isHasBonusHoldWithdrawalType($user->id)) {
            $withdrawal->status          = Withdrawal::STATUS_ESCALATED;
            $withdrawal->escalate_reason = Withdrawal::ESCALATE_BY_1ST_TIME_CLAIM_BONUS;
        }

        $withdrawal->save();

        return $withdrawal;
    }

    /**
     * 获取审核明细
     *
     * @param integer $userId
     * @return array
     */
    public static function getVerifyDetails($userId)
    {
        $verifies = [];

        # 是否是扑克玩家，默认true
        $verifies['is_poker_player'] = true;

        # 获取是否存在未移除的remark
        $verifies['is_has_remark'] = !RemarkRepository::isHasWithdrawalNotRemoveRemark($userId);

        # 获取最近一次成功提现单都时间
        $lastWithdrawal   = static::findLastWithdrawal($userId);
        $lastWithdrawalAt = !is_null($lastWithdrawal) ? $lastWithdrawal->paid_at : null;
        $now              = now();

        $verifies['turnover_details'] = [];

        # 获取上次成功提现时间到此时的充值金额和充值流水
        $deposits                      = DepositRepository::getByUserIdAndTime($userId, $now, $lastWithdrawalAt);
        $notClosedDeposits             = $deposits->where('is_turnover_closed', false);
        $verifies['deposit_turnover']  = $deposits->sum('turnover_closed_value');
        $depositNotCloseTurnover       = $notClosedDeposits->sum('turnover_closed_value');
        $verifies['deposit_is_closed'] = $notClosedDeposits->count() == 0;
        $verifies['turnover_details']  = static::getTurnoverDetails($verifies['turnover_details'], $deposits, 'Deposit');

        # 获取上次成功提现时间到此时的adjustment
        $adjustments                  = AdjustmentRepository::getByUserIdAndTime($userId, $now, $lastWithdrawalAt);
        $notClosedAdjustments         = $adjustments->where('is_turnover_closed', false);
        $adjustmentIsClosed           = $notClosedAdjustments->count() == 0;
        $adjustmentTurnover           = $adjustments->sum('turnover_closed_value');
        $adjustmentNotCloseTurnover   = $notClosedAdjustments->sum('turnover_closed_value');
        $verifies['turnover_details'] = static::getTurnoverDetails($verifies['turnover_details'], $adjustments, 'Adjustment');

        # 获取上次成功提现时间到此时是否有申请红利
        $userBonusPrizes              = UserBonusPrizeRepository::getByUserIdAndTime($userId, $now, $lastWithdrawalAt);
        $notClosedUserBonusPrizes     = $userBonusPrizes->where('is_turnover_closed', false);
        $verifies['is_has_bonus']     = $userBonusPrizes->count() == 0;
        $bonusIsClosed                = $notClosedUserBonusPrizes->count() == 0;
        $bonusTurnover                = $userBonusPrizes->sum('turnover_closed_value');
        $bonusNotCloseTurnover        = $notClosedUserBonusPrizes->sum('turnover_closed_value');
        $verifies['turnover_details'] = static::getTurnoverDetails($verifies['turnover_details'], $userBonusPrizes, 'Bonus');

        # 获取上次成功提现时间到此时是否有代理转账
        $transferDetails                    = TransferDetail::getByUserIdAndTime($userId, $now, $lastWithdrawalAt);
        $notClosedTransferDetails           = $transferDetails->where('is_turnover_closed', false);
        $transferDetailIsClosed             = $notClosedTransferDetails->count() == 0;
        $transferDetailTurnover             = $transferDetails->sum('turnover_closed_value');
        $transferDetailNotCloseTurnover     = $notClosedTransferDetails->sum('turnover_closed_value');
        $verifies['turnover_details']       = static::getTurnoverDetails($verifies['turnover_details'], $transferDetails, 'Transfer');

        # 获取总的关闭流水值
        $verifies['total_turnover']           = $verifies['deposit_turnover'] + $adjustmentTurnover + $bonusTurnover + $transferDetailTurnover;
        $verifies['turnover_is_closed']       = $verifies['deposit_is_closed'] && $adjustmentIsClosed && $bonusIsClosed && $transferDetailIsClosed;
        $verifies['total_not_close_turnover'] = $depositNotCloseTurnover + $adjustmentNotCloseTurnover + $bonusNotCloseTurnover + $transferDetailNotCloseTurnover;

        # 获取上次成功提现时间到此时是否有调整金额(暂时移除)
        $verifies['is_has_adjustment'] = true;

        $verifies['turnover_details'] = collect($verifies['turnover_details'])->sortByDesc('created_at')->toArray();
        $verifies['turnover_details'] = array_values($verifies['turnover_details']);

        return $verifies;
    }

    /**
     * 转化未关闭订单详情
     *
     * @param $turnoverDetails
     * @param $notClosedDetails
     * @param $type
     * @return mixed
     */
    public static function getTurnoverDetails($turnoverDetails, $notClosedDetails, $type)
    {
        foreach ($notClosedDetails as $detail) {
            $turnoverDetails[] = [
                'order_no'               => $detail->order_no,
                'type'                   => $type,
                'turnover_closed_value'  => $detail->turnover_closed_value,
                'turnover_current_value' => $detail->turnover_current_value,
                'turnover_remain_value'  => $detail->turnover_closed_value - $detail->turnover_current_value,
                'is_turnover_closed'     => $detail->is_turnover_closed,
                'created_at'             => convert_time($detail->created_at),
            ];
        }

        return $turnoverDetails;
    }

    /**
     * 检查提现系统配置限制
     *
     * @param User $user
     * @param int $bankId
     * @param float $amount 提现金额
     * @return  bool
     */
    public static function checkConfigWithdrawalLimit(User $user, $bankId, $amount)
    {
        # 获取币别配置
        $currencySet = Currency::findByCodeFromCache($user->currency);

        if ($amount < $currencySet->min_withdrawal || ($amount > $currencySet->max_withdrawal && !empty($currencySet->max_withdrawal))) {
            $minShow = thousands_number($currencySet->min_withdrawal);
            $maxShow = thousands_number($currencySet->max_withdrawal);
            error_response(422, __('withdrawal.SINGLE_CASH_WITHDRAWAL_EXCEEDS_LIMIT', ['min' => $minShow, 'max' => $maxShow]));
        }

        $totalAmount        = WithdrawalRepository::getUserDailyWithdrawTotalAmount($user->id, now()->toDateString());
        if ($totalAmount + $amount > $currencySet->max_daily_withdrawal) {
            $totalShow = thousands_number($currencySet->max_daily_withdrawal);
            $nowShow   = thousands_number($totalAmount);
            error_response(422, __('withdrawal.TOTAL_CASH_WITHDRAWAL_EXCEEDS_LIMIT', ['total' => $totalShow, 'now' => $nowShow]));
        }

        return true;
    }

    /**
     * 获取指定日志会员成功提现总金额
     *
     * @param $userId
     * @param $date
     * @return mixed
     */
    public static function getUserDailyWithdrawTotalAmount($userId, $date)
    {
        return Withdrawal::query()->where('user_id', $userId)
            ->whereDate('created_at', $date)
            ->where('status', Withdrawal::STATUS_SUCCESSFUL)
            ->sum('amount');
    }

    /**
     * 根据会员id获取最近一笔提现成功订单
     *
     * @param $userId
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function findLastWithdrawal($userId)
    {
        return Withdrawal::query()->where('user_id', $userId)
            ->where('status', Withdrawal::STATUS_SUCCESSFUL)
            ->latest()
            ->first();
    }

    /**
     * 拒绝提现
     *
     * @param Withdrawal $withdrawal
     * @param string $rejectReason 拒绝理由
     * @return  Withdrawal
     */
    public static function reject(Withdrawal $withdrawal, $rejectReason)
    {
        # 判断是否是大额提现, 如果是大额提现需要二次审核, 如果不是直接提现失败
        if (static::isLargeAmountWithdrawal($withdrawal)) {
            $withdrawal->reject($rejectReason);
        } else {
            $withdrawal = static::fail($withdrawal, $rejectReason);
        }

        return $withdrawal;
    }

    /**
     * 同意出款并添加对应报表记录
     *
     * @param Withdrawal $withdrawal
     * @param Admin $admin 管理员
     * @return  Withdrawal
     */
    public static function approve(Withdrawal $withdrawal, Admin $admin)
    {
        # 判断如果是大额，需要进行二次审核，如果不是直接提现成功
        if (static::isLargeAmountWithdrawal($withdrawal)) {
            $withdrawal->approve();
        } else {
            $withdrawal = static::success($withdrawal, $admin);
        }

        return $withdrawal;
    }

    /**
     * 提现成功
     *
     * @param Withdrawal $withdrawal
     * @param Admin $admin 管理员
     * @return  Withdrawal
     */
    public static function success(Withdrawal $withdrawal, Admin $admin)
    {
        # 发起帐变
        try {
            $transaction = DB::transaction(function () use ($withdrawal, $admin) {

                # 添加出款
                static::addRecords($withdrawal, $admin);

                # 解冻金额并发起帐变
                $transaction = (new TransactionService())->unfreezeAndAddTransaction(
                    $withdrawal->user->account,
                    $withdrawal->amount,
                    Transaction::TYPE_WITHDRAW,
                    FreezeLog::TYPE_WITHDRAW,
                    $withdrawal->id,
                    $withdrawal->order_no,
                    false
                );

                # 更新审核细节及出款信息
                $verifyDetails = static::getVerifyDetails($withdrawal->user_id);
                $withdrawal->success($verifyDetails);

                return $transaction;
            });

        } catch (\Exception $e) {
            error_response(422, $e->getMessage());
        }

        if (!empty($transaction)) {

            # 检查银行卡验证
            UserRepository::checkBankAccountVerified($withdrawal->user);

            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }

        return $withdrawal;
    }

    /**
     * 提款失败【解冻金额】
     *
     * @param Withdrawal $withdrawal
     * @param string $rejectReason
     * @return Withdrawal
     */
    public static function fail(Withdrawal $withdrawal, $rejectReason='')
    {
        if ($withdrawal->fail($rejectReason)) {
            UserAccountRepository::unfreeze(
                $withdrawal->user->account,
                $withdrawal->amount,
                FreezeLog::TYPE_WITHDRAW,
                $withdrawal->id
            );
        }

        return $withdrawal;
    }

    /**
     * 添加出款并更新提现单数字
     *
     * @param Withdrawal $withdrawal
     * @param Admin $admin 管理员
     * @return  Withdrawal
     * @throws
     */
    public static function addRecords(Withdrawal $withdrawal, Admin $admin)
    {
        $records = $withdrawal->records;
        # 更新剩余未出款金额
        $totalAmount = collect($records)->sum('amount');
        $totalFee    = collect($records)->sum('fee');

        # 检查金额是否出完
        if ($withdrawal->amount != $totalAmount) {
            throw new \Exception('The value is incorrect');
        }

        foreach ($records as $record) {
            $companyBankAccount = CompanyBankAccountRepository::findByCode($record['company_bank_account_code']);

            if (!$companyBankAccount->isWithdrawalType()) {
                throw new \Exception('The bank card is not a withdrawal type bank card.');
            }

            # 添加公司银行卡交易记录
            CompanyBankAccountTransactionRepository::add(
                $companyBankAccount,
                CompanyBankAccountTransaction::TYPE_WITHDRAWAL,
                false,
                $record['amount'],
                $record['fee'],
                $admin->name,
                $withdrawal->user->name,
                $withdrawal->id,
                $companyBankAccount->code,
                $withdrawal->account_no,
                $withdrawal->remark,
                null,
                $withdrawal->order_no
            );
        }

        $withdrawal->update([
            'remain_amount' => 0,
            'fee'           => $totalFee,
            'records'       => $records,
        ]);

        return $withdrawal->refresh();
    }

    /**
     * 获取修改记录
     *
     * @param Withdrawal $withdrawal
     * @param bool $isAsc
     * @return array
     */
    public static function getAudits(Withdrawal $withdrawal, $isAsc=false)
    {
        $data = [];

        $audits = $isAsc ? $withdrawal->audits->sortBy('created_at') : $withdrawal->audits->sortByDesc('created_at');

        foreach ($audits as $key => $audit) {

            if (array_key_exists('remark', $audit->new_values)) {
                $data[$key]['action']      = 'remark';
                $data[$key]['description'] = $audit->new_values['remark'];
            } elseif (array_key_exists('claim_admin_name', $audit->new_values)) {
                if (!empty($audit->new_values['claim_admin_name'])) {
                    $data[$key]['action']  = 'claim';
                } else {
                    $data[$key]['action']  = 'unclaim';
                }
                $data[$key]['description'] = $audit->new_values['claim_admin_name'];
            } elseif (array_key_exists('status', $audit->new_values)) {
                $status    = $audit->new_values['status'];
                $oldStatus = $audit->old_values['status'];
                switch ($status) {
                    case Withdrawal::STATUS_HOLD:
                        $data[$key]['description'] = array_key_exists('hold_reason', $audit->new_values)
                            ? transfer_show_value($audit->new_values['hold_reason'], Withdrawal::$holdReasons)
                            : '';
                        break;
                    case Withdrawal::STATUS_REJECTED:
                        $data[$key]['description'] = array_key_exists('reject_reason', $audit->new_values)
                            ? transfer_show_value($audit->new_values['reject_reason'], Withdrawal::$rejectReasons)
                            : '';
                        break;
                    case Withdrawal::STATUS_ESCALATED:
                        $data[$key]['description'] = array_key_exists('escalate_reason', $audit->new_values)
                            ? transfer_show_value($audit->new_values['escalate_reason'], Withdrawal::$escalateReasons)
                            : '';
                        break;
                    case Withdrawal::STATUS_PENDING:
                    case Withdrawal::STATUS_PROCESS:
                    case Withdrawal::STATUS_DEFERRED:
                    case Withdrawal::STATUS_APPROVED:
                    case Withdrawal::STATUS_FAIL:
                    case Withdrawal::STATUS_SUCCESSFUL:
                        $data[$key]['description'] = '';
                        break;
                    default:
                        break;
                }

                # 额外修改状态
                if (Withdrawal::STATUS_HOLD == $oldStatus) {
                    $data[$key]['action'] = 'release hold';
                } elseif (Withdrawal::STATUS_DEFERRED == $oldStatus) {
                    $data[$key]['action'] = 'release defer';
                } elseif (Withdrawal::STATUS_ESCALATED == $oldStatus) {
                    if (Withdrawal::STATUS_PENDING == $status) {
                        $data[$key]['action'] = 'rm approve';
                    } elseif (Withdrawal::STATUS_HOLD == $status) {
                        $data[$key]['action'] = 'rm hold';
                    } else {
                        $data[$key]['action'] = 'rm rejected';
                    }
                } else {
                    $data[$key]['action'] = isset(Withdrawal::$statuses[$status]) ? Withdrawal::$statuses[$status] : '';
                }

            } else {
                continue;
            }

            $data[$key]['admin_name'] = $audit->user->name;
            $data[$key]['created_at'] = convert_time($audit->created_at);
        }
        return array_values($data);
    }

    /**
     * 获取会员最近10笔提现单
     *
     * @param Withdrawal $withdrawal
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection
     */
    public static function lastTenWithdrawal(Withdrawal $withdrawal)
    {
        return Withdrawal::query()->where('user_id', $withdrawal->user_id)
            ->where('id', '!=', $withdrawal->id)
            ->latest()
            ->limit(10)
            ->get([
                'id', 'order_no', 'status', 'created_at', 'amount'
            ]);
    }

    /**
     * 判断是否是大额提现
     *
     * @param Withdrawal $withdrawal
     * @return bool
     */
    public static function isLargeAmountWithdrawal(Withdrawal $withdrawal)
    {
        $currencySet = Currency::findByCodeFromCache($withdrawal->currency);

        if ($currencySet->withdrawal_second_approve_amount > 0) {
            return $withdrawal->amount >= $currencySet->withdrawal_second_approve_amount;
        }

        return false;
    }

    public static function checkWithdrawalPendingLimit(User $user)
    {
        $count    = Withdrawal::where('user_id', $user->id)->where('status', Withdrawal::STATUS_PENDING)->count();
        $currency = Currency::findByCodeFromCache($user->currency);
        if ($count >= $currency->withdrawal_pending_limit && !empty($currency->withdrawal_pending_limit)) {
            error_response(422, __('withdrawal.exceed_the_pending_orders_limit', ['limit' => $currency->withdrawal_pending_limit]));
        }
        return true;
    }

    /**
     *
     * 判断claim权限
     *
     * @param Withdrawal $withdrawal
     * @param $admin
     * @return bool
     */
    public static function isClaimAdmin(Withdrawal $withdrawal, $admin)
    {
        if (empty($admin)) {
            return false;
        }

        return $withdrawal->claim_admin_name == $admin->name;
    }
}
