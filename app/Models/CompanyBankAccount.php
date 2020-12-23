<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;
use OwenIt\Auditing\Contracts\Auditable;

class CompanyBankAccount extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $casts = [
        'first_balance'             => 'float',
        'balance'                   => 'float',
        'min_balance'               => 'float',
        'max_balance'               => 'float',
        'daily_fund_out'            => 'float',
        'daily_fund_out_limit'      => 'float',
        'daily_fund_in'             => 'float',
        'daily_fund_in_limit'       => 'float',
        'daily_transaction'         => 'integer',
        'daily_transaction_limit'   => 'integer',
    ];

    # 类别
    const TYPE_DEPOSIT          = 1;
    const TYPE_WITHDRAWAL       = 2;
    const TYPE_BACKUP           = 3;
    const TYPE_TRANSIT          = 4;
    const TYPE_PAYMENT_GATEWAY  = 5;

    public static $types = [
        self::TYPE_DEPOSIT          => 'Deposit',
        self::TYPE_WITHDRAWAL       => 'Withdrawal',
        self::TYPE_BACKUP           => 'Backup',
        self::TYPE_TRANSIT          => 'Transit',
        self::TYPE_PAYMENT_GATEWAY  => 'Payment Gateway',
    ];

    # 关联app
    const APP_RELATED_LINE = 1;
    const APP_RELATED_MOMO = 2;

    public static $appRelates = [
        self::APP_RELATED_LINE => 'Line',
        self::APP_RELATED_MOMO => 'Momo',
    ];

    # 状态
    const STATUS_ACTIVE         = 1;
    const STATUS_INACTIVE       = 2;
    const STATUS_INVESTIGATION  = 3;
    const STATUS_RISKY          = 4;
    const STATUS_FROZEN         = 5;

    public static $statuses = [
        self::STATUS_ACTIVE         => 'Active',
        self::STATUS_INACTIVE       => 'Inactive',
        self::STATUS_INVESTIGATION  => 'Investigation',
        self::STATUS_RISKY          => 'Risky',
        self::STATUS_FROZEN         => 'Frozen',
    ];

    # remark
    public static $remarks = [
        'New Remarks',
        'Bank Fee',
        'Interest/ Rebate',
        'Testing',
        'Unknown Deduction',
    ];

    # is_income
    public static $isIncomes = [
        1 => 'Credit',
        0 => 'Debit',
    ];

    # otps
    const OTP_SMS = 1;
    const OTP_APP = 2;

    public static $otps = [
        self::OTP_SMS => 'sms',
        self::OTP_APP => 'app',
    ];

    protected $auditInclude = [
        'payment_group_id', 'type', 'branch', 'province', 'status',
        'phone', 'phone_asset', 'password', 'safe_key_pass', 'otp',
        'app_related', 'user_name', 'password', 'account_name', 'account_no',
    ];

    protected $auditEvents = [
        'updated',
    ];

    public function generateTags() : array
    {
        $data = $this->getUpdatedEventAttributes();
        return array_keys($data[1]);
    }

    # 模型关联 start
    public function paymentPlatform()
    {
        return $this->belongsTo(PaymentPlatform::class, 'platform_id', 'id');
    }

    public function paymentGroup()
    {
        return $this->belongsTo(PaymentGroup::class);
    }

    public function bank()
    {
        return $this->belongsTo(Bank::class);
    }

    public function remarks()
    {
        return $this->hasMany(CompanyBankAccountRemark::class);
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
    # 模型关联 end

    # 判断银行卡是否可用
    public function isEnable()
    {
        return static::STATUS_ACTIVE == $this->status;
    }

    public function isInactive()
    {
        return static::STATUS_INACTIVE == $this->status;
    }

    public static function findByCode($code)
    {
        return static::query()->where('code', $code)->first();
    }

    public static function getAll()
    {
        return static::query()->get();
    }

    public static function getDropList()
    {
        return static::getAll()->pluck('code', 'id')->toArray();
    }

    public static function getCodeDropList()
    {
        return static::getAll()->pluck('code', 'code')->toArray();
    }

    public static function boot()
    {
        parent::boot();

        static::creating(function($model) {

            # 自动生成code
            $model->code = $model->bank_code . '-' . substr($model->account_no, -4, 4);

            if($model->type == self::TYPE_DEPOSIT) {
                $data = [
                    "name" => $model->code,
                    "display_name" => $model->code,
                    "code" => $model->code,
                    "payment_type" => PaymentPlatform::PAYMENT_TYPE_BANKCARD,
                    "currencies" => $model->bank->currency,
                ];

                $paymentPlatform =$model->paymentPlatform()->create($data);

                $model->platform_id = $paymentPlatform->id;
            }
        });

        static::deleting(function($model) {
            if($model->type == self::TYPE_DEPOSIT) {
                $model->paymentPlatform()->forceDelete();
            }
        });
    }

    /**
     * 更新公司银行卡余额、日入款、出款
     *
     * @param  float        $amount         变动金额
     * @param  float        $fee            手续费
     * @param  boolean      $isIncome       是否是进款 true:入款 false:出款
     * @throws \Exception
     * @return static
     */
    public function updateBalance($amount, $isIncome, $fee)
    {
        $totalAmount = $amount + $fee;
        $totalAmount = $isIncome ? abs($totalAmount) : -1 * abs($totalAmount);

        $builder = $this->setPrimaryKeyQuery()
            ->whereRaw(DB::raw("balance + $totalAmount >= 0"));

        # 入账手续费是给银行的而不是进入银行卡，所以不需要加上手续费
        if ($isIncome) {
            $affectRow = $builder->update([
                'balance'           => DB::raw("balance + $amount"),
                'fee'               => DB::raw("fee + $fee"),
                'daily_fund_in'     => DB::raw("daily_fund_in + $amount"),
                'daily_transaction' => DB::raw("daily_transaction + 1"),
            ]);
        } else {
            $totalAmount = abs($totalAmount);
            $affectRow = $builder->update([
                'balance'           => DB::raw("balance - $totalAmount"),
                'fee'               => DB::raw("fee + $fee"),
                'daily_fund_out'    => DB::raw("daily_fund_out + $amount"),
                'daily_transaction' => DB::raw("daily_transaction + 1"),
            ]);
        }

        if (1 != $affectRow) {
            throw new \Exception('company bank account balance not enough');
        }

        return $this->refresh();
    }

    /**
     * 余额是否超出限制
     *
     * @return bool
     */
    public function isBalanceExceedLimit()
    {
        return (!empty($this->max_balance) && $this->balance > $this->max_balance)
            || (!empty($this->min_balance) && $this->balance < $this->min_balance);
    }

    /**
     * 每日转入是否超出限制
     *
     * @return bool
     */
    public function isDailyFundInExceedLimit()
    {
        return !empty($this->daily_fund_in_limit) && $this->daily_fund_in > $this->daily_fund_in_limit;
    }

    /**
     * 每日转出是否超出限制
     *
     * @return bool
     */
    public function isDailyFundOutExceedLimit()
    {
        return !empty($this->daily_fund_out_limit) && $this->daily_fund_out > $this->daily_fund_out_limit;
    }

    /**
     * 每日交易次数是否超出限制
     *
     * @return bool
     */
    public function isDailyFundTransactionExceedLimit()
    {
        return !empty($this->daily_transaction_limit) && $this->daily_transaction > $this->daily_transaction_limit;
    }

    /**
     * 判断是否是提现类型公司银行卡
     *
     * @return bool
     */
    public function isWithdrawalType()
    {
        return $this->type == static::TYPE_WITHDRAWAL;
    }

    /**
     * 获取可提现公司银行卡
     *
     * @return mixed
     */
    public static function getWithdrawalTypeAccount($currency = '')
    {
        $query = static::query()->where('type', static::TYPE_WITHDRAWAL)
            ->where('status', static::STATUS_ACTIVE);

        if (!empty($currency)) {
            $query->where('currency', $currency);
        }

        return $query->get();
    }
}
