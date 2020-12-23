<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class PgAccount extends Model
{
    protected $casts = [
        'payment_platform_code' => 'string',
        'current_balance'       => 'float',
        'status'                => 'boolean',
    ];

    # 状态
    const STATUS_ACTIVE         = 1;
    const STATUS_INACTIVE       = 0;

    public static $statuses = [
        self::STATUS_ACTIVE         => 'Active',
        self::STATUS_INACTIVE       => 'Inactive',
    ];

    # otps
    const OTP_SMS = 1;
    const OTP_APP = 2;

    public static $otps = [
        self::OTP_SMS => 'sms',
        self::OTP_APP => 'app',
    ];

    public function paymentPlatform()
    {
        return $this->belongsTo(PaymentPlatform::class, 'payment_platform_code', 'code');
    }

    public function remarks()
    {
        return $this->hasMany(PgAccountRemark::class,'payment_platform_code','payment_platform_code');
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('paymentPlatform', function ($query) use ($value) {
            return $query->where('currencies', 'like', '%' . $value . '%');
        });
    }

    /**
     * 更新pg account 的余额.
     *
     * @param  float        $amount         变动金额(实际帐变金额)
     * @param  boolean      $isIncome       是否是进款 true:入款 false:出款
     * @throws \Exception
     * @return static
     */
    public function updateBalance($amount, $isIncome)
    {
        $amount = $isIncome ? abs($amount) : -1 * abs($amount);

        $builder = $this->setPrimaryKeyQuery()
            ->whereRaw(DB::raw("current_balance + $amount >= 0"));

        if ($isIncome) {
            $affectRow = $builder->update([
                'current_balance'           => DB::raw("current_balance + $amount"),

            ]);
        } else {
            $amount = abs($amount);
            $affectRow = $builder->update([
                'current_balance'           => DB::raw("current_balance - $amount"),
            ]);
        }

        if (1 != $affectRow) {
            throw new \Exception('pg account balance not enough');
        }

        return $this->refresh();
    }

    public static function getDropList()
    {
        return static::query()->pluck('payment_platform_code', 'id')->toArray();
    }

    /**
     * 获取help2Pay的存款手续费 支付平台收取的我方的金额.
     *
     * @param $currency
     * @param $payment_platform_code
     * @return float|int
     */
    public static function getHelp2PayRate($currency,$payment_platform_code)
    {
        $rate = 0;

        # 获取当月的存款总额.
        $month_start_date = date("Y-m-01", time());
        $today_date = date("Y-m-d", time());

        $sumInfo = PgAccountReport::where('date','>=', $month_start_date)->where('date','<=',$today_date)->where('payment_platform_code', $payment_platform_code)->first([
            DB::raw('sum(deposit) as deposit'),
            DB::raw('sum(deposit_fee) as deposit_fee')
        ]);

        $deposit      = !empty($sumInfo['deposit']) ? $sumInfo['deposit'] : 0;
        $deposit_fee  = !empty($sumInfo['deposit_fee']) ? $sumInfo['deposit_fee'] : 0;

        $total_deposit = $deposit + $deposit_fee; # 用户实际充值的金额.

        $currency = strtolower($currency);

        if ($currency == "vnd") {
            if ($total_deposit <= 5500000) {
                $rate = 0.025;
            } elseif ($total_deposit > 5500000 && $total_deposit <=16000000) {
                $rate = 0.0225;
            } else {
                $rate = 0.02;
            }
        } elseif ($currency == "thb") {
            if ($total_deposit <= 10000000) {
                $rate = 0.025;
            } elseif($total_deposit > 10000000 && $total_deposit <= 30000000) {
                $rate = 0.0225;
            } else {
                $rate = 0.02;
            }
        }

        return $rate;
    }

}
