<?php

namespace App\Models;

use App\Models\Traits\TurnoverRequirementTrait;

class Adjustment extends Model
{
    use TurnoverRequirementTrait;

    public static $reportMappingType = Report::TYPE_CLOSE_ADJUSTMENT_BET;

    protected $fillable = [
        'user_id',
        'user_name',
        'order_no',
        'type',
        'category',
        'platform_code',
        'product_code',
        'amount',
        'status',
        'created_admin_name',
        'remark',
        'reason',
        'related_order_no',
        'turnover_closed_value',
        'turnover_current_value',
        'turnover_closed_at',
        'turnover_closed_admin_name',
        'is_turnover_closed',
        'verified_admin_name',
        'platform_transfer_detail_id',
        'verified_at',
        'batch_adjustment_id',
    ];

    protected $casts = [
        'amount'                 => 'float',
        'is_turnover_closed'     => 'boolean',
        'turnover_closed_value'  => 'float',
        'turnover_current_value' => 'float',
    ];

    protected $dates = [
        'turnover_closed_at', 'verified_at',
    ];

    # type
    const TYPE_DEPOSIT  = 1;
    const TYPE_WITHDRAW = 2;

    public static $types = [
        self::TYPE_DEPOSIT  => 'Deposit',
        self::TYPE_WITHDRAW => 'Withdrawal',
    ];

    public static $frontTypes = [
        self::TYPE_DEPOSIT  => '+',
        self::TYPE_WITHDRAW => '-',
    ];

