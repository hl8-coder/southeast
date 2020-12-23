<?php

namespace App\Models;

class PgAccountRemark extends Model
{
    protected $fillable = [
        'payment_platform_code', 'remark', 'admin_name',
    ];

    public static function add($payment_platform_code, $remark, $adminName)
    {
        $accountRemark = new static;
        $accountRemark->payment_platform_code = $payment_platform_code;
        $accountRemark->remark         = $remark;
        $accountRemark->admin_name     = $adminName;
        $accountRemark->save();

        return $accountRemark;
    }
}
