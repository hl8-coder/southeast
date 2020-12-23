<?php
namespace App\Repositories;

use App\Models\PaymentPlatform;

class PaymentPlatformRepository
{

    /**
     * 依币别取得资料
     *
     */
    public static function getByCurrencies($currency)
    {
        return PaymentPlatform::whereRaw('FIND_IN_SET(?,currencies)', [$currency]);
    }
}