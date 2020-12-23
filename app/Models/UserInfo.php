<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;

class UserInfo extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'user_info';

    protected $guarded = [];

    protected $auditInclude = [
        'full_name', 'email', 'phone', 'birth_at', 'gender', 'web_url','address',
    ];

    protected $dates = [
        'email_verified_at', 'phone_verified_at', 'bank_account_verified_at', 'last_login_at', 'profile_verified_at', 'claimed_verify_prize_at',
    ];

    public $casts = [
        'describe' => 'array'
    ];
    # 性别
    const GENDER_MALE   = 'male';
    const GENDER_FEMALE = 'female';

    public static $genders = [
        self::GENDER_MALE   => 'male',
        self::GENDER_FEMALE => 'female',
    ];

    public static $gendersForTranslation = [
        self::GENDER_MALE   => 'GENDER_MALE',
        self::GENDER_FEMALE => 'GENDER_FEMALE',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeIsUser($query)
    {
        return $query->where('is_agent', false);
    }

    # 方法 start

    public function generateTags() : array
    {
        $data = $this->getUpdatedEventAttributes();
        return array_keys($data[1]);
    }

    public function updateLastLogin($loginIp, $token, $lastDevice)
    {
        $this->update([
            'last_login_ip' => $loginIp,
            'last_login_at' => now(),
            'old_token'     => $token,
            'last_device'   => $lastDevice,
        ]);
    }

    public function isClaimedVerifyPrize()
    {
        return !is_null($this->claimed_verify_prize_at);
    }

    # 方法 end

}
