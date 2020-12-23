<?php

namespace App\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Log;

class CrmOrder extends Model
{
    use SoftDeletes;
    protected $fillable = [
        'user_id',
        'affiliate_id',
        'type',
        'call_status',
        'tag_admin_id',
        'tag_admin_name',
        'tag_at',
        'admin_id',
        'admin_name',
        'last_save_case_admin_id',
        'last_save_case_admin_name',
        'last_save_case_at',
        'is_auto',
        'affiliated_code',
        'status',
        'batch',
    ];

    public $casts = [
        'is_auto'     => 'boolean',
        'status'      => 'boolean',
        'call_status' => 'boolean',
    ];

    const STATUS_OPEN   = 0;
    const STATUS_LOCKED = 1;
    public static $status = [
        self::STATUS_LOCKED => 'locked',
        self::STATUS_OPEN   => 'open',
    ];

    #纪录类型 这个类型与日表、周表里面到 type 直接关联，如果修改需要同步修改且不能与 resource 冲突
    const TYPE_WELCOME         = 1;
    const TYPE_DAILY_RETENTION = 2;
    const TYPE_RETENTION       = 3;
    const TYPE_NON_DEPOSIT     = 4;
    public static $type = [
        self::TYPE_WELCOME         => 'Welcome',
        self::TYPE_DAILY_RETENTION => 'Daily Retention',
        self::TYPE_RETENTION       => 'Retention',
        self::TYPE_NON_DEPOSIT     => 'Non Deposit',
    ];

    # 状态
    const CALL_STATUS_SUCCESSFUL = 1;
    const CALL_STATUS_FAIL       = 0;

    public static $call_statuses = [
        self::CALL_STATUS_FAIL       => 'Fail',
        self::CALL_STATUS_SUCCESSFUL => 'Successful',
    ];


    public function user()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    public function userInfo()
    {
        return $this->belongsTo(UserInfo::class, 'user_id', 'user_id');
    }


    public function tagAdmin()
    {
        return $this->belongsTo(Admin::class, 'tag_admin_id', 'id');
    }

    public function boAdmin()
    {
        return $this->belongsTo(Admin::class, 'admin_id', 'id');
    }

