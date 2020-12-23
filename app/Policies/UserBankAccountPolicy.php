<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserBankAccount;

class UserBankAccountPolicy extends Policy
{
    public function own(User $user, UserBankAccount $userBankAccount)
    {
        return $user->isAuthorOf($userBankAccount);
    }
}
