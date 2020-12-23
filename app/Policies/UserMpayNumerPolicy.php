<?php

namespace App\Policies;

use App\Models\User;
use App\Models\UserMpayNumber;

class UserMpayNumerPolicy extends Policy
{
    public function own(User $user, UserMpayNumber $userMpayNumber)
    {
        return $user->isAuthorOf($userMpayNumber);
    }
}
