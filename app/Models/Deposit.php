<?php

namespace App\Models;

use App\Models\Traits\TurnoverRequirementTrait;
use App\Repositories\PgAccountTransactionRepository;


class Deposit extends Model
{
    use TurnoverRequirementTrait;

    public static $reportMappingType = Report::TYPE_CLOSE_DEPOSIT_BET;

    protected $guarded = [];

    protected $casts = [
        'is_advance_credit'       => 'boolean',
        'is_turnover_closed'      => 'boolean',
        'turnover_closed_value'   => 'float',
        'turnover_current_value'  => 'float',
    ];

    protected $dates = [
        'turnover_closed_at', 'approved_at', 'receipt_img_created_at',
    ];

    # 状态 Start
    const STATUS_CREATED          = 1;
    const STATUS_HOLD             = 2;
    const STATUS_RECHARGE_SUCCESS = 3;
    const STATUS_RECHARGE_FAIL    = 4;

    public static $statues = [
        self::STATUS_CREATED          => 'Pending',     # 未处理
        self::STATUS_HOLD             => 'Hold',        # 保留
        self::STATUS_RECHARGE_SUCCESS => 'Successful',  # 充值成功
        self::STATUS_RECHARGE_FAIL    => 'Failed',      # 充值失败
    ];

    # auto状态 Start
    const AUTO_STATUS_PROCESSING    = 1;
    const AUTO_STATUS_SUCCESS       = 2;
    const AUTO_STATUS_FAIL          = 3;

    public static $autoStatues = [
        self::AUTO_STATUS_PROCESSING    => 'Processing',     # 自动处理中
        self::AUTO_STATUS_SUCCESS       => 'Successful',     # 成功
        self::AUTO_STATUS_FAIL          => 'Fail',           # 失败
    ];

    # 标签 Start
    const TAG_OPEN           = 1;
    const TAG_CLOSED         = 2;
    const TAG_LOSE           = 3;

    public static $tags = [
        self::TAG_OPEN       => 'OPEN',
        self::TAG_CLOSED     => 'CLOSED',
        self::TAG_LOSE       => 'LOSE',
    ];

    # 标签类型 Start
    const TAG_CATEGORY_NO_FUND_RECEIVE  = 1;
    const TAG_CATEGORY_BANK_ISSUE       = 2;
    const TAG_CATEGORY_OTHERS           = 3;

    public static $tag_categories = [
        self::TAG_CATEGORY_NO_FUND_RECEIVE       => 'NO FUND RECEIVE',
        self::TAG_CATEGORY_BANK_ISSUE            => 'BANK ISSUE',
        self::TAG_CATEGORY_OTHERS                => 'OTHERS',
    ];

    # 模型关联 start
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function paymentPlatform()
    {
        return $this->belongsTo(PaymentPlatform::class);
    }

    public function userBank()
    {
        return $this->belongsTo(Bank::class, 'user_bank_id', 'id');
    }

    public function companyBankAccount()
    {
        return $this->belongsTo(CompanyBankAccount::class, 'company_bank_account_id', 'id');
    }

    public function bankTransaction()
    {
        return $this->belongsTo(BankTransaction::class, 'statement_id', 'id');
    }

    public function pgAccountTransaction()
    {
        return $this->hasOne(PgAccountTransaction::class, 'trace_id', 'order_no');
    }

    public function turnoverRequirement()
    {
        return $this->morphOne(TurnoverRequirement::class, 'requireable');
    }

    public function logs()
    {
        return $this->hasMany(DepositLog::class);
    }

    public function accessLogs()
    {
        return $this->logs()->orderBy("created_at","desc")->where("type", DepositLog::TYPE_ACCESS)->get()->take(3);
    }

    public function activeLogs()
    {
        return $this->logs()->orderBy("created_at","desc")->where("type", '<>',  DepositLog::TYPE_ACCESS)->get();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
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

    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('user', function($query) use ($userName) {
            $query->where('name', $userName);
        });
    }

    public function scopeAdminName($query, $value)
    {
        return $query->whereHas('logs', function($query) use ($value) {
            $query->where('admin_name', $value);
        });
    }

