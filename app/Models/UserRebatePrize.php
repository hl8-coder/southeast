<?php

namespace App\Models;

class UserRebatePrize extends Model
{
    protected $dates = [
        'marketing_sent_at', 'payment_sent_at'
    ];

    protected $casts = [
        'effective_bet'         => 'float',
        'prize'                 => 'float',
        'close_bonus_bet'       => 'float',
        'calculate_rebate_bet'  => 'float',
        'is_max_prize'          => 'bool',
    ];

    const STATUS_CREATED                = 1; # 创建
    const STATUS_WAITING_MARKET_SEND    = 2; # 等待market审核
    const STATUS_WAITING_PAYMENT_SEND   = 3; # 等待payment审核
    const STATUS_SUCCESS                = 4; # 派发成功
    const STATUS_FAIL                   = 5; # 派发失败

    public static $statuses = [
        self::STATUS_CREATED                => 'Created',
        self::STATUS_WAITING_MARKET_SEND    => 'Waiting Marketing Send',
        self::STATUS_WAITING_PAYMENT_SEND   => 'Waiting Payment Send',
        self::STATUS_SUCCESS                => 'Successful',
        self::STATUS_FAIL                   => 'Fail',
    ];

    public static $paymentShowStatuses = [
        self::STATUS_WAITING_PAYMENT_SEND,
        self::STATUS_SUCCESS,
        self::STATUS_FAIL,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function riskGroup()
    {
        return $this->belongsTo(RiskGroup::class);
    }

    public function vip()
    {
        return $this->belongsTo(Vip::class);
    }

    public function gamePlatformProduct()
    {
        return $this->belongsTo(GamePlatformProduct::class, 'product_code', 'code');
    }

    public function isWaitingMarketSend()
    {
        return $this->is_manual_send && static::STATUS_WAITING_MARKET_SEND == $this->status;
    }

    public function isWaitingPaymentSend()
    {
        return $this->is_manual_send && static::STATUS_WAITING_PAYMENT_SEND == $this->status;
    }

    public function isCreated()
    {
        return static::STATUS_CREATED == $this->status;
    }

    public function success($adminName=null)
    {
        return $this->update([
            'status'             => static::STATUS_SUCCESS,
            'payment_admin_name' => $adminName,
            'payment_sent_at'    => now(),
        ]);
    }

    # 查询作用域 start
    public function scopeType($query, $value)
    {
        return $query->whereHas('gamePlatformProduct', function($query) use ($value) {
            $query->where('type', $value);
        });
    }

    public function scopeDateFrom($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeDateTo($query, $value)
    {
        return $query->where('created_at', '<', $value);
    }

    public function scopeMinPayout($query, $value)
    {
        return $query->where('prize', '>=', $value);
    }

    public function scopeMaxPayout($query, $value)
    {
        return $query->where('prize', '<=', $value);
    }

    public function scopeMarketingInitiatePayout($query, $value)
    {
        if (!$value) {
            return $query->where('status', static::STATUS_WAITING_MARKET_SEND)->where('is_manual_send', true);
        } else {
            return $query->where(function() use ($query) {
                $query->where('status', '!=', static::STATUS_WAITING_MARKET_SEND)
                    ->orWhere('is_manual_send', false);
            });
        }
    }

    public function scopePaymentInitiatePayout($query, $value)
    {
        if (!$value) {
            return $query->where('status', static::STATUS_WAITING_PAYMENT_SEND)->where('is_manual_send', true);
        } else {
            return $query->where(function() use ($query) {
                $query->where('status', '!=', static::STATUS_WAITING_PAYMENT_SEND)
                    ->orWhere('is_manual_send', false);
            });
        }
    }
    # 查询作用域 end
}
