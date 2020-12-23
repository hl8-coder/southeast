<?php

namespace App\Providers;

use App\Models\DatabaseNotification;
use App\Models\UserBankAccount;
use App\Models\UserMpayNumber;
use App\Models\Withdrawal;
use App\Policies\DatabaseNotificationPolicy;
use App\Policies\UserBankAccountPolicy;
use App\Policies\UserMpayNumerPolicy;
use App\Policies\WithdrawalPolicy;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
        UserBankAccount::class => UserBankAccountPolicy::class,
        DatabaseNotification::class => DatabaseNotificationPolicy::class,
        Withdrawal::class => WithdrawalPolicy::class,
        UserMpayNumber::class => UserMpayNumerPolicy::class,
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

        //
    }
}
