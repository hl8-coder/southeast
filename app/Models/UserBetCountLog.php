<?php

namespace App\Models;

use Carbon\Carbon;

class UserBetCountLog extends Model
{
    protected $casts = [
        'stake'                         => 'float',
        'open_bet'                      => 'float',
        'effective_bet'                 => 'float',
        'close_bonus_bet'               => 'float',
        'close_cash_back_bet'           => 'float',
        'close_adjustment_bet'          => 'float',
        'close_deposit_bet'             => 'float',
        'calculate_rebate_bet'          => 'float',
        'calculate_reward_bet'          => 'float',
        'profit'                        => 'float',
        'effective_profit'              => 'float',
        'calculate_cash_back_profit'    => 'float',
        'rebate'                        => 'float',
        'bonus'                         => 'float',
        'cash_back'                     => 'float',
        'proxy_bonus'                   => 'float',
        'bet_num'                       => 'integer',
        'bet_id'                        => 'integer',
        'date'                          => 'date',
    ];

    const STATUS_CREATED        = 1;
    const STATUS_PROCESSING     = 2;
    const STATUS_SUCCESS        = 3;
    const STATUS_FAIL           = 4;

    const PREFIX_ADJUSTMENT     = 'a';
    const PREFIX_BET            = 'b';
    const PREFIX_PRIZE          = 'p';
    const PREFIX_REBATE         = 'r';

    public static function report(
        $uniqueId,
        $userId,
        $productCode,
        Carbon $date,
        $data
    )
    {
        $log = new UserBetCountLog([
            'unique_id'     => $uniqueId,
            'user_id'       => $userId,
            'product_code'  => $productCode,
            'date'          => $date->toDateString(),
        ]);

        foreach ($data as $k => $v) {
            $log->$k = $v;
        }

        $log->save();

        return $log;
    }

    public static function processing($ids)
    {
        return static::query()->whereIn('id', $ids)->update([
            'status' => static::STATUS_PROCESSING,
        ]);
    }

    public static function success($ids)
    {
        return static::query()->whereIn('id', $ids)->update([
            'status' => static::STATUS_SUCCESS,
        ]);
    }

    public static function fail($ids)
    {
        return static::query()->whereIn('id', $ids)->update([
            'status' => static::STATUS_FAIL,
        ]);
    }
}
