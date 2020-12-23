<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Withdrawal;

class WithdrawalPolicy extends Policy
{
    public function own(User $user, Withdrawal $withdrawal)
    {
        return $user->isAuthorOf($withdrawal);
    }

}
