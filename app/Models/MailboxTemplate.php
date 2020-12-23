<?php

namespace App\Models;

class MailboxTemplate extends Model
{
    public $fillable = [
        'type', 'last_update_by', 'languages', 'is_affiliate', 'currencies'
    ];

    const WELCOME         = 1;
    const FORGET_PASSWORD = 2;
    const VERIFY_EMAIL    = 3;
    const INVITE_SUB_AFF  = 4;
    const FEEDBACK        = 5;
    const APPROVE_AFF     = 6;

    public static $types = [
        self::WELCOME         => 'Welcome',
        self::FORGET_PASSWORD => 'Forget Password',
        self::VERIFY_EMAIL    => 'Verify Email',
        self::INVITE_SUB_AFF  => 'Invite Sub - Affiliates',
        self::FEEDBACK        => 'Feedback',
        self::APPROVE_AFF     => 'AFF Welcome',
    ];

    protected $casts = [
        'languages'    => 'array',
        'currencies'   => 'array',
        'is_affiliate' => 'boolean',
    ];

    public function checkCurrencySet($currency)
    {
        return in_array($currency, $this->currencies);
    }

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data) {
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }
}
