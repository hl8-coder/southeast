<?php
namespace App\Repositories;

use App\Models\FreezeLog;
use App\Models\UserAccount;

class UserAccountRepository
{
    public static function freeze(UserAccount $userAccount, $amount, $freezeType, $traceId)
    {
        $amount = abs($amount);
        # 记录日志
        FreezeLog::record($userAccount->user, true, $amount, $userAccount->freeze_balance, $freezeType, $traceId);

        UserAccount::freeze($userAccount, $amount);

        return $userAccount->refresh();
    }

    public static function unfreeze(UserAccount $userAccount, $amount, $freezeType, $traceId)
    {
        $amount = abs($amount);
        # 记录日志
        FreezeLog::record($userAccount->user, false, $amount, $userAccount->freeze_balance, $freezeType, $traceId);

        UserAccount::unfreeze($userAccount, $amount);

        return $userAccount->refresh();
    }
}