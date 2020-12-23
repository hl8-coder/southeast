<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class UserBankAccount extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $dates = [
        'last_used_at', 'deleted_at',
    ];

    protected $auditInclude = [
        'user_id', 'bank_id', 'province', 'city', 'branch', 'account_no', 'account_name', 'is_preferred', 'status'
    ];

    protected $fillable = [
        'user_id', 'bank_id', 'province', 'city', 'branch', 'account_no', 'account_name', 'is_preferred', 'status', 'deleted_at'
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

    public function getAuditFields()
    {
        return $this->auditInclude;
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function scopeActive($query)
    {
        return $query->where('status', static::STATUS_ACTIVE);
    }

    public function scopeAccountNo($query, $value)
    {
        return $query->whereRaw(DB::raw("RIGHT(account_no, 4)=$value"));
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

    public function delete()
    {
        return $this->update([
            'status' => static::STATUS_DELETED,
            'deleted_at' => now(),
        ]);
    }

    public static function isAccountExists($accountNo)
    {
        return static::query()->where('account_no', $accountNo)->active()->exists();
    }

    public function accountExists($accountNo, $uid)
    {
        return static::query()->where([
            ['account_no', $accountNo],
            ['user_id', '!=', $uid]
        ])->active()->exists();
    }

    public function isActive()
    {
        return $this->status == static::STATUS_ACTIVE;
    }

    public function isInActive()
    {
        return $this->status == static::STATUS_INACTIVE;
    }

    /**
     * 获取用户可用银行卡数量
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

    public function generateTags(): array
    {
        $data = $this->getUpdatedEventAttributes();
        return array_keys($data[1]);
    }
}
