<?php

namespace App\Models;

class GamePlatformTransferDetail extends Model
{
    protected $casts = [
        'is_income'           => 'bool',
        'bonus_amount'        => 'float',
        'amount'              => 'float',
        'conversion_amount'   => 'float',
        'from_before_balance' => 'float',
        'from_after_balance'  => 'float',
        'to_before_balance'   => 'float',
        'to_after_balance'    => 'float',
    ];

    protected $guarded = [];

    # status
    const STATUS_CREATED             = 1; # 转账中
    const STATUS_WAITING             = 2; # 等待第三方
    const STATUS_CHECKING            = 3; # 检查中
    const STATUS_SUCCESS             = 4; # 成功
    const STATUS_FAIL                = 5; # 失败
    const STATUS_WAIT_MANUAL_CONFIRM = 6; # 等待手动确认

    public static $statues = [
        self::STATUS_CREATED             => 'Created',
        self::STATUS_WAITING             => 'Waiting',
        self::STATUS_CHECKING            => 'Checking',
        self::STATUS_SUCCESS             => 'Successful',
        self::STATUS_FAIL                => 'Fail',
        self::STATUS_WAIT_MANUAL_CONFIRM => 'Manual Confirm',
    ];

    /**
     * 需要人工确认的状态
     *
     * @var array
     */
    public static $needManualCheckingStatuses = [
        self::STATUS_WAITING,
        self::STATUS_CHECKING,
        self::STATUS_WAIT_MANUAL_CONFIRM,
    ];

    # 模型关联 start
    public function userBonusPrize()
    {
        return $this->belongsTo(UserBonusPrize::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function platform()
    {
        return $this->belongsTo(GamePlatform::class, 'platform_code', 'code');
    }
    # 模型关联 end

    # 查询作用域 start
    public function scopeFoStatus($query, $value)
    {
        $status = [
            3 => [
                self::STATUS_CREATED,
                self::STATUS_WAITING,
                self::STATUS_CHECKING,
                self::STATUS_WAIT_MANUAL_CONFIRM,
            ],
            1 => [self::STATUS_SUCCESS],
            2 => [self::STATUS_FAIL],
        ];
        return $query->whereIn('status', $status[$value]);
    }

    public function scopeDateFrom($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeDateTo($query, $value)
    {
        return $query->where('created_at', '<', $value);
    }

    public function scopeWaiting($query)
    {
        return $query->where('status', static::STATUS_WAITING);
    }

    public function scopePlatformCode($query, $platformCode)
    {
        return $query->where('from', $platformCode)->orWhere('to', $platformCode);
    }

    public function scopeCurrency($query, $value)
    {
        return $query->where('user_currency', $value);
    }

    # 查询作用域 end

    # 方法 start
    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $model->order_no = '6' . str_pad($model->id, 10, '0', STR_PAD_LEFT);
            $model->save();
        });
    }

    public function isFail()
    {
        return static::STATUS_FAIL == $this->status;
    }

    public function isSuccess()
    {
        return static::STATUS_SUCCESS == $this->status;
    }

    public function isWait()
    {
        return static::STATUS_WAITING == $this->status;
    }

    public function isChecking()
    {
        return static::STATUS_CHECKING == $this->status;
    }

    public function isWaitingConfirm()
    {
        return static::STATUS_WAIT_MANUAL_CONFIRM == $this->status;
    }

    public function isNeedCheck()
    {
        return in_array($this->status, [static::STATUS_WAITING, static::STATUS_FAIL, static::STATUS_SUCCESS]);
    }

    public function isNeedManualCheck()
    {
        return in_array($this->status, static::$needManualCheckingStatuses);
    }

    public function isAdjustmentDetail()
    {
        return Adjustment::query()->where('platform_transfer_detail_id', $this->id)->exists();
    }


    /**
     * 是否为第三方入账
     *
     * @return mixed
     */
    public function isIncome()
    {
        return $this->is_income;
    }

    public function success($adminName = null, $remark = '')
    {
        $data = [
            'from_after_balance' => $this->from_before_balance - $this->amount + $this->bonus_amount,
            'to_after_balance'   => $this->to_before_balance + $this->conversion_amount,
            'status'             => static::STATUS_SUCCESS,
            'remark'             => $remark,
        ];

        if (!empty($adminName)) {
            $data['admin_name'] = $adminName;
        }

        return $this->update($data);
    }

    public function fail($sysRemark, $remark, $adminName = null)
    {
        $data = [
            'remark'     => $remark,
            'sys_remark' => $sysRemark,
            'status'     => static::STATUS_FAIL,
            'admin_name' => $adminName,
        ];

        if (!empty($adminName)) {
            $data['admin_name'] = $adminName;
        }

        return $this->update($data);
    }

    public function waiting($sysRemark = '')
    {
        return $this->update([
            'sys_remark' => $sysRemark,
            'status'     => static::STATUS_WAITING,
        ]);
    }

    public function waitManualConfirm($sysRemark = '')
    {
        return $this->update([
            'sys_remark' => $sysRemark,
            'status'     => static::STATUS_WAIT_MANUAL_CONFIRM,
        ]);
    }

    public function checking()
    {
        $result = $this->setPrimaryKeyQuery()
            ->where('status', static::STATUS_WAITING)
            ->update([
                'status' => static::STATUS_CHECKING,
            ]);

        if ($result) {
            $this->increment('check_times');
        }

        return $result;
    }

    /**
     * 获取转入金额
     *
     * @return mixed
     */
    public function getTransferAmount()
    {
        return $this->amount - $this->bonus_amount;
    }
    # 方法 end
}
