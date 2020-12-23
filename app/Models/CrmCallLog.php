<?php

namespace App\Models;


class CrmCallLog extends Model
{
    public $fillable = [
        'crm_order_id', 'admin_id', 'channel', 'call_status', 'purpose',
        'prefer_product', 'prefer_bank', 'source', 'comment',
    ];


    #渠道
    const CHANNEL_PHONE = 1;
    const CHANNEL_SMS   = 2;
    const CHANNEL_EMAIL = 3;
    const CHANNEL_ZALO  = 4;
    const CHANNEL_LINE  = 5;
    public static $channel = [
        self::CHANNEL_PHONE => 'PHONE',
        self::CHANNEL_SMS   => 'SMS',
        self::CHANNEL_EMAIL => 'EMAIL',
        self::CHANNEL_ZALO  => 'ZALO',
        self::CHANNEL_LINE  => 'LINE',
    ];

    #目的
    const PURPOSE_CUSTOMER_WANT_TO_PLAY                    = 1;
    const PURPOSE_CUSTOMER_WANT_TO_CLAIM_BONUS_OR_FREE_BET = 2;
    const PURPOSE_NON_OWN_INFORMATION                      = 3;
    const PURPOSE_FOR_FUN                                  = 4;
    const PURPOSE_FOR_NA                                   = 5;
    public static $purpose = [
        self::PURPOSE_CUSTOMER_WANT_TO_PLAY                    => 'Customer Want To Play',
        self::PURPOSE_CUSTOMER_WANT_TO_CLAIM_BONUS_OR_FREE_BET => 'Customer Want To Claim Bonus or Free Bet',
        self::PURPOSE_NON_OWN_INFORMATION                      => 'Non Own Information',
        self::PURPOSE_FOR_FUN                                  => 'For Fun',
        self::PURPOSE_FOR_NA                                   => 'N/A',
    ];

    #偏好产品
    const PREFER_PRODUCT_SPORTBOOK     = 1;
    const PREFER_PRODUCT_CASINO        = 2;
    const PREFER_PRODUCT_SLOTS         = 3;
    const PREFER_PRODUCT_COCKFIGHT     = 4;
    const PREFER_PRODUCT_FISHING       = 5;
    const PREFER_PRODUCT_LOTTERY       = 6;
    const PREFER_PRODUCT_E_SPORT       = 7;
    const PREFER_PRODUCT_VIRTUAL_SPORT = 8;
    const PREFER_PRODUCT_P2P           = 9;
    const PREFER_PRODUCT_NA            = 10;

    public static $prefer_product = [
        self::PREFER_PRODUCT_SPORTBOOK     => 'Sportbook',
        self::PREFER_PRODUCT_CASINO        => 'Casino',
        self::PREFER_PRODUCT_SLOTS         => 'Slots',
        self::PREFER_PRODUCT_COCKFIGHT     => 'Cockfight',
        self::PREFER_PRODUCT_FISHING       => 'Fishing',
        self::PREFER_PRODUCT_LOTTERY       => 'Lottery',
        self::PREFER_PRODUCT_E_SPORT       => 'E-Sport',
        self::PREFER_PRODUCT_VIRTUAL_SPORT => 'Virtual Sport',
        self::PREFER_PRODUCT_P2P           => 'P2P',
        self::PREFER_PRODUCT_NA            => 'N/A',
    ];

    #来源
    const SOURCE_FRIENDS  = 1;
    const SOURCE_FACEBOOK = 2;
    const SOURCE_BANNER   = 3;
    const SOURCE_BLOG     = 4;
    const SOURCE_OTHER    = 5;
    const SOURCE_NA       = 6;
    const SOURCE_LINE     = 7;
    const SOURCE_SMS      = 8;
    public static $source = [
        self::SOURCE_FRIENDS  => 'Friends',
        self::SOURCE_FACEBOOK => 'Facebook',
        self::SOURCE_BANNER   => 'Banner',
        self::SOURCE_BLOG     => 'Blog',
        self::SOURCE_OTHER    => 'Other',
        self::SOURCE_NA       => 'N/A',
        self::SOURCE_LINE     => 'Line',
        self::SOURCE_SMS      => 'SMS',
    ];

    #偏好银行
    const PREFER_BANK_DONGA       = 1;
    const PREFER_BANK_AGRIBANK    = 2;
    const PREFER_BANK_SACOM       = 3;
    const PREFER_BANK_VCB         = 4;
    const PREFER_BANK_VIETTIN     = 5;
    const PREFER_BANK_ACB         = 6;
    const PREFER_BANK_TECHCOMBANK = 7;
    const PREFER_BANK_BIDV        = 8;
    const PREFER_BANK_EXIMBANK    = 9;
    const PREFER_BANK_BBL         = 10;
    const PREFER_BANK_KTB         = 11;
    const PREFER_BANK_KBANK       = 12;
    const PREFER_BANK_SCB         = 13;
    const PREFER_BANK_TMB         = 14;
    const PREFER_BANK_BAY         = 15;
    const PREFER_BANK_GSB         = 16;
    const PREFER_BANK_OTHER       = 17;

