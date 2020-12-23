<?php

namespace App\Models;

class CompanyBankAccountReport extends Model
{
    protected $casts = [
        'opening_balance'   => 'float',
        'ending_balance'    => 'float',
        'buffer_in'         => 'float',
        'buffer_out'        => 'float',
        'deposit'           => 'float',
        'withdrawal'        => 'float',
        'adjustment'        => 'float',
        'internal_transfer' => 'float',
    ];

    public static $typeMappingFields = [
        CompanyBankAccountTransaction::TYPE_DEPOSIT             => 'deposit',
        CompanyBankAccountTransaction::TYPE_WITHDRAWAL          => 'withdrawal',
        CompanyBankAccountTransaction::TYPE_ADJUSTMENT          => 'adjustment',
        CompanyBankAccountTransaction::TYPE_INTERNAL_TRANSFER   => 'internal_transfer',
        CompanyBankAccountTransaction::TYPE_BUFFER              => 'buffer',
    ];

    public function account()
    {
        return $this->belongsTo(CompanyBankAccount::class, 'company_bank_account_code', 'code');
    }
}
