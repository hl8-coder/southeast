<?php

namespace App\Listeners;

use App\Models\Admin;
use App\Models\Withdrawal;
use OwenIt\Auditing\Events\Auditing;

class UpdateWithdrawalListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  Auditing  $event
     * @return void
     */
    public function handle(Auditing $event)
    {
        # 提现如果是前端触发修改不记录
        if ($event->model instanceof Withdrawal && !request()->user() instanceof Admin) {
            return false;
        }
    }
}
