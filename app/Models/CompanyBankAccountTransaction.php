<?php

namespace App\Models;

class CompanyBankAccountTransaction extends Model
{
    protected $casts = [
        'total_amount'  => 'float',
        'amount'        => 'float',
        'fee'           => 'float',
        'after_balance' => 'float',
        'is_income'     => 'boolean',
    ];

    # type
    const TYPE_DEPOSIT              = 1;
    const TYPE_WITHDRAWAL           = 2;
    const TYPE_ADJUSTMENT           = 3;
    const TYPE_INTERNAL_TRANSFER    = 4;
    const TYPE_BUFFER               = 5;

    public static $types = [
        self::TYPE_DEPOSIT              => 'Deposit',
        self::TYPE_WITHDRAWAL           => 'Withdrawal',
        self::TYPE_ADJUSTMENT           => 'Adjustment',
        self::TYPE_INTERNAL_TRANSFER    => 'Internal Transfer',
        self::TYPE_BUFFER               => 'Buffer',
    ];

    # reason
    const REASON_SERVICE_FEES       = 1;
    const REASON_INTEREST           = 2;
    const REASON_BUFFER_TRANSFER    = 3;
    const REASON_UNCLAIMED_DEPOSIT  = 4;
    const REASON_DOUBLE_CREDIT      = 5;
    const REASON_OTHER              = 6;

    public static $reasons = [
        self::REASON_SERVICE_FEES       => 'Service fees',
        self::REASON_INTEREST           => 'Interest',
        self::REASON_BUFFER_TRANSFER    => 'Buffer Transfer',
        self::REASON_UNCLAIMED_DEPOSIT  => 'Unclaimed deposit',
        self::REASON_DOUBLE_CREDIT      => 'Double credit',
        self::REASON_OTHER              => 'Other',
    ];

    public function account()
    {
        return $this->belongsTo(CompanyBankAccount::class, 'company_bank_account_code', 'code');
    }

//    public function scopeCompanyBankAccountCode($query, $value)
//    {
//        return $query->where(function($query) use ($value) {
//            $query->where('from_account', $value)->orWhere('to_account', $value)->orWhere('company_bank_account_code', $value);
//        });
//    }
}
