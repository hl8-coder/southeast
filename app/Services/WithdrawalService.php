<?php

namespace App\Services;

use App\Models\Admin;
use App\Models\CompanyBankAccountTransaction;
use App\Models\FreezeLog;
use App\Models\User;
use App\Models\UserBankAccount;
use App\Models\Withdrawal;
use App\Repositories\CompanyBankAccountRepository;
use App\Repositories\CompanyBankAccountTransactionRepository;
use App\Repositories\UserAccountRepository;
use App\Repositories\WithdrawalRepository;
use Illuminate\Support\Facades\DB;

class WithdrawalService
{
    /**
     * 添加提现记录
     *
     * @param   User            $user
     * @param   UserBankAccount $userBankAccount
     * @param   float           $amount           充值金额
     * @param   int             $device           装置
     * @param   string          $userIp           会员ip
     * @return  Withdrawal
     */
    public function store(User $user, UserBankAccount $userBankAccount, $amount, $device, $userIp)
    {
        # 添加提现记录
        $withdrawal = WithdrawalRepository::create($user, $userBankAccount, $amount, $device, $userIp);

        # 更新会员银行卡最后使用时间
        $userBankAccount->updateLastUsedAt();

        #冻结提款金额
        UserAccountRepository::freeze($user->account, $amount, FreezeLog::TYPE_WITHDRAW, $withdrawal->id);

        return $withdrawal;
    }
}