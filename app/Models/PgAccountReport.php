<?php

namespace App\Models;

class PgAccountReport extends Model
{
    protected $casts = [
        'payment_platform_code' => 'string',
        'start_balance'         => 'float',
        'end_balance'           => 'float',
        'deposit'               => 'float',
        'deposit_fee'           => 'float',
        'withdrawal'            => 'float',
        'withdraw_fee'          => 'float',
    ];


    public function account()
    {
        return $this->belongsTo(PgAccount::class, 'payment_platform_code', 'payment_platform_code');
    }
}
