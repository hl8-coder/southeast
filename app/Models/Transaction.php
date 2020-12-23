<?php

namespace App\Models;

class Transaction extends Model
{
    protected $guarded = [];

    protected $dates = [
        'start_process_at', 'end_process_at',
    ];

    protected $casts = [
        'is_income'         => 'boolean',
        'amount'            => 'float',
        'before_balance'    => 'float',
        'after_balance'     => 'float',
    ];

    public static $isIncomes = [
        '1' => '+',
        '0' => '-',
    ];

    # 状态
    const STATUS_CREATED       = 1; //已建立
    const STATUS_PROCESSING    = 2; //账变处理中
    const STATUS_SUCCESS       = 3; //账变成功
    const STATUS_FAIL          = 4; //账变失败

    public static $statuses = [
        self::STATUS_CREATED    => 'Created',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_SUCCESS    => 'Successful',
        self::STATUS_FAIL       => 'Fail',
    ];

    # 帐变类型大分类 Start
    const TYPE_GROUP_DEPOSIT          = 1; # 充值
    const TYPE_GROUP_WITHDRAWAL       = 2; # 提现
    const TYPE_GROUP_ADJUSTMENT       = 3; # 调整
    const TYPE_GROUP_FUND_TRANSFER    = 4; # 转账
    const TYPE_GROUP_REBATE           = 5; # 返点
    const TYPE_GROUP_BONUS            = 6; # 红利
    const TYPE_GROUP_AFFILIATE_TOPUP  = 7; # 代理转账

    public static $typeGroups = [
        self::TYPE_GROUP_DEPOSIT            => 'Deposit',
        self::TYPE_GROUP_WITHDRAWAL         => 'Withdrawal',
        self::TYPE_GROUP_ADJUSTMENT         => 'Adjustment',
        self::TYPE_GROUP_FUND_TRANSFER      => 'Fund Transfer',
        self::TYPE_GROUP_REBATE             => 'Rebate',
        self::TYPE_GROUP_BONUS              => 'Bonus',
        self::TYPE_GROUP_AFFILIATE_TOPUP    => 'Affiliate Topup',
    ];
    # 帐变类型大分类 End

    # 帐变类型 Start
    const TYPE_ONLINE_BANKING_SAVE          = 10; # 银行存款
    const TYPE_THIRD_PARTY_SAVE             = 11; # 第三方存款
    const TYPE_THIRD_PARTY_FAST_SAVE        = 12; # 快捷款

    const TYPE_GAME_TRANSFER_IN             = 15; # 第三方转钱到平台
    const TYPE_GAME_TRANSFER_OUT            = 16; # 平台转钱到第三方

    const TYPE_WITHDRAW                     = 20; #提现

    const TYPE_REBATE_PRIZE                 = 21; # 返点奖励

    const TYPE_ADJUSTMENT_IN                = 22; # 加款调整
    const TYPE_ADJUSTMENT_OUT               = 23; # 出款调整

    const TYPE_AFFILIATE_TRANSFER_IN        = 25; # 代理转入
    const TYPE_AFFILIATE_TRANSFER_OUT       = 26; # 代理转出
    # 帐变类型 End

    # 统计会员报表类型
    public static $mappingReportType = [
        self::TYPE_ONLINE_BANKING_SAVE          => Report::TYPE_DEPOSIT,
        self::TYPE_THIRD_PARTY_SAVE             => Report::TYPE_DEPOSIT,
        self::TYPE_THIRD_PARTY_FAST_SAVE        => Report::TYPE_DEPOSIT,
        self::TYPE_GAME_TRANSFER_IN             => Report::TYPE_TRANSFER_IN,
        self::TYPE_GAME_TRANSFER_OUT            => Report::TYPE_TRANSFER_OUT,
        self::TYPE_WITHDRAW                     => Report::TYPE_WITHDRAWAL,
        self::TYPE_REBATE_PRIZE                 => Report::TYPE_REBATE,
        self::TYPE_AFFILIATE_TRANSFER_IN        => Report::TYPE_AFFILIATE_TRANSFER_IN,
        self::TYPE_AFFILIATE_TRANSFER_OUT       => Report::TYPE_AFFILIATE_TRANSFER_OUT,
    ];

    public static $mappingTypeGroup = [
        self::TYPE_ONLINE_BANKING_SAVE          => self::TYPE_GROUP_DEPOSIT,
        self::TYPE_THIRD_PARTY_SAVE             => self::TYPE_GROUP_DEPOSIT,
        self::TYPE_THIRD_PARTY_FAST_SAVE        => self::TYPE_GROUP_DEPOSIT,
        self::TYPE_GAME_TRANSFER_IN             => self::TYPE_GROUP_FUND_TRANSFER,
        self::TYPE_GAME_TRANSFER_OUT            => self::TYPE_GROUP_FUND_TRANSFER,
        self::TYPE_WITHDRAW                     => self::TYPE_GROUP_WITHDRAWAL,
        self::TYPE_REBATE_PRIZE                 => self::TYPE_GROUP_REBATE,
        self::TYPE_ADJUSTMENT_IN                => self::TYPE_GROUP_ADJUSTMENT,
        self::TYPE_ADJUSTMENT_OUT               => self::TYPE_GROUP_ADJUSTMENT,
        self::TYPE_AFFILIATE_TRANSFER_IN        => self::TYPE_GROUP_AFFILIATE_TOPUP,
        self::TYPE_AFFILIATE_TRANSFER_OUT       => self::TYPE_GROUP_AFFILIATE_TOPUP,
    ];

    # 帐变类型出入款列表 true：存款 false：出款
    public static $typeIncomes = [
        self::TYPE_ONLINE_BANKING_SAVE          => true,
        self::TYPE_THIRD_PARTY_SAVE             => true,
        self::TYPE_THIRD_PARTY_FAST_SAVE        => true,
        self::TYPE_GAME_TRANSFER_IN             => true,
        self::TYPE_GAME_TRANSFER_OUT            => false,
        self::TYPE_WITHDRAW                     => false,
        self::TYPE_REBATE_PRIZE                 => true,
        self::TYPE_ADJUSTMENT_IN                => true,
        self::TYPE_ADJUSTMENT_OUT               => false,
        self::TYPE_AFFILIATE_TRANSFER_IN        => true,
        self::TYPE_AFFILIATE_TRANSFER_OUT       => false,
    ];

    # 模型关联 start
    public function user()
    {
        return $this->belongsTo(User::class);
    }
    # 模型关联 end

    # 查询作用域 start
    public function scopeUserName($query, $userName)
    {
        return $query->whereHas('user', function($query) use ($userName) {
            $query->where('name', $userName);
        });
    }

    public function scopeIsUser($query)
    {
        return $query->whereHas('user', function ($query) {
            $query->where('is_agent', false);
        });
    }
    # 查询作用域 end

    public function isCreated()
    {
        return static::STATUS_CREATED == $this->status;
    }

    public function isIncome()
    {
        return $this->is_income;
    }

    public function start()
    {
        $this->update([
            'status' => static::STATUS_PROCESSING,
            'start_process_at' => now(),
        ]);
    }

    public function success($beforeBalance)
    {
        $this->status = static::STATUS_SUCCESS;
        $this->end_process_at = now();

        if ($this->isIncome()) {
            $this->before_balance = $beforeBalance;
            $this->after_balance  = $beforeBalance + $this->amount;
        }

        $this->save();
    }

    public function fail($msg)
    {
        $this->update([
            'status'            => static::STATUS_FAIL,
            'end_process_at'    => now(),
            'sys_remark'        => $msg,
        ]);
    }
}
