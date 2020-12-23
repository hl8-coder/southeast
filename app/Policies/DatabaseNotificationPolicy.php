<?php

namespace App\Policies;

use App\Models\DatabaseNotification;
use App\Models\User;
use Illuminate\Auth\Access\HandlesAuthorization;

class DatabaseNotificationPolicy extends Policy
{
    use HandlesAuthorization;

    public function own(User $user, DatabaseNotification $notification)
    {
        return $user->id == $notification->notifiable_id;
    }
}
