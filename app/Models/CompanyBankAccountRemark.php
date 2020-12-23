<?php

namespace App\Models;

class CompanyBankAccountRemark extends Model
{
    protected $fillable = [
        'company_bank_account_id', 'remark', 'admin_name',
    ];

    public static function add($companyBankAccountId, $remark, $category, $adminName)
    {
        $accountRemark = new static;
        $accountRemark->company_bank_account_id = $companyBankAccountId;
        $accountRemark->remark         = $remark;
        $accountRemark->category       = $category;
        $accountRemark->admin_name     = $adminName;
        $accountRemark->save();

        return $accountRemark;
    }
}