    public function crmCallLogs()
    {
        return $this->hasMany(CrmCallLog::class);
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class, 'user_id', 'user_id');
    }


    # 查询作用域 start
    public function scopeName($query, $name)
    {
        return $query->whereHas('user', function ($query) use ($name) {
            $query->where('name', 'like', "%{$name}%");
        });
    }

    public function scopeFullName($query, $fullName)
    {
        return $query->whereHas('userInfo', function ($query) use ($fullName) {
            $query->where('full_name', $fullName);
        });
    }

    public function scopeCurrency($query, $currency)
    {
        return $query->whereHas('user', function ($query) use ($currency) {
            $query->where('currency', $currency);
        });
    }

    public function scopeUserStatus($query, $status)
    {
        return $query->whereHas('user', function ($query) use ($status) {
            $query->where('status', $status);
        });
    }

    public function scopeRiskGroupId($query, $riskGroupId)
    {
        return $query->whereHas('user', function ($query) use ($riskGroupId) {
            $query->where('risk_group_id', $riskGroupId);
        });
    }

    public function scopePaymentGroupId($query, $paymentGroupId)
    {
        return $query->whereHas('user', function ($query) use ($paymentGroupId) {
            $query->where('payment_group_id', $paymentGroupId);
        });
    }

    public function scopeAffiliatedCode($query, $affiliateCode)
    {
        return $query->whereHas('user', function ($query) use ($affiliateCode) {
            return $query->where('affiliated_code', $affiliateCode);
        });
    }

    public function scopePhone($query, $phone)
    {
        return $query->whereHas('userInfo', function ($query) use ($phone) {
            $query->where('phone', 'like', "%{$phone}%");
        });
    }

    public function scopeEmail($query, $email)
    {
        return $query->whereHas('userInfo', function ($query) use ($email) {
            $query->where('email', 'like', "%{$email}%");
        });
    }

    public function scopeRegisterStart($query, $registerStart)
    {
        return $query->whereHas('userInfo', function ($query) use ($registerStart) {
            $query->where('created_at', '>=', $registerStart);
        });
    }

    public function scopeRegisterEnd($query, $registerEnd)
    {
        return $query->whereHas('userInfo', function ($query) use ($registerEnd) {
            $query->where('created_at', '<=', $registerEnd);
        });
    }

    public function scopeLastLoginStart($query, $lastLoginStart)
    {
        return $query->whereHas('userInfo', function ($query) use ($lastLoginStart) {
            $query->where('last_login_at', '>=', $lastLoginStart);
        });
    }

    public function scopeLastLoginEnd($query, $lastLoginEnd)
    {
        return $query->whereHas('userInfo', function ($query) use ($lastLoginEnd) {
            $query->where('last_login_at', '<=', $lastLoginEnd);
        });
    }

    public function scopeTagStart($query, $date)
    {
        return $query->where('tag_at', '>=', $date);
    }

    public function scopeTagEnd($query, $date)
    {
        return $query->where('tag_at', '<=', $date);
    }

    public function scopeLastSaveStart($query, $date)
    {
        return $query->where('last_save_case_at', '>=', $date);
    }

    public function scopeLastSaveEnd($query, $date)
    {
        return $query->where('last_save_case_at', '<=', $date);
    }

    public function scopeRegisterIp($query, $value)
    {
        return $query->whereHas('userInfo', function ($query) use ($value) {
            $query->where('register_ip', 'like', "%{$value}%");
        });
    }

    public function scopeDeposit($query, bool $value)
    {
        if ($value) {
            return $query->whereHas('user', function($query){
                $query->whereNotNull('first_deposit_at');
            });
        } else {
            return $query->whereHas('user', function($query){
                $query->whereNull('first_deposit_at');
            });
        }
    }

    public function scopeLastDepositStart($query, $date)
    {
        return $query->whereHas('deposits', function ($query) use ($date) {
            $query->where('status', Deposit::STATUS_RECHARGE_SUCCESS)->where('deposit_at', '>=', $date);
        });    }

    public function scopeLastDepositEnd($query, $data)
    {
        return $query->whereHas('deposits', function ($query) use ($data) {
            $query->where('status', Deposit::STATUS_RECHARGE_SUCCESS)->where('deposit_at', '<=', $data);
        });
    }
    # 查询作用域 end


    /**
     * 一个订单有新的通讯记录后引发的数据变更
     *
     * @param $callStatus
     * @return bool
     *
     * @author  Martin
     * @date    23/7/2020 4:29 am
     * @version viet-214
     */
    public function makeCall($callStatus)
    {
        $admin  = auth('admin')->user();
        $update = [
            'admin_id'                  => $admin->id,
            'admin_name'                => $admin->name,
            'last_save_case_admin_id'   => $admin->id,
            'last_save_case_admin_name' => $admin->name,
            'last_save_case_at'         => now(),
            'call_status'               => $callStatus,
        ];

        if ($this->tag_at == null){
            $update['tag_at'] = now();
        }
        return $this->update($update);
    }

    public function isLocked()
    {
        return $this->status == self::STATUS_LOCKED;
    }

    public function isCalled()
    {
        return $this->call_status != null;
    }

    /**
     * @param $userCollection
     * @param int $type
     * @param bool $isAuto
     * @return bool
     */
    public function insertUsersIntoCrmOrder($userCollection, int $type, bool $isAuto = false)
    {
        $insertData = [];
        $update     = [];
        foreach ($userCollection as $user) {
            $one          = [
                'user_id'    => $user->id,
                'type'       => $type,
                'is_auto'    => $isAuto,
                'batch'      => $user->getLatestCrmOrderBatch() + 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
            $update[]     = [
                'user_id'         => $user->id,
                'affiliate_id'    => $user->parent_id,
                'affiliated_code' => $user->affiliated_code,
            ];
            $insertData[] = $one;
        }

        try {
            if (empty($insertData)) {
                return false;
            }
            batch_insert(app(CrmOrder::class)->getTable(), $insertData);
            self::updateBatch($update);
        } catch (\Exception $exception) {
            Log::error($exception);
            return false;
        }
        return true;
    }
}
