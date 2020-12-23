<?php

namespace App\Models;

class PgAccountTransaction extends Model
{
    protected $casts = [
        'payment_platform_code' => 'string',
        'type' => 'integer',
        'is_income'     => 'boolean',
        'total_amount'  => 'float',
        'amount'        => 'float',
        'fee'           => 'float',
        'after_balance' => 'float',
    ];

    # type
    const TYPE_USER_DEPOSIT         = 1; // 用户存款到第三方
    const TYPE_COMPANY_WITHDRAWAL   = 2; // 公司从第三方提款
    const TYPE_ADJUSTMENT           = 3; // 管理人员手动调整

    public static $types = [
        self::TYPE_USER_DEPOSIT              => 'User Deposit',
        self::TYPE_COMPANY_WITHDRAWAL        => 'Company Withdrawal',
        self::TYPE_ADJUSTMENT           => 'Adjustment',
    ];

    public function account()
    {
        return $this->belongsTo(PgAccount::class, 'payment_platform_code', 'payment_platform_code');
    }

    public function deposit()
    {
        return $this->belongsTo(Deposit::class, 'trace_id', 'order_no');
    }
}
