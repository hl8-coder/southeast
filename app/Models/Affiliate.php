<?php

namespace App\Models;

use Illuminate\Database\Query\Builder;
use OwenIt\Auditing\Contracts\Auditable;
use Carbon\Carbon;

class Affiliate extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $table = 'affiliates';

    protected $casts = [
        'commission_setting' => 'array',
    ];

    protected $fillable = [
        'user_id',
        'code',
        'refer_by_code',
        'commission_setting',
        'cs_status',
        'cs_cycles',
        'admin_name',
        'cs_status_last_updated_at',
        'cs_last_updated_name',
        'is_become_user',
        'is_fund_open',
    ];

    protected $dates = [];

    # 代理奖励签约状态 Start
    const CS_STATUS_PENDING  = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         # 用户建立未审查
    const CS_STATUS_REJECTED = 2;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         # 管理员拒绝
    const CS_STATUS_APPROVED = 3;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         # 管理员同意

    public static $csStatuses = [
        self::CS_STATUS_PENDING  => 'pending',
        self::CS_STATUS_REJECTED => 'rejected',
        self::CS_STATUS_APPROVED => 'approved',
    ];
    # 代理奖励签约状态 End

    # 代理奖励签约週期 Start
    const CS_CYCLE_NO_CAL       = 0;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                            # 不计算, 若是大户手动结算可用这选项
    const CS_CYCLE_ONE_MONTH    = 1;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                         # 月结算
    const CS_CYCLE_HALF_MONTH   = 2;                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                                        # 半月结算

    public static $csCycles = [
        self::CS_CYCLE_NO_CAL       => 'no calculation',
        self::CS_CYCLE_ONE_MONTH    => 'one month',
        self::CS_CYCLE_HALF_MONTH   => 'half month',
    ];
    # 代理奖励签约週期 END

    # 模型关联 start
    public function parentUser()
    {
        return $this->user();
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }

    public function userAccount()
    {
        return $this->belongsTo(UserAccount::class, 'user_id', 'user_id');
    }

    public function bankAccount()
    {
        return $this->belongsTo(UserBankAccount::class, 'user_id', 'user_id');
    }

    public function subUsers()
    {
        return $this->hasMany(User::class, 'parent_id', 'user_id')->where("is_agent", false);
    }

    public function subAffiliates()
    {
        return $this->hasMany(User::class, 'parent_id', 'user_id')->where("is_agent", true);
    }

    public function remarks()
    {
        return $this->hasMany(AffiliateRemark::class);
    }

    public function commissions()
    {
        return $this->hasMany(AffiliateCommission::class);
    }

    public function trackingStatistics()
    {
        return $this->hasMany(TrackingStatistic::class, 'user_id', 'user_id');
    }

    # 模型关联 End

    # 查询作用域 start
    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }

    public function scopeStatus($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            $query->where('status', $value);
        });
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            $query->where('currency', $value);
        });
    }

    public function scopePhone($query, $value)
    {
        return $query->whereHas('userInfo', function ($query) use ($value) {
            $query->where('phone', $value);
        });
    }

    public function scopeDescribeSwitchLanguage($query, $value)
    {
        return $query->whereHas('userInfo', function ($query) use ($value) {
            $query->where('describe', 'like', "%\"{$value}\"%");
        });
    }

    public function scopeWebUrl($query, $value)
    {
        return $query->whereHas('userInfo', function ($query) use ($value) {
            $query->where('web_url', 'like', "%" . $value . "%");
        });
    }

    public function scopeName($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            $query->where('name', 'like', "%" . $value . "%");
        });
    }

    public function scopeEmail($query, $value)
    {
        return $query->whereHas('userInfo', function ($query) use ($value) {
            $query->where('email', $value);
        });
    }

    public function scopeCreatedAt($query, $value)
    {
        $start_date = (new Carbon($value))->toDateString();
        $end_date   = (new Carbon($value))->addDay()->toDateString();
        return $query->where("created_at", '>=', $start_date)
            ->where("created_at", '<', $end_date);
    }

    public function scopeParentName($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            return $query->whereHas('parentUser', function ($query) use ($value) {
                return $query->where('name', $value);
            });
        });
    }

    # 查询作用域 end

    # 方法 start
    public function isActive()
    {
        return $this->cs_status == static::CS_STATUS_APPROVED;
    }
    # 方法 end
}
