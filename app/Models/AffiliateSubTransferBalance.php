<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class AffiliateSubTransferBalance extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    # status
    const STATUS_SUCCESS = 'Success';
    const STATUS_Failed  = 'Failed';

    public static $statuses = [
        self::STATUS_SUCCESS => 'Success',
        self::STATUS_Failed  => 'Failed',
    ];

    # method
    const METHOD_ATM = 'ATM';

    public static $methods = [
        self::METHOD_ATM => 'ATM',
    ];

    protected $dates = [
    ];

    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    # 查询作用域 start
    public function scopeCode($query, $value)
    {
        return $query->whereHas('affiliate', function ($query) use ($value) {
            return $query->where("code", $value);
        });
    }

    public function scopeToUser($query, $value)
    {
        return $query->whereHas('toUser', function ($query) use ($value) {
            return $query->where("name", $value);
        });
    }

    public function scopeIsAgent($query, $value)
    {
        return $query->whereHas('toUser', function ($query) use ($value) {
            return $query->where("is_agent", $value);
        });
    }

    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }
    # 查询作用域 end
}
