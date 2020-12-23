<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;
use OwenIt\Auditing\Contracts\Auditable;
use Illuminate\Notifications\Notifiable;
use OwenIt\Auditing\Redactors\LeftRedactor;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable implements JWTSubject, Auditable
{
    use Notifiable {
        notify as protected laravelNotify;
    }

    use \OwenIt\Auditing\Auditable;

    # 状态
    const STATUS_ACTIVE   = 1;
    const STATUS_BLOCKED  = 2;
    const STATUS_LOCKED   = 3;
    const STATUS_INACTIVE = 4;
    const STATUS_PENDING  = 5;

    #设备类型
    const DEVICE_PC      = 1;
    const DEVICE_MOBILE  = 2;
    const DEVICE_ANDROID = 3;
    const DEVICE_IOS     = 4;

    public static $devices = [
        self::DEVICE_PC      => 'PC',
        self::DEVICE_MOBILE  => 'MOBILE',
        self::DEVICE_ANDROID => 'ANDROID',
        self::DEVICE_IOS     => 'IOS',
    ];

    public static $statuses = [
        self::STATUS_ACTIVE   => 'active',
        self::STATUS_BLOCKED  => 'blocked',
        self::STATUS_LOCKED   => 'locked',
        self::STATUS_INACTIVE => 'inactive',
    ];

    public static $affiliateStatuses = [
        self::STATUS_ACTIVE   => 'active',
        self::STATUS_BLOCKED  => 'blocked',
        self::STATUS_LOCKED   => 'locked',
        self::STATUS_INACTIVE => 'inactive',
        self::STATUS_PENDING  => 'pending',
    ];

    # agent
    const AGENT_0 = 0;
    const AGENT_1 = 1;

    public static $agent = [
        self::AGENT_0 => 'Member',
        self::AGENT_1 => 'Affiliate',
    ];

    # odds
    const ODDS_CHINA      = 1;
    const ODDS_INDONESIAN = 2;
    const ODDS_AMERICAN   = 3;
    const ODDS_DECIMAL    = 4;
    const ODDS_MALAY      = 5;

    public static $odds = [
        self::ODDS_CHINA      => 'China Odds',
        self::ODDS_INDONESIAN => 'Indonesian Odds',
        self::ODDS_AMERICAN   => 'American Odds',
        self::ODDS_DECIMAL    => 'Decimal Odds',
        self::ODDS_MALAY      => 'Malay Odds',
    ];

    public static $oddsForTranslation = [
        self::ODDS_CHINA      => 'ODDS_CHINA',
        self::ODDS_INDONESIAN => 'ODDS_INDONESIAN',
        self::ODDS_AMERICAN   => 'ODDS_AMERICAN',
        self::ODDS_DECIMAL    => 'ODDS_DECIMAL',
        self::ODDS_MALAY      => 'ODDS_MALAY',
    ];

    const SECURITY_QUESTION_1 = 1;
    const SECURITY_QUESTION_2 = 2;
    const SECURITY_QUESTION_3 = 3;
    const SECURITY_QUESTION_4 = 4;
    const SECURITY_QUESTION_5 = 5;
    const SECURITY_QUESTION_6 = 6;
    const SECURITY_QUESTION_7 = 7;

    public static $securityQuestions = [
        self::SECURITY_QUESTION_1 => 'What was your childhood nickname?',
        self::SECURITY_QUESTION_2 => 'In what town was your first job?',
        self::SECURITY_QUESTION_3 => 'What is your favorite movie?',
        self::SECURITY_QUESTION_4 => 'What year did you graduate high school?',
        self::SECURITY_QUESTION_5 => 'What is your birth month?',
        self::SECURITY_QUESTION_6 => 'What is your first school name?',
        self::SECURITY_QUESTION_7 => 'Who was your childhood hero?',
    ];

    public static $securityQuestionForTranslation = [
        self::SECURITY_QUESTION_1 => 'SECURITY_QUESTION_1',
        self::SECURITY_QUESTION_2 => 'SECURITY_QUESTION_2',
        self::SECURITY_QUESTION_3 => 'SECURITY_QUESTION_3',
        self::SECURITY_QUESTION_4 => 'SECURITY_QUESTION_4',
        self::SECURITY_QUESTION_5 => 'SECURITY_QUESTION_5',
        self::SECURITY_QUESTION_6 => 'SECURITY_QUESTION_6',
        self::SECURITY_QUESTION_7 => 'SECURITY_QUESTION_7',
    ];

    protected $auditInclude = [
        'risk_group_id', 'payment_group_id', 'vip_id', 'reward_id', 'language', 'password', 'status', 'is_agent', 'security_question', 'security_question_answer', 'odds'
    ];

    protected $attributeModifiers = [
        'password' => LeftRedactor::class,
    ];

    public function getAuditFields()
    {
        return $this->auditInclude;
    }

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $guarded = [];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'fund_password', 'password',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'is_agent'                => 'boolean',
        'is_need_change_password' => 'boolean',
        'is_test'                 => 'boolean',
        'security_question'       => 'integer',
    ];

    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }

    public function notify($instance)
    {
        if (method_exists($instance, 'toDatabase')) {
            $this->increment('notification_count');
        }

        $this->laravelNotify($instance);
    }

    # 模型关联 start
    public function info()
    {
        return $this->hasOne(UserInfo::class);
    }

    public function riskGroup()
    {
        return $this->belongsTo(RiskGroup::class);
    }

    public function paymentGroup()
    {
        return $this->belongsTo(PaymentGroup::class);
    }

    public function bankAccounts()
    {
        return $this->hasMany(UserBankAccount::class);
    }

    public function mpayNumbers()
    {
        return $this->hasMany(UserMpayNumber::class);
    }

    public function account()
    {
        return $this->hasOne(UserAccount::class);
    }

    public function parentUser()
    {
        return $this->belongsTo(User::class, "parent_id", "id");
    }

    # 下级会员
    public function subUsers()
    {
        return $this->hasMany(User::class, 'parent_id', 'id')->where("is_agent", false);
    }

    # 下级代理
    public function subAffiliates()
    {
        return $this->hasMany(User::class, 'parent_id', 'id')->where("is_agent", true);
    }

    # 所有下级
    public function subAll()
    {
        return $this->hasMany(User::class, 'parent_id', 'id');
    }

    public function affiliate()
    {
        return $this->hasOne(Affiliate::class);
    }

    public function vip()
    {
        return $this->belongsTo(Vip::class);
    }

    public function reward()
    {
        return $this->belongsTo(Reward::class);
    }

    public function gamePlatformUsers()
    {
        return $this->hasMany(GamePlatformUser::class);
    }

    public function profileRemarks()
    {
        return $this->hasMany(ProfileRemark::class);
    }

    # 充值提领相关备注
    public function remarks()
    {
        return $this->hasMany(Remark::class);
    }

    # 充值
    public function deposits()
    {
        return $this->hasMany(Deposit::class);
    }

    public function depositsSuccessLatest($limit = 1)
    {
        return $this->hasMany(Deposit::class)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->orderByDesc('deposit_at')
            ->limit($limit);
    }

    public function depositsSuccessFirst($limit = 1)
    {
        return $this->hasMany(Deposit::class)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->orderBy('deposit_at', 'asc')
            ->limit($limit);
    }

    public function report()
    {
        return $this->hasOne(UserPlatformTotalReport::class)->where('platform_code', UserAccount::MAIN_WALLET);
    }

    public function lastDeposits($skipDepositId, $limit)
    {
        return $this->deposits()->latest()->where("id", "<>", $skipDepositId)->take($limit)->get();
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class);
    }

    public function userRisks()
    {
        return $this->hasMany(UserRisk::class);
    }

    public function userLoginLogs()
    {
        return $this->hasMany(UserLoginLog::class);
    }

    public function crmOrder()
    {
        return $this->hasMany(CrmOrder::class);
    }

    # 模型关联 end

    # 查询作用域 start
    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }

    public function scopeEmail($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('email', 'like', '%' . $value . '%');
        });
    }

    public function scopePhone($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('phone', 'like', '%' . $value . '%');
        });
    }

    public function scopeRegisterUrl($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('register_url', 'like', '%' . $value . '%');
        });
    }

    public function scopeRegisterIp($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('register_ip', 'like', '%' . $value . '%');
        });
    }

    public function scopeFullName($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('full_name', 'like', '%' . $value . '%');
        });
    }

    public function scopeIsAgent($query)
    {
        return $query->where('is_agent', true);
    }

    public function scopeIsUser($query)
    {
        return $query->where('is_agent', false);
    }

    public function scopeDeposit($query, $value)
    {
        if ($value) {
            return $query->deposited();
        } else {
            return $query->noDeposit();
        }
    }

    public function scopeNoDeposit($query)
    {
        return $query->whereNotExists(function ($query) {
            $query->select(DB::raw(1))->from('user_platform_total_reports')
                ->where('platform_code', UserAccount::MAIN_WALLET)
                ->where('deposit', '>', 0)
                ->whereRaw('user_platform_total_reports.user_id = users.id');
        });
    }

    public function scopeDeposited($query)
    {
        return $query->whereExists(function ($query) {
            $query->select(DB::raw(1))->from('user_platform_total_reports')
                ->where('platform_code', UserAccount::MAIN_WALLET)
                ->where('deposit', '>', 0)
                ->whereRaw('user_platform_total_reports.user_id = users.id');
        });
    }

    public function scopeBehaviour($query, $value)
    {
        return $query->whereHas('userRisks', function ($query) use ($value) {
            return $query->where('behaviour', $value);
        });
    }

    public function scopeRisk($query, $value)
    {
        return $query->whereHas('userRisks', function ($query) use ($value) {
            return $query->where('risk', $value);
        });
    }

    public function scopeLastLoginStartAt($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('last_login_at', '>=', $value);
        });
    }

    public function scopeLastLoginEndAt($query, $value)
    {
        return $query->whereHas('info', function ($query) use ($value) {
            return $query->where('last_login_at', '<=', $value);
        });
    }

    public function scopeCrmOrderTypeDoesntHave($query, $type)
    {
        return $query->whereDoesntHave('crmOrder', function ($query) use ($type) {
            return $query->where('type', $type);
        });
    }

    public function scopeCrmOrderType($query, $type)
    {
        return $query->whereHas('crmOrder', function ($query) use ($type) {
            return $query->where('type', $type);
        });
    }
    public function scopeCrmOrderTypeNoCallDoesntHave($query, $type)
    {
        return $query->whereDoesntHave('crmOrder', function ($query) use ($type) {
            return $query->where('type', $type)->whereNull('call_status');
        });
    }

    public function scopeCrmOrderTypeAndBatchDoesntHave($query, $type, $batch)
    {
        return $query->whereDoesntHave('crmOrder', function($query) use ($type, $batch){
            return $query->where('type', $type)->where('batch', $batch);
        });
    }
    # 查询作用域 end

    # 方法 start

    # 判断是否是vip
    public function isVip()
    {
        $initVipId = Config::findValue('default_vip_id', null);

        if (!empty($initVipId)) {
            return $this->vip_id > $initVipId;
        } else {
            return !empty($initVipId);
        }
    }

    public function updatePassword($password)
    {
        return $this->update([
            'password' => bcrypt($password),
        ]);
    }

    public function updateStatus($status)
    {
        return $this->update([
            'status' => $status,
        ]);
    }

    public function isAuthorOf($model)
    {
        return $this->id === $model->user_id;
    }

    public function markAsRead(DatabaseNotification $notification)
    {
        $notification->markSingleAsRead();
        $this->newQuery()->where('notification_count', '>', 0)->decrement('notification_count');
    }

    public function isDirectChild($user)
    {
        return $this->id == $user->parent_id;
    }

    /**
     * 更新vip
     *
     * @param $vipId
     */
    public function updateVip($vipId)
    {
        $this->update([
            'vip_id' => $vipId,
        ]);
    }

    public function updateReward($rewardId)
    {
        $this->update([
            'reward_id' => $rewardId,
        ]);
    }

    public function isReachBankAccountLimit()
    {
        $limit = Config::findValue('user_bank_account_limit', 5);
        $count = $this->bankAccounts()->active()->count();
        return $count >= $limit;
    }

    public function isReachMpayNumberLimit()
    {
        $limit = Config::findValue('user_mpay_number_limit', 2);
        $count = $this->mpayNumbers()->active()->count();
        return $count >= $limit;
    }

    # 是否有未处理remark
    public function isUnprocessedRemarkLimit()
    {
        $count = $this->remarks()->where("remove_reason", '')->count();
        return $count >= 1;
    }

    public function isCanLogin()
    {
        return $this->status == static::STATUS_ACTIVE;
    }

    public function isTestUser()
    {
        return $this->is_test;
    }

    public function isUser()
    {
        return false == $this->is_agent;
    }

    /**
     * 设置需要强制更新密码
     *
     * @return bool
     */
    public function setNeedChangePassword()
    {
        return $this->update([
            'is_need_change_password' => true,
        ]);
    }

    /**
     * 撤消设置需要强制更新密码
     *
     * @return bool
     */
    public function cancelNeedChangePassword()
    {
        return $this->update([
            'is_need_change_password' => false,
        ]);
    }

    # 方法 end

    public function generateTags(): array
    {
        $data = $this->getUpdatedEventAttributes();
        return array_keys($data[1]);
    }

    /**
     * 获取用户在crm order 中当前最新的批次
     * @return int
     */
    public function getLatestCrmOrderBatch():int
    {
        $crmOrder = $this->crmOrder()->orderByDesc('batch')->first();
        return $crmOrder ? $crmOrder->batch : 0;
    }

    public function hasSuccessDeposit():bool
    {
        return $this->deposits()->where('status', Deposit::STATUS_RECHARGE_SUCCESS)->exists();
    }

    public function updateFirstDepositTime($time)
    {
        if ($this->first_deposit_at) {
            return;
        }
        $this->first_deposit_at = $time;
        $this->save();
    }
}
