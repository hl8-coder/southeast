<?php

namespace App\Models;

class UserPlatformMonthlyReport extends Report
{
    protected $casts = [
        'deposit'                   => 'float',
        'withdrawal'                => 'float',
        'transfer_in'               => 'float',
        'transfer_out'              => 'float',
        'adjustment'                => 'float',
        'affiliate_transfer_in'     => 'float',
        'affiliate_transfer_out'    => 'float',
    ];
}