    public static $typeMappingIsIncomes = [
        self::TYPE_DEPOSIT  => true,
        self::TYPE_WITHDRAW => false,
    ];

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {
            # 流水关闭值判断
            if (empty($model->turnover_closed_value)) {
                $model->is_turnover_closed = true;
            }
        });

        static::created(function ($model) {
            $model->update([
                'order_no' => static::findCreatedOrderNo(static::TXN_ID_ADJUSTMENT, $model->id),
            ]);
        });
    }

    # category
    const CATEGORY_WD               = 1;
    const CATEGORY_REIMBURSEMENT    = 2;
    const CATEGORY_PROMOTION        = 3;
    const CATEGORY_REBATE           = 4;
    const CATEGORY_DEPOSIT          = 5;
    const CATEGORY_WELCOME_BONUS    = 6;
    const CATEGORY_BONUS_HUNTER     = 7;
    const CATEGORY_NEUTRALIZATION   = 8;
    const CATEGORY_LIFT_OFF         = 9;
    const CATEGORY_RETENTION        = 10;
    const CATEGORY_ACCOUNT_SAFETY   = 11;
    const CATEGORY_SYSTEM_REFUND    = 12;
    const CATEGORY_AFF_PROMOTION    = 13;

    public static $categories = [
        self::CATEGORY_WD             => 'WD',
        self::CATEGORY_REIMBURSEMENT  => 'Reimbursement',
        self::CATEGORY_PROMOTION      => 'Promotion',
        self::CATEGORY_REBATE         => 'Rebate',
        self::CATEGORY_DEPOSIT        => 'Deposit',
        self::CATEGORY_WELCOME_BONUS  => 'Welcome Bonus',
        self::CATEGORY_BONUS_HUNTER   => 'Bonus Hunter',
        self::CATEGORY_NEUTRALIZATION => 'Neutralization',
        self::CATEGORY_LIFT_OFF       => 'Lift off',
        self::CATEGORY_RETENTION      => 'Retention',
        self::CATEGORY_ACCOUNT_SAFETY => 'Account safety',
        self::CATEGORY_SYSTEM_REFUND  => 'System refund',
        self::CATEGORY_AFF_PROMOTION  => 'AFF Promotion',
    ];

    public static $userCategories = [
        self::CATEGORY_WD             => 'WD',
        self::CATEGORY_REIMBURSEMENT  => 'Reimbursement',
        self::CATEGORY_PROMOTION      => 'Promotion',
        self::CATEGORY_REBATE         => 'Rebate',
        self::CATEGORY_DEPOSIT        => 'Deposit',
        self::CATEGORY_WELCOME_BONUS  => 'Welcome Bonus',
        self::CATEGORY_BONUS_HUNTER   => 'Bonus Hunter',
        self::CATEGORY_NEUTRALIZATION => 'Neutralization',
        self::CATEGORY_LIFT_OFF       => 'Lift off',
        self::CATEGORY_RETENTION      => 'Retention',
        self::CATEGORY_ACCOUNT_SAFETY => 'Account safety',
        self::CATEGORY_SYSTEM_REFUND  => 'System refund',
    ];

    public static $affiliateCategory = [
        self::CATEGORY_AFF_PROMOTION  => 'AFF Promotion',
    ];

    public static $mappingPlatformReportType = [
        self::CATEGORY_WD             => Report::TYPE_WITHDRAWAL,
        self::CATEGORY_DEPOSIT        => Report::TYPE_DEPOSIT,
        self::CATEGORY_PROMOTION      => Report::TYPE_PROMOTION,
        self::CATEGORY_WELCOME_BONUS  => Report::TYPE_PROMOTION,
        self::CATEGORY_RETENTION      => Report::TYPE_PROMOTION,
        self::CATEGORY_ACCOUNT_SAFETY => Report::TYPE_PROMOTION,
    ];

    public static $mappingProductReportType = [
        self::CATEGORY_REBATE         => Report::TYPE_REBATE,
    ];

    /**
     * 不记录报表的类型
     *
     * @var array
     */
    public static $mappingNoReportType = [
        self::CATEGORY_BONUS_HUNTER,
        self::CATEGORY_LIFT_OFF,
        self::CATEGORY_NEUTRALIZATION,
    ];

    /**
     * 需要检查流水关闭的分类
     *
     * @var array
     */
    public static $checkTurnoverClosedCategories = [
        self::CATEGORY_PROMOTION,
        self::CATEGORY_ACCOUNT_SAFETY,
        self::CATEGORY_WELCOME_BONUS,
    ];

    # status
    const STATUS_PENDING        = 1;
    const STATUS_SUCCESSFUL     = 2;
    const STATUS_REJECT         = 3;
    const STATUS_FAIL           = 4;
    const STATUS_WAITING_CHECK  = 5;
    const STATUS_ADJUSTING      = 6;

    public static $statuses = [
        self::STATUS_PENDING        => 'Pending',
        self::STATUS_SUCCESSFUL     => 'Successful',
        self::STATUS_REJECT         => 'Rejected',
        self::STATUS_FAIL           => 'Fail',
        self::STATUS_WAITING_CHECK  => 'Waiting Check',
        self::STATUS_ADJUSTING      => 'Adjusting',
    ];

    /**
     * 需要检查流水的状态
     *
     * @var array
     */
    public static $checkStatuses = [
        self::STATUS_PENDING,
        self::STATUS_SUCCESSFUL,
        self::STATUS_WAITING_CHECK,
    ];

    /**
     * 可显示审核为系统状态
     *
     * @var array
     */
    public static $showSystemStatuses = [
        self::STATUS_SUCCESSFUL,
        self::STATUS_REJECT,
        self::STATUS_FAIL,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function turnoverRequirement()
    {
        return $this->morphOne(TurnoverRequirement::class, 'requireable');
    }

    public function reject($remark, $adminName)
    {
        return $this->update([
            'status'              => static::STATUS_REJECT,
            'remark'              => $remark,
            'verified_admin_name' => $adminName,
            'verified_at'         => now(),
        ]);
    }

    public function approve($adminName='', $remark='')
    {

        $data = [
            'status'        => static::STATUS_SUCCESSFUL,
            'remark'        => $remark,
            'verified_at'   => now(),
        ];

        if (!empty($adminName)) {
            $data['verified_admin_name'] = $adminName;
        }

        return $this->update($data);
    }

    public function fail($adminName='', $remark='')
    {
        $data = [
            'status'        => static::STATUS_FAIL,
            'remark'        => $remark,
            'verified_at'   => now(),
        ];

        if (!empty($adminName)) {
            $data['verified_admin_name'] = $adminName;
        }

        return $this->update($data);
    }

    public function waitingCheck($adminName='', $remark='')
    {
        $data = [
            'status'        => static::STATUS_WAITING_CHECK,
            'remark'        => $remark,
            'verified_at'   => now(),
        ];

        if (!empty($adminName)) {
            $data['verified_admin_name'] = $adminName;
        }

        return $this->update($data);
    }

    public function adjusting() {
        $this->update([
            'status' => static::STATUS_ADJUSTING,
        ]);
    }

    public function isDeposit()
    {
        return self::TYPE_DEPOSIT == $this->type;
    }

    public function isWithdrawal()
    {
        return self::TYPE_WITHDRAW == $this->type;
    }

    /**
     * 获取状态类型
     *
     * @return int
     */
    public function findTransactionType()
    {
        return static::$typeMappingIsIncomes[$this->type] ? Transaction::TYPE_ADJUSTMENT_IN : Transaction::TYPE_ADJUSTMENT_OUT;
    }

    # 查询作用域 start
    public function scopeFoStatus($query, $value)
    {
        $status = [
            1 => [self::STATUS_SUCCESSFUL],
            2 => [self::STATUS_PENDING],
            3 => [self::STATUS_REJECT, self::STATUS_FAIL],
        ];

        return $query->whereIn('status', $status[$value]);
    }

    public function scopeDateFrom($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeDateTo($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }


    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('user', function ($query) use ($userName) {
            $query->where('name', $userName);
        });
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            $query->where('currency', $value);
        });
    }
    # 查询作用域 end
}