    public function scopeReference($query, $value)
    {
        return $query->where(function($query) use ($value) {
            return $query->where('id', $value)
                        ->orWhere('currency', 'like', '%' . $value . '%')
                        ->orWhere('order_no', 'like', '%' . $value . '%')
                        ->orWhere('amount', 'like', '%' . $value . '%')
                        ->orWhereHas('user', function($query) use ($value) {
                            return $query->where('name', 'like', '%' . $value . '%');
                        });
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where("status", $status);
    }

    public function scopeDevice($query, $device)
    {
        return $query->where("device", $device);
    }
    # 查询作用域 end

    # 方法 start
    public static function findAvailableOrderNo()
    {
        do {
            $orderNo = now()->format('YmdHis') . random_int(10000, 99999);
        } while (self::query()->where('order_no', $orderNo)->exists());

        return $orderNo;
    }

    public static function findByOrderNo($orderNo)
    {
        return static::query()->where('order_no', $orderNo)->first();
    }

    # 已关单
    public function isOpen()
    {
        return $this->tag == self::TAG_OPEN;
    }

    # 已关单
    public function isClosed()
    {
        return $this->tag == self::TAG_CLOSED;
    }

    # 已完成的訂單
    public function isCompleted()
    {
        return in_array($this->status, [self::STATUS_RECHARGE_SUCCESS, self::STATUS_RECHARGE_FAIL]);
    }

    # 确认是否要转成提前信貸
    public function IsAdvanceCreditHoldReason($hold_reason)
    {
        if(in_array($this->payment_type, [PaymentPlatform::PAYMENT_TYPE_BANKCARD, PaymentPlatform::PAYMENT_TYPE_MPAY, PaymentPlatform::PAYMENT_TYPE_LINEPAY])) {
            return in_array($hold_reason, [self::HOLD_REASON_BANK_OFFLINE, self::HOLD_REASON_BANK_UNSTABLE, self::HOLD_REASON_BANK_LOCK]);
        } else {
            return false;
        }
    }

    # 确认按钮显示
    public function checkApprove(&$error = "")
    {
        if($this->button_flow_code != "1") {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkHold(&$error = "")
    {
        if(!in_array($this->payment_type, [PaymentPlatform::PAYMENT_TYPE_BANKCARD, PaymentPlatform::PAYMENT_TYPE_MPAY, PaymentPlatform::PAYMENT_TYPE_LINEPAY])){
            $error = "System Error";
            return false;
        }

        if(!in_array($this->button_flow_code, ["1"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkReleaseHold(&$error = "")
    {
        if(!in_array($this->button_flow_code, ["1.2"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkReject(&$error = "")
    {
        if($this->isClosed())
        {
            $error = "This ticket has been closed.";
            return false;
        }

        if(!in_array($this->button_flow_code, ["1", "1.2.2"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkCancel(&$error = "")
    {
        if($this->isClosed())
        {
            $error = "This ticket has been closed.";
            return false;
        }

        if(!in_array($this->button_flow_code, ["1.1", "1.3"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }



    public function checkApproveChanges(&$error = "")
    {
        if($this->isClosed())
        {
            $error = "This ticket has been closed.";
            return false;
        }

        if(!in_array($this->button_flow_code, ["1.1", "1.3"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkRequestAdvance(&$error = "")
    {
        if(!in_array($this->button_flow_code, ["1.2"])
            || !$this->IsAdvanceCreditHoldReason($this->hold_reason)
        ) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkApproveAdv(&$error = "", $need_statement = false)
    {
        if(!in_array($this->button_flow_code, ["1.2.2"])
            || !$this->IsAdvanceCreditHoldReason($this->hold_reason)
        ) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkApprovePartial(&$error = "", $partial_amount = 0)
    {
        if(!in_array($this->button_flow_code, ["1.2.2"])
            || !$this->IsAdvanceCreditHoldReason($this->hold_reason)
        ) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkRevertAction(&$error = "")
    {
        if(!in_array($this->button_flow_code, ["1.2.2","1.2.2.1", "1.2.2.2"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkApproveAdvanceCredit(&$error = "")
    {
        if(!in_array($this->button_flow_code, ["1.2.2.1"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkApprovePartialAdvanceCredit(&$error = "")
    {
        if(!in_array($this->button_flow_code, ["1.2.2.2"])) {
            $error = "System Error";
            return false;
        }

        return true;
    }

    public function checkMatch(&$error = "")
    {
        if($this->statement_id) {
            $error = "This ticket has been matched";
            return false;
        }

        return true;
    }

    public function checkUnmatch(&$error = "")
    {
        if($this->status == static::STATUS_RECHARGE_SUCCESS && $this->statement_id) {
            return true;
        }
        else {
            return false;
        }

    }

    public function checkFinalApprove()
    {
        if(in_array($this->button_flow_code, ['1.2.2.1.2', '1.2.2.2.2']) && in_array($this->payment_type, [PaymentPlatform::PAYMENT_TYPE_MPAY, PaymentPlatform::PAYMENT_TYPE_LINEPAY])) {
            return true;
        } else {
            return false;
        }

    }

    public function isSuccess()
    {
        return static::STATUS_RECHARGE_SUCCESS == $this->status;
    }

    public function startAuto()
    {
        return $this->update([
            'auto_status' => static::AUTO_STATUS_PROCESSING,
        ]);
    }

    public function autoSuccess()
    {
        return $this->update([
            'auto_status' => static::AUTO_STATUS_SUCCESS,
        ]);
    }

    public function autoFail($remark)
    {
        return $this->update([
            'auto_status' => static::AUTO_STATUS_FAIL,
            'remarks'     => $remark,
        ]);
    }
    # 方法 end

    # 查询作用域 start
    public static function scopeSuccess($query)
    {
        return $query->where('status', static::STATUS_RECHARGE_SUCCESS);
    }
    # 查询作用域 end

    public function scopeMemberCode($query, $memberCode)
    {
        return $query->whereHas('user', function($query) use ($memberCode) {
             $query->where("name" , "like", "%" . $memberCode . "%");
        });
    }

    # 更新状态 start
    public function success($data=[])
    {
        $updateData = [
            'status'            => static::STATUS_RECHARGE_SUCCESS,
            'tag'               => static::TAG_CLOSED,
            'button_flow_code'  => 2,
            'callback_content'  => json_encode($data),
            'callback_at'       => now(),
            'approved_at'       => now(),
        ];

        # 计算第三方充值  公司需要承担的手续费.
        switch ($this->paymentPlatform->code) {
            case "Paytrust88-quickpay": # 平台1vnd=1000vnd paytrust回调金额1vnd=1vnd.
            case "Paytrust88-quickpay-thb":
                if (strtolower($this->user->currency) == "vnd") {
                    $updateData['reimbursement_fee'] = !empty($data['total_fees']) ? (float)$data['total_fees']/1000 : 0;
                } elseif (strtolower($this->user->currency) == "thb") {
                    $updateData['reimbursement_fee'] = !empty($data['total_fees']) ? (float)$data['total_fees'] : 0;
                }
                break;
            case "Help2-quickpay":
            case "Help2-quickpay-thb":
                # 获取存款手续费 手续费的比例是根据当月累计存款去判断的
                $rate = PgAccount::getHelp2PayRate($this->user->currency,'Help2-quickpay');
                $updateData['reimbursement_fee'] = $this->amount * $rate;
                break;
            default:
                break;
        }

        return $this->update($updateData);
    }

    public function fail($data=[], $remarks='')
    {
        # 如果状态为成功就不能再更新了
        if ($this->isSuccess()) {
            return false;
        }

        return $this->update([
            'status'            => static::STATUS_RECHARGE_FAIL,
            'tag'               => static::TAG_CLOSED,
            'button_flow_code'  => 3,
            'callback_content'  => json_encode($data),
            'callback_at'       => now(),
            'sys_remarks'       => $remarks,
        ]);
    }
    # 更新状态 end

    public static function boot()
    {
        parent::boot();

        static::created(function ($model) {
            $prefix = $model->user->isUser() ? static::TXN_ID_DEPOSIT : static::TXN_ID_AFF_DEPOSIT;
            $model->update([
                'order_no' => static::findCreatedOrderNo($prefix, $model->id),
            ]);
        });

        static::saved(function($model) {
            # 充值成功的订单 而且是第三方通道账单
            if ($model->status == self::STATUS_RECHARGE_SUCCESS && $model->tag == self::TAG_CLOSED && in_array($model->payment_type,[PaymentPlatform::PAYMENT_TYPE_QUICKPAY,PaymentPlatform::PAYMENT_TYPE_LINEPAY,PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD,PaymentPlatform::PAYMENT_TYPE_MPAY])) {
                # 如果不存在就写入第三方的通道帐变记录表.
                PgAccountTransactionRepository::findOrCreatePgAccountTranslationByUserDeposit($model);
            }

            # 充值成功更新会员第一笔充值时间
            if ($model->status == self::STATUS_RECHARGE_SUCCESS && $model->tag == self::TAG_CLOSED) {
                $user = $model->user;
                if ($user) {
                    $user->updateFirstDepositTime($model->deposit_at);
                }
            }
        });
    }


    # 支付管道 Start
    const BANK_PAY_WAY_UNKNOW               = 0;
    const BANK_PAY_WAY_INTERNET_BANKING     = 1;
    const BANK_PAY_WAY_CASH_BANKING         = 2;
    const BANK_PAY_WAY_ATM                  = 3;
    const BANK_PAY_WAY_MOBILE_BANKING       = 4;
    const BANK_PAY_WAY_PAYMENT_GATEWAY      = 5;
    const BANK_PAY_WAY_CASH_DEPOSIT_MACHINE = 6;

    public static $bankPayWays = [
        self::BANK_PAY_WAY_UNKNOW               => 'BANK_PAY_WAY_UNKNOW',            #未知
        self::BANK_PAY_WAY_INTERNET_BANKING     => 'BANK_PAY_WAY_INTERNET_BANKING',  #网路
        self::BANK_PAY_WAY_CASH_BANKING         => 'BANK_PAY_WAY_CASH_BANKING',      #现金
        self::BANK_PAY_WAY_ATM                  => 'BANK_PAY_WAY_ATM',               #ATM
        self::BANK_PAY_WAY_MOBILE_BANKING       => 'BANK_PAY_WAY_MOBILE_BANKING',    #手机银行
        self::BANK_PAY_WAY_PAYMENT_GATEWAY      => 'BANK_PAY_WAY_PAYMENT_GATEWAY',
        self::BANK_PAY_WAY_CASH_DEPOSIT_MACHINE => 'BANK_PAY_WAY_CASH_DEPOSIT_MACHINE',
    ];
    # 支付管道 End

    # 保持原因 Start
    const HOLD_REASON_NO_FUND_RECEIVE_YET                        = 1;
    const HOLD_REASON_BANK_RECEIPT_SUBMISSION                    = 2;
    const HOLD_REASON_CONTACT_CUSTOMER_SERVICE                   = 3;
    const HOLD_REASON_BANK_OFFLINE                               = 4;
    const HOLD_REASON_DEPOSIT_TO_OLD_ACCOUNT                     = 5;
    const HOLD_REASON_BANK_UNSTABLE                              = 6;
    const HOLD_REASON_INCORRECT_FUND_IN_ACCOUNT                  = 7;
    const HOLD_REASON_VERIFY_NAME                                = 8;
    const HOLD_REASON_VERIFY_FUND_IN_ACCOUNT                     = 9;
    const HOLD_REASON_VERIFY_BANK_REFERENCE                      = 10;
    const HOLD_REASON_VERIFY_3RD_PARTY_DEPOSIT                   = 11;
    const HOLD_REASON_BANK_LOCK                                  = 12;


    public static $holdReasons = [
        self::HOLD_REASON_NO_FUND_RECEIVE_YET                        => 'No Fund Receive Yet.',
        self::HOLD_REASON_BANK_RECEIPT_SUBMISSION                    => 'Bank Receipt Submission.',
        self::HOLD_REASON_CONTACT_CUSTOMER_SERVICE                   => 'Contact Customer Service.',
        self::HOLD_REASON_BANK_OFFLINE                               => 'Bank Offline.',
        self::HOLD_REASON_DEPOSIT_TO_OLD_ACCOUNT                     => 'Deposit To Old Account.',
        self::HOLD_REASON_BANK_UNSTABLE                              => 'Bank Unstable/Slow.',
        self::HOLD_REASON_INCORRECT_FUND_IN_ACCOUNT                  => 'Incorrect Fund In Account.',
        self::HOLD_REASON_VERIFY_NAME                                => 'Verify Name.',
        self::HOLD_REASON_VERIFY_FUND_IN_ACCOUNT                     => 'Verify Fund In Account.',
        self::HOLD_REASON_VERIFY_BANK_REFERENCE                      => 'Verify Bank Reference.',
        self::HOLD_REASON_VERIFY_3RD_PARTY_DEPOSIT                   => 'Verify 3rd Party deposit.',
        self::HOLD_REASON_BANK_LOCK                                  => 'Bank Lock.',
    ];
    # 保持原因 End


    # 拒絕原因 Start
    const REJECT_REASON_NO_FUND_RECEIVE          = 1;
    const REJECT_REASON_INVALID_BANK_RECEIPT     = 2;
    const REJECT_REASON_INCORRECT_DEPOSIT_AMOUNT = 3;
    const REJECT_REASON_3RD_PARTY_DEPOSIT        = 4;
    const REJECT_REASON_CONTACT_CUSTOMER_SERVICE = 5;
    const REJECT_REASON_INVALID_ACCOUNT_NUMBER   = 6;
    const REJECT_REASON_INVALID_BANK_REFERENCE   = 7;
    const REJECT_REASON_DUPLICATE_SUBMISSION     = 8;

    public static $rejectReasons = [
        self::REJECT_REASON_NO_FUND_RECEIVE          => 'No Fund Receive.',
        self::REJECT_REASON_INVALID_BANK_RECEIPT     => 'Invalid Bank Receipt.',
        self::REJECT_REASON_INCORRECT_DEPOSIT_AMOUNT => 'Incorect Deposit Amount.',
        self::REJECT_REASON_3RD_PARTY_DEPOSIT        => '3rd Party Deposit.',
        self::REJECT_REASON_CONTACT_CUSTOMER_SERVICE => 'Contact Customer Service.',
        self::REJECT_REASON_INVALID_ACCOUNT_NUMBER   => 'Invalid Account Number.',
        self::REJECT_REASON_INVALID_BANK_REFERENCE   => 'Invalid Bank Refference.',
        self::REJECT_REASON_DUPLICATE_SUBMISSION     => 'Duplicate Submission.',
    ];
    # 拒絕原因 End
}
