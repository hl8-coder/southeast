<?php

namespace App\Providers;

use App\Events\ReportSavedEvent;
use App\Listeners\UpdateWithdrawalListener;
use App\Listeners\UpUserVipLevelListener;
use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use OwenIt\Auditing\Events\Auditing;
use OwenIt\Auditing\Models\Audit;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event listener mappings for the application.
     *
     * @var array
     */
    protected $listen = [
        Registered::class => [
            SendEmailVerificationNotification::class,
        ],
        ReportSavedEvent::class => [
            UpUserVipLevelListener::class,
        ],
        Auditing::class => [
            UpdateWithdrawalListener::class,
        ],
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();

        Audit::creating(function (Audit $model) {
            if (empty($model->old_values) && empty($model->new_values)) {
                return false;
            }
        });
    }
}
