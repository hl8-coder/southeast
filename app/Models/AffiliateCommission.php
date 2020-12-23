<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class AffiliateCommission extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $dates = [
        'start_at', 'end_at', 'last_access_at',
    ];

    protected $casts = [
        'calculate_setting'             => 'array',
        'profit'                        => 'float',
        'stake'                         => 'float',
        'deposit'                       => 'float',
        'withdrawal'                    => 'float',
        'rebate'                        => 'float',
        'promotion'                     => 'float',
        'rake'                          => 'float',
        'sub_adjustment'                => 'float',
        'affiliate_adjustment'          => 'float',
        'active_count'                  => 'int',
        'transaction_cost'              => 'float',
        'bear_cost'                     => 'float',
        'product_cost'                  => 'float',
        'net_loss'                      => 'float',
        'parent_commission'             => 'float',
        'sub_commission'                => 'float',
        'sub_commission_percent'        => 'float',
        'previous_remain_commission'    => 'float',
        'remain_commission'             => 'float',
        'total_commission'              => 'float',
        'payout_commission'             => 'float',
    ];

    # 代理奖励付款状态 Start
    const STATUS_PENDING = 1; # 系统建立未付款
    const STATUS_RELEASE = 2; # 管理员已同意
    const STATUS_PAID    = 3; # 管理员同意已付款

    public static $statuses = [
        self::STATUS_PENDING => 'pending',
        self::STATUS_RELEASE => 'release',
        self::STATUS_PAID    => 'paid',
    ];
    # 代理奖励付款状态 End

    # 模型关联 start
    public function affiliate()
    {
        return $this->belongsTo(Affiliate::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }
    # 模型关联 end

    # 查询作用域 start
    public function scopeName($query, $value)
    {
        return $query->whereHas('affiliate', function ($query) use ($value) {
            return $query->whereHas('user', function ($query) use ($value) {
                return $query->where("name", $value);
            });
        });
    }

    public function scopeStatus($query, $value)
    {
        return $query->whereHas('affiliate', function ($query) use ($value) {
            return $query->whereHas('user', function ($query) use ($value) {
                return $query->where("status", $value);
            });
        });
    }

    public function scopeMonth($query, $value)
    {
        return $query->where('start_at', $value);
    }
    # 查询作用域 end

    # 方法 start
    /**
     * 根据起始时间和结束时间判断周期内是否已存在代理分红
     *
     * @param $userId
     * @param $startAt
     * @param $endAt
     * @return bool
     */
    public static function isExistsByDate($userId, $startAt, $endAt)
    {
        return AffiliateCommission::query()->where('user_id', $userId)
            ->where('start_at', $startAt)
            ->where('end_at', $endAt)
            ->exists();
    }
    # 方法 end
}