    public static $prefer_bank = [
        self::PREFER_BANK_DONGA       => '(VND)DongA',
        self::PREFER_BANK_AGRIBANK    => '(VND)Agribank',
        self::PREFER_BANK_SACOM       => '(VND)Sacom',
        self::PREFER_BANK_VCB         => '(VND)VCB',
        self::PREFER_BANK_VIETTIN     => '(VND)Viettin',
        self::PREFER_BANK_ACB         => '(VND)ACB',
        self::PREFER_BANK_TECHCOMBANK => '(VND)Techcombank',
        self::PREFER_BANK_BIDV        => '(VND)BIDV',
        self::PREFER_BANK_EXIMBANK    => '(VND)Eximbank',
        self::PREFER_BANK_BBL         => '(THB)BBL',
        self::PREFER_BANK_KTB         => '(THB)KTB',
        self::PREFER_BANK_KBANK       => '(THB)KBANK',
        self::PREFER_BANK_SCB         => '(THB)SCB',
        self::PREFER_BANK_TMB         => '(THB)TMB',
        self::PREFER_BANK_BAY         => '(THB)BAY',
        self::PREFER_BANK_GSB         => '(THB)GSB',
        self::PREFER_BANK_OTHER       => 'Other',
    ];

    #原因
    const REASON_NO_TIME                 = 1;
    const REASON_NO_MONEY                = 2;
    const REASON_NO_INTERESTED_PROMOTION = 3;
    const REASON_CUSTOMER_SERVICE        = 4;
    const REASON_PAYMENT_SPEED           = 5;
    const REASON_OTHER                   = 6;
    public static $reason = [
        self::REASON_NO_TIME                 => 'No time',
        self::REASON_NO_MONEY                => 'No money',
        self::REASON_NO_INTERESTED_PROMOTION => 'No interested promotion',
        self::REASON_CUSTOMER_SERVICE        => 'Customer Service',
        self::REASON_PAYMENT_SPEED           => 'Payment Speed',
        self::REASON_OTHER                   => 'Other',
    ];


    const CALL_STATUS_VOICE_MAIL        = 1;
    const CALL_STATUS_HAND_UP           = 2;
    const CALL_STATUS_NO_PICK_UP        = 3;
    const CALL_STATUS_INVALID_NUMBER    = 4;
    const CALL_STATUS_NOT_OWN_NUMBER    = 5;
    const CALL_STATUS_CALL_BACK         = 6;
    const CALL_STATUS_NO_ANSWER         = 7;
    const CALL_STATUS_NOT_INTERESTED_IN = 8;
    const CALL_STATUS_SUCCESS           = 9;

    public static $call_statuses = [
        self::CALL_STATUS_VOICE_MAIL        => 'Voice Mail',
        self::CALL_STATUS_HAND_UP           => 'Hang up',
        self::CALL_STATUS_NO_PICK_UP        => 'No Pick Up',
        self::CALL_STATUS_INVALID_NUMBER    => 'Invalid Number',
        self::CALL_STATUS_NOT_OWN_NUMBER    => 'Not Own Number',
        self::CALL_STATUS_CALL_BACK         => 'Call Back',
        self::CALL_STATUS_NO_ANSWER         => 'No Answer',
        self::CALL_STATUS_NOT_INTERESTED_IN => 'Not Interested In',
        self::CALL_STATUS_SUCCESS           => 'Success',
    ];

    public static $callStatusToStatus = [
        self::CALL_STATUS_VOICE_MAIL        => false,
        self::CALL_STATUS_HAND_UP           => false,
        self::CALL_STATUS_NO_PICK_UP        => false,
        self::CALL_STATUS_INVALID_NUMBER    => false,
        self::CALL_STATUS_NOT_OWN_NUMBER    => true,
        self::CALL_STATUS_CALL_BACK         => true,
        self::CALL_STATUS_NO_ANSWER         => false,
        self::CALL_STATUS_NOT_INTERESTED_IN => true,
        self::CALL_STATUS_SUCCESS           => true,
    ];

    public function crmOrder()
    {
        // 关联查询的时候，忽略软删除，可以反向关联到
        return $this->belongsTo(CrmOrder::class)->withTrashed();
    }

    # 查询作用域 start
    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('crmOrder', function ($query) use ($userName) {
            return $query->where('name', $userName);
        });
    }
    # 查询作用域 end
}
