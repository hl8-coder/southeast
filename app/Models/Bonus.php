<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class Bonus extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'bonuses';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'is_claim',
        'category',
        'languages',
        'code',
        'platform_code',
        'product_code',
        'effective_start_at',
        'effective_end_at',
        'sign_start_at',
        'sign_end_at',
        'status',
        'bonus_group_id',
        'bonus_group_name',
        'type',
        'rollover',
        'amount',
        'is_auto_hold_withdrawal',
        'cycle',
        'user_type',
        'risk_group_ids',
        'payment_group_ids',
        'user_ids',
        'currencies',
        'admin_name',
    ];

    protected $casts = [
        'currencies'                => 'array',
        'languages'                 => 'array',
        'is_claim'                  => 'bool',
        'is_auto_hold_withdrawal'   => 'bool',
        'status'                    => 'bool',
        'risk_group_ids'            => 'array',
        'payment_group_ids'         => 'array',
        'user_ids'                  => 'array',
        'rollover'                  => 'int',
        'amount'                    => 'float',
    ];

    protected $dates = [
        'effective_start_at', 'effective_end_at', 'sign_start_at', 'sign_end_at',
    ];

    # category
    const CATEGORY_RECRUIT   = 1;
    const CATEGORY_RETENTION = 2;

    public static $categories = [
        self::CATEGORY_RECRUIT      => 'recruit',
        self::CATEGORY_RETENTION    => 'retention',
    ];

    # type
    const TYPE_FIXED    = 1;
    const TYPE_PERCENT  = 2;

    public static $types = [
        self::TYPE_FIXED    => '$',
        self::TYPE_PERCENT  => '%'
    ];

    # 周期
    const CYCLE_WHOLE       = 1;
    const CYCLE_DAILY       = 2;
    const CYCLE_WEEKLY      = 3;
    const CYCLE_MONTHLY     = 4;

    public static $cycles = [
        self::CYCLE_WHOLE       => 'whole',
        self::CYCLE_DAILY       => 'daily',
        self::CYCLE_WEEKLY      => 'weekly',
        self::CYCLE_MONTHLY     => 'monthly',
    ];

    # 会员类型
    const USER_TYPE_ALL              = 1;
    const USER_TYPE_RISK             = 2;
    const USER_TYPE_PAYMENT          = 3;
    const USER_TYPE_RISK_AND_PAYMENT = 4;
    const USER_TYPE_LIST             = 5;

    public static $userTypes = [
        self::USER_TYPE_ALL                 => 'all',
        self::USER_TYPE_RISK                => 'risk_group',
        self::USER_TYPE_PAYMENT             => 'payment_group',
        self::USER_TYPE_RISK_AND_PAYMENT    => 'risk_group_and_payment_group',
        self::USER_TYPE_LIST                => 'list',
    ];

    # 属性修改器 start
    public function setRiskGroupIdsAttribute($riskGroupIds)
    {
        $riskGroupIds = array_map(function($value) {
            return (int)$value;
        }, $riskGroupIds);

        $this->attributes['risk_group_ids'] = json_encode($riskGroupIds);
    }

    public function setPaymentGroupIdsAttribute($paymentGroupIds)
    {
        $paymentGroupIds = array_map(function($value) {
            return (int)$value;
        }, $paymentGroupIds);

        $this->attributes['payment_group_ids'] = json_encode($paymentGroupIds);
    }

    public function setCurrenciesAttribute($currencies)
    {
        $currencies = array_map(function($value) {
            $value['min_transfer']  = (int)$value['min_transfer'];
            $value['max_prize']     = (int)$value['max_prize'];
            $value['deposit_count'] = (int)$value['deposit_count'];
            return $value;
        }, $currencies);

        $this->attributes['currencies'] = json_encode($currencies);
    }
    # 属性修改器 end


    # 模型关联 start
    public function bonusGroup()
    {
        return $this->belongsTo(BonusGroup::class);
    }

    public function product()
    {
        return $this->belongsTo(GamePlatformProduct::class, 'product_code', 'code');
    }

    # 模型关联 end

    # 查询本地作用域 start
    public function scopeEffectiveStartAt($query, $value)
    {
        return $query->where('effective_start_at', '>=', $value);
    }

    public function scopeEffectiveEndAt($query, $value)
    {
        return $query->where('effective_end_at', '<=', $value);
    }
    # 查询本地作用域 end

    # 方法 start
    public static function isUserTypeList($userType)
    {
        return static::USER_TYPE_LIST == $userType;
    }

    public function isEnable()
    {
        return $this->status;
    }

    public function isNeedClaim()
    {
        return $this->is_claim;
    }

    public function isAutoHoldWithdrawal()
    {
        return $this->is_auto_hold_withdrawal;
    }

    public function getCurrencySet($currency)
    {
        return collect($this->currencies)->where('currency', $currency)->first();
    }

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data){
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }
    # 方法 end
}
