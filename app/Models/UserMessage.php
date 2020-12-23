<?php

namespace App\Models;

class UserMessage extends Model
{
    protected $fillable = [
        'category', 'content', 'number', 'sent_admin_id', 'sent_admin_name', 'provider_code',
        'use_type', 'title'
    ];

    const MESSAGE_CATEGORY_PROMOTION        = 1;
    const MESSAGE_CATEGORY_BANKING          = 2;
    const MESSAGE_CATEGORY_ACCOUNT_PROFIT   = 3;
    const MESSAGE_CATEGORY_REBATE           = 4;
    const MESSAGE_CATEGORY_BONUS            = 5;

    public static $categories = [
        self::MESSAGE_CATEGORY_PROMOTION      => 'Promotion',
        self::MESSAGE_CATEGORY_BANKING        => 'Banking',
        self::MESSAGE_CATEGORY_ACCOUNT_PROFIT => 'Account Profit',
        self::MESSAGE_CATEGORY_REBATE         => 'Rebate',
        self::MESSAGE_CATEGORY_BONUS          => 'Welcome Bonus',
    ];

    const USE_TYPE_FOR_AD                   = 1;//广告用途
    const USE_TYPE_FOR_VERIFICATION_CODE    = 2;//验证码用途

    public static $useTypes = [
        self::USE_TYPE_FOR_AD                => 'ad',
        self::USE_TYPE_FOR_VERIFICATION_CODE => 'verification_code',
    ];

    public function getFriendlyCategory()
    {
        $categoryArray = self::$categories;

        if (isset($categoryArray[$this->category])) {
            return $categoryArray[$this->category];
        }

        return $this->category;
    }

    public function userMessageDetails()
    {
        return $this->hasMany(UserMessageDetail::class);
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('userMessageDetails', function ($query) use ($value) {
            return $query->where('currency', $value);
        });    }
}
