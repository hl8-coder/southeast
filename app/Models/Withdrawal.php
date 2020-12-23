<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;

class Withdrawal extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $casts = [
        'amount'            => 'float',
        'remain_amount'     => 'float',
        'fee'               => 'float',
        'remain_fee'        => 'float',
        'verify_details'    => 'array',
        'records'           => 'array',
    ];

    protected $dates = [
        'paid_at',
    ];

    protected $auditInclude = [
        'remark', 'status', 'hold_reason', 'reject_reason', 'escalate_reason', 'claim_admin_name',
    ];

    protected $auditEvents = [
        'updated',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            $model->update([
                'order_no' => static::findCreatedOrderNo(static::TXN_ID_WITHDRAWAL, $model->id)
            ]);
        });
    }

    # status
    const STATUS_PENDING            = 1;  # 初审已受理
    const STATUS_CANCELED           = 2;  # 用户取消
    const STATUS_HOLD               = 3;  # 保持
    const STATUS_REJECTED           = 4;  # 初次拒绝
    const STATUS_ESCALATED          = 5;  # 提升
    const STATUS_REVIEWED           = 6;  # 复审
    const STATUS_PROCESS            = 7;  # 处理中
    const STATUS_DEFERRED           = 8;  # 延迟
    const STATUS_APPROVED           = 9;  # 初次同意上分
    const STATUS_FAIL               = 10; # 失败
    const STATUS_SUCCESSFUL         = 11; # 成功

    public static $statuses = [
        self::STATUS_PENDING        => 'pending',
        self::STATUS_CANCELED       => 'cancel',
        self::STATUS_HOLD           => 'hold',
        self::STATUS_REJECTED       => 'rejected',
        self::STATUS_ESCALATED      => 'escalated',
        self::STATUS_REVIEWED       => 'reviewed',
        self::STATUS_PROCESS        => 'process',
        self::STATUS_DEFERRED       => 'deferred',
        self::STATUS_APPROVED       => 'approved',
        self::STATUS_FAIL           => 'fail',
        self::STATUS_SUCCESSFUL     => 'successful',
    ];

    # hold reason
    const HOLD_BY_CONTACT_CUSTOMER          = 1;
    const HOLD_BY_VERIFY_BANK_DETAILS       = 2;
    const HOLD_BY_BANK_UNSTABLE_OR_OFFLINE  = 3;
    const HOLD_BY_NO_DEPOSIT                = 4;
    const HOLD_BY_NEED_TO_BET_MORE_AMOUNT   = 5;
    const HOLD_BY_OTHERS                    = 6;

    public static $holdReasons = [
        self::HOLD_BY_CONTACT_CUSTOMER          => 'Contact customer',
        self::HOLD_BY_VERIFY_BANK_DETAILS       => 'Verify bank details',
        self::HOLD_BY_BANK_UNSTABLE_OR_OFFLINE  => 'Bank unstable / offline',
        self::HOLD_BY_NO_DEPOSIT                => 'No deposit',
        self::HOLD_BY_NEED_TO_BET_MORE_AMOUNT   => 'Need to bet more(amount)',
        self::HOLD_BY_OTHERS                    => 'Others',
    ];

    # reject reason
    const REJECT_BY_NEED_TO_BET_MORE_AMOUNT     = 1;
    const REJECT_BY_3RD_PARTY                   = 2;
    const REJECT_BY_INVALID_BANK_DETAILS        = 3;
    const REJECT_BY_REQUESTED_BY_MEMBER         = 4;
    const REJECT_BY_NO_DEPOSIT                  = 5;
    const REJECT_BY_SYSTEM_ERROR                = 6;
    const REJECT_BY_CONTACT_CUSTOMER_SERVICE    = 6;
    const REJECT_BY_OTHERS                      = 7;

    public static $rejectReasons = [
        self::REJECT_BY_NEED_TO_BET_MORE_AMOUNT  => 'Need to bet more(amount)',
        self::REJECT_BY_3RD_PARTY                => '3rd party',
        self::REJECT_BY_INVALID_BANK_DETAILS     => 'invalid bank details',
        self::REJECT_BY_REQUESTED_BY_MEMBER      => 'requested by member',
        self::REJECT_BY_NO_DEPOSIT               => 'no deposit',
        self::REJECT_BY_SYSTEM_ERROR             => 'system error',
        self::REJECT_BY_CONTACT_CUSTOMER_SERVICE => 'contact customer service',
        self::REJECT_BY_OTHERS                   => 'others'
    ];

    # escalate reason
    const ESCALATE_BY_1ST_TIME_CLAIM_BONUS  = 1;
    const ESCALATE_BY_P2P                   = 2;
    const ESCALATE_BY_HUGE_AMOUNT           = 3;
    const ESCALATE_BY_OTHERS                = 4;
    const ESCALATE_BY_LIFT_OFF              = 5;
    const ESCALATE_BY_BONUS_HUNTER          = 6;
    const ESCALATE_BY_KYC                   = 7;

    public static $escalateReasons = [
        self::ESCALATE_BY_1ST_TIME_CLAIM_BONUS  => '1st time claim bonus',
        self::ESCALATE_BY_P2P                   => 'Poker/Mahjong Player/Flshing(P2P)',
        self::ESCALATE_BY_HUGE_AMOUNT           => 'Huge amount',
        self::ESCALATE_BY_OTHERS                => 'Others',
        self::ESCALATE_BY_LIFT_OFF              => 'Lift off',
        self::ESCALATE_BY_BONUS_HUNTER          => 'Bonus hunter',
        self::ESCALATE_BY_KYC                   => 'Kyc',
    ];

    # 可以拒绝的状态
    public static $canRejectStatuses = [
        self::STATUS_PENDING,
        self::STATUS_HOLD,
        self::STATUS_ESCALATED,
        self::STATUS_REVIEWED,
        self::STATUS_PROCESS,
    ];

    # 可以hold的状态
    public static $canHoldStatuses = [
        self::STATUS_PENDING,
        self::STATUS_ESCALATED,
        self::STATUS_REVIEWED,
        self::STATUS_PROCESS,
    ];

    # 可以unclim的状态
    public static $canUnclaimStatuses = [
        self::STATUS_PENDING,
        self::STATUS_PROCESS,
    ];

    # 可以二次审核的状态
    public static $canSecondVerifyStatuses = [
        self::STATUS_REJECTED,
        self::STATUS_APPROVED,
    ];

    # 最终完成的2种状态
    public static $lastStatuses = [
        self::STATUS_CANCELED,
        self::STATUS_FAIL,
        self::STATUS_SUCCESSFUL,
    ];

    /**
     * 检查可以拒绝的状态
     *
     * @return bool
     */
    public function checkCanRejectStatus()
    {
        return (!empty($this->claim_admin_name) && in_array($this->status, static::$canRejectStatuses))
            || $this->status == self::STATUS_ESCALATED;
    }

    /**
     * 检查可以hold住的状态
     *
     * @return bool
     */
    public function checkCanHoldStatus()
    {
        return (!empty($this->claim_admin_name) && in_array($this->status, static::$canHoldStatuses))
            ||$this->status == self::STATUS_ESCALATED;
    }

    /**
     * 检查可以unclaim的状态
     *
     * @return bool
     */
    public function checkCanUnclaimStatus()
    {
        return !empty($this->claim_admin_name) && in_array($this->status, static::$canUnclaimStatuses);
    }

    /**
     * 检查可以二次审核的状态
     *
     * @return bool
     */
    public function checkSecondVerifyStatus()
    {
        return in_array($this->status, static::$canSecondVerifyStatuses);
    }

    /**
     * 检查是否是最终完成的状态
     *
     * @return bool
     */
    public function checkIsLastStatus()
    {
        return in_array($this->status, static::$lastStatuses);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }

    public function isPending()
    {
        return static::STATUS_PENDING == $this->status;
    }

    public function isHold()
    {
        return static::STATUS_HOLD == $this->status;
    }

    public function isReviewed()
    {
        return static::STATUS_REVIEWED == $this->status;
    }

    public function isProcess()
    {
        return static::STATUS_PROCESS == $this->status;
    }

    public function isEscalate()
    {
        return static::STATUS_ESCALATED == $this->status;
    }

    public function isDeferred()
    {
        return static::STATUS_DEFERRED == $this->status;
    }

    public function isApproved()
    {
        return static::STATUS_APPROVED == $this->status;
    }

    public function isSuccessful()
    {
        return static::STATUS_SUCCESSFUL == $this->status;
    }

    public function isCompletePayment()
    {
        return 0 == $this->remain_amount;
    }

    public function isRejected()
    {
        return static::STATUS_REJECTED == $this->status;
    }

    /**
     * hold提现单
     *
     * @param   integer   $holdReason   hold理由
     * @return  bool
     */
    public function hold($holdReason)
    {
        $holdStatus = $this->status;
        return $this->update([
            'status'        => static::STATUS_HOLD,
            'hold_status'   => $holdStatus,
            'hold_reason'   => $holdReason,
        ]);
    }

    /**
     * 解除提现hold状态
     *
     * @return bool
     */
    public function releaseHold()
    {
        return $this->update([
            'status' => $this->hold_status,
        ]);
    }

    public function cancel()
    {
        return $this->update([
            'status' => static::STATUS_CANCELED,
        ]);
    }

    /**
     * 设置为pending状态
     *
     * @return bool
     */
    public function pending()
    {
        return $this->update([
            'status' => static::STATUS_PENDING,
        ]);
    }

    /**
     * 拒绝提现单
     *
     * @param  integer   $rejectReason  拒绝理由
     * @return bool
     */
    public function reject($rejectReason)
    {
        return $this->update([
            'status'        => static::STATUS_REJECTED,
            'reject_reason' => $rejectReason,
        ]);
    }

    /**
     * 提升RM审核
     *
     * @param  integer  $escalateReason 提升理由
     * @return bool
     */
    public function escalate($escalateReason)
    {
        return $this->update([
            'status'            => static::STATUS_ESCALATED,
            'escalate_reason'   => $escalateReason,
        ]);
    }

    /**
     * 初审
     *
     * @return bool
     */
    public function review()
    {
        return $this->update([
            'status' => static::STATUS_REVIEWED,
        ]);
    }

    /**
     * 处理中
     *
     * @return bool
     */
    public function process()
    {
        return $this->update([
            'status' => static::STATUS_PROCESS,
        ]);
    }

    /**
     * 延迟
     *
     * @return bool
     */
    public function defer()
    {
        return $this->update([
            'status'   => static::STATUS_DEFERRED,
        ]);
    }

    /**
     * 解除延迟状态
     *
     * @return bool
     */
    public function releaseDefer()
    {
        return $this->update([
            'status' => static::STATUS_PROCESS,
        ]);
    }

    /**
     * 出款成功
     *
     * @return bool
     */
    public function approve()
    {
        return $this->update([
            'status'    => static::STATUS_APPROVED,
        ]);
    }

    /**
     * 提现最终成功
     *
     * @param  array  $verifyDetails
     * @return bool
     */
    public function success($verifyDetails)
    {
        return $this->update([
            'status'            => static::STATUS_SUCCESSFUL,
            'verify_details'    => $verifyDetails,
        ]);
    }

    /**
     * 提现最终失败
     *
     * @param   string  $rejectReason  拒绝理由
     * @return  bool
     */
    public function fail($rejectReason='')
    {
        if (!empty($rejectReason)) {
            return $this->update([
                'status'        => static::STATUS_FAIL,
                'reject_reason' => $rejectReason,
            ]);
        } else {
            return $this->update([
                'status'        => static::STATUS_FAIL,
            ]);
        }
    }

    /**
     * 提现失败
     *
     * @param $adminName
     * @return bool
     */
    public function updateLastAccess($adminName)
    {
        return $this->update([
            'last_access_at'    => now(),
            'last_access_name'  => $adminName,
        ]);
    }

    /**
     * 设定关闭菜单
     */
    public function closeForm()
    {
        $this->is_close_form = true;

        return $this;
    }

    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('user', function($query) use ($userName) {
            $query->where('name', $userName);
        });
    }
}
