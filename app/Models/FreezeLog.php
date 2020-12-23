<?php

namespace App\Models;

class FreezeLog extends Model
{
    protected $casts = [
        'amount'                => 'float',
        'before_freeze_balance' => 'float',
        'after_freeze_balance'  => 'float',
    ];

    const TYPE_WITHDRAW                   = 1;
    const TYPE_GAME_PLATFORM_TRANSFER     = 2;

    public static $types = [
        self::TYPE_WITHDRAW                 => 'withdrawal',
        self::TYPE_GAME_PLATFORM_TRANSFER   => 'game platform transfer',
    ];

    /**
     * 记录冻结日志
     *
     * @param User      $user
     * @param bool      $isFreeze       是否冻结 true:冻结 false:解冻
     * @param float     $amount         冻结金额
     * @param float     $beforeBalance  冻结前金额
     */
    public static function record(User $user, $isFreeze, $amount, $beforeBalance, $type, $traceId=null)
    {
        $freezeLog = new static();
        $freezeLog->user_id                 = $user->id;
        $freezeLog->user_name               = $user->name;
        $freezeLog->currency                = $user->currency;
        $freezeLog->is_freeze               = $isFreeze;
        $freezeLog->amount                  = $amount;
        $freezeLog->before_freeze_balance   = $beforeBalance;
        $freezeLog->after_freeze_balance    = $isFreeze ? $beforeBalance + $amount : $beforeBalance - $amount;
        $freezeLog->type                    = $type;
        $freezeLog->trace_id                = $traceId;
        $freezeLog->save();
    }
}
