<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;

class UserMpayNumber extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $dates = [
        'last_used_at',
    ];

    protected $auditInclude = [
        'status',
    ];

    protected $fillable = [
        'area_code', 'number',
    ];

    # 状态
    const STATUS_ACTIVE     = 1;
    const STATUS_INACTIVE   = 2;
    const STATUS_DELETED    = 3;

    public static $statuses = [
        self::STATUS_ACTIVE     => 'ACTIVE',
        self::STATUS_INACTIVE   => 'INACTIVE',
        self::STATUS_DELETED    => 'DELETED',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    public function scopeUserName($query, $value)
    {
        return $query->whereHas('user', function($query) use ($value) {
            return $query->where('name', $value);
        });
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            $query->where('currency', $value);
        });
    }

    public function updateLastUsedAt()
    {
        $this->update([
            'last_used_at' => now(),
        ]);
    }

    public function updateStatus($status)
    {
        $this->update([
            'status' => $status,
        ]);
    }

    public static function isAccountExists($accountNo)
    {
        return static::query()->where('account_no', $accountNo)->active()->exists();
    }

    public function isActive()
    {
        return $this->status == static::STATUS_ACTIVE;
    }

    /**
     * 获取用户可用Mpay数量
     * @author wythe
     * @param $userId
     * @return int
     */
    public static function getActiveCounts($userId)
    {
        return static::query()
            ->where('user_id', $userId)
            ->where('status', static::STATUS_ACTIVE)
            ->count();
    }

    /**
     * 获取可用的mpay账号
     *
     * @param $userId
     * @return mixed
     */
    public static function getActiveByUserId($userId)
    {
        return static::query()->where('user_id', $userId)
            ->where('status', static::STATUS_ACTIVE)
            ->get();
    }
}
