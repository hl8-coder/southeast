<?php

namespace App\Models;

class UserLoginLog extends Model
{
    protected $guarded = [];

    const SUCCESS = 1;
    const FAILED  = 0;

    public static $loginStatus = [
        self::SUCCESS => 'Success',
        self::FAILED  => 'Failed',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeCurrency($query, $currency)
    {
        return $query->whereHas('user', function($query) use ($currency) {
            $query->where('currency', $currency);
        });
    }

    public function scopeMemberCode($query, $memberCode)
    {
        return $query->where("user_name" , "like", "%" . $memberCode . "%");
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('success_login', $status);
    }

    public function scopeAffiliatedCode($query, $affiliatedCode)
    {
        return $query->whereHas('user', function($query) use ($affiliatedCode) {
            $query->where('affiliated_code', $affiliatedCode);
        });
    }

    public function scopeAffiliateCode($query, $affiliateCode)
    {
        return $query->whereHas('user', function($query) use ($affiliateCode) {
            $query->where('affiliated_code', $affiliateCode);
        });
    }

    public function scopeStartAt($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeEndAt($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }
}
