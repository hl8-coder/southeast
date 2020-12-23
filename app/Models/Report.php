<?php

namespace App\Models;

use App\Models\Traits\CreatesWithLock;

class Report extends Model
{
    use CreatesWithLock;

    protected $guarded = [];

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
        'deposit'                       => 'float',
        'withdrawal'                    => 'float',
        'transfer_in'                   => 'float',
        'transfer_out'                  => 'float',
        'adjustment_in'                 => 'float',
        'adjustment_out'                => 'float',
        'affiliate_transfer_in'         => 'float',
        'affiliate_transfer_out'        => 'float',
        'bet_num'                       => 'integer',
        'promotion'                     => 'float',
    ];

    # 类型
    const TYPE_STAKE                        = 1;
    const TYPE_EFFECTIVE_BET                = 2;
    const TYPE_OPEN_BET                     = 3;
    const TYPE_CLOSE_BONUS_BET              = 4;
    const TYPE_CLOSE_CASH_BACK_BET          = 5;
    const TYPE_CLOSE_ADJUSTMENT_BET         = 6;
    const TYPE_CLOSE_DEPOSIT_BET            = 7;
    const TYPE_CALCULATE_REBATE_BET         = 8;
    const TYPE_CALCULATE_REWARD_BET         = 9;
    const TYPE_PROFIT                       = 10;
    const TYPE_EFFECTIVE_PROFIT             = 11;
    const TYPE_CALCULATE_CASH_BACK_PROFIT   = 13;
    const TYPE_DEPOSIT                      = 14;
    const TYPE_WITHDRAWAL                   = 15;
    const TYPE_TRANSFER_IN                  = 16;
    const TYPE_TRANSFER_OUT                 = 17;
    const TYPE_ADJUSTMENT_IN                = 18;
    const TYPE_REBATE                       = 19;
    const TYPE_BONUS                        = 20;
    const TYPE_CASH_BACK                    = 21;
    const TYPE_PROXY_BONUS                  = 22;
    const TYPE_AFFILIATE_TRANSFER_IN        = 23;
    const TYPE_AFFILIATE_TRANSFER_OUT       = 24;
    const TYPE_BET_NUM                      = 25;
    const TYPE_ADJUSTMENT_OUT               = 26;
    const TYPE_PROMOTION                    = 27;


    public static $types = [
        self::TYPE_STAKE                        => '总流水',
        self::TYPE_OPEN_BET                     => '未开奖投注',
        self::TYPE_EFFECTIVE_BET                => '有效流水',
        self::TYPE_CLOSE_BONUS_BET              => '关闭红利流水',
        self::TYPE_CLOSE_CASH_BACK_BET          => '关闭赎返流水',
        self::TYPE_CLOSE_ADJUSTMENT_BET         => '关闭调整流水',
        self::TYPE_CLOSE_DEPOSIT_BET            => '关闭充值流水',
        self::TYPE_CALCULATE_REBATE_BET         => '计算返点流水',
        self::TYPE_CALCULATE_REWARD_BET         => '计算积分流水',
        self::TYPE_PROFIT                       => '总盈亏',
        self::TYPE_EFFECTIVE_PROFIT             => '有效盈亏',
        self::TYPE_CALCULATE_CASH_BACK_PROFIT   => '计算赎返盈亏',
        self::TYPE_DEPOSIT                      => '充值',
        self::TYPE_WITHDRAWAL                   => '提现',
        self::TYPE_TRANSFER_IN                  => '转入',
        self::TYPE_TRANSFER_OUT                 => '转出',
        self::TYPE_ADJUSTMENT_IN                => '加钱调整',
        self::TYPE_ADJUSTMENT_OUT               => '扣钱调整',
        self::TYPE_REBATE                       => '返利',
        self::TYPE_BONUS                        => '红利',
        self::TYPE_CASH_BACK                    => '赎返',
        self::TYPE_PROXY_BONUS                  => '代理分红',
        self::TYPE_AFFILIATE_TRANSFER_IN        => '代理转入',
        self::TYPE_AFFILIATE_TRANSFER_OUT       => '代理转出',
        self::TYPE_BET_NUM                      => '注单数',
        self::TYPE_PROMOTION                    => '优惠',
    ];

    public static $productMappingTypes = [
        self::TYPE_BET_NUM                      => 'bet_num',
        self::TYPE_STAKE                        => 'stake',
        self::TYPE_OPEN_BET                     => 'open_bet',
        self::TYPE_EFFECTIVE_BET                => 'effective_bet',
        self::TYPE_CLOSE_BONUS_BET              => 'close_bonus_bet',
        self::TYPE_CLOSE_CASH_BACK_BET          => 'close_cash_back_bet',
        self::TYPE_CLOSE_ADJUSTMENT_BET         => 'close_adjustment_bet',
        self::TYPE_CLOSE_DEPOSIT_BET            => 'close_deposit_bet',
        self::TYPE_CALCULATE_REBATE_BET         => 'calculate_rebate_bet',
        self::TYPE_CALCULATE_REWARD_BET         => 'calculate_reward_bet',
        self::TYPE_PROFIT                       => 'profit',
        self::TYPE_EFFECTIVE_PROFIT             => 'effective_profit',
        self::TYPE_CALCULATE_CASH_BACK_PROFIT   => 'calculate_cash_back_profit',
        self::TYPE_REBATE                       => 'rebate',
        self::TYPE_BONUS                        => 'bonus',
        self::TYPE_CASH_BACK                    => 'cash_back',
        self::TYPE_PROXY_BONUS                  => 'proxy_bonus',
    ];

    public static $PlatformMappingTypes = [
        self::TYPE_DEPOSIT                    => 'deposit',
        self::TYPE_WITHDRAWAL                 => 'withdrawal',
        self::TYPE_PROMOTION                  => 'promotion',
        self::TYPE_TRANSFER_IN                => 'transfer_in',
        self::TYPE_TRANSFER_OUT               => 'transfer_out',
        self::TYPE_ADJUSTMENT_IN              => 'adjustment_in',
        self::TYPE_ADJUSTMENT_OUT             => 'adjustment_out',
        self::TYPE_AFFILIATE_TRANSFER_IN      => 'affiliate_transfer_in',
        self::TYPE_AFFILIATE_TRANSFER_OUT     => 'affiliate_transfer_out',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public static function platformRecord(User $user, $platformCode, $type, $value, $date='')
    {
        if (!empty($date)) {
            $report = static::firstOrCreate([
                'user_id'               => $user->id,
                'user_name'             => $user->name,
                'platform_code'         => $platformCode,
                'date'                  => $date,
            ]);
        } else {
            $report = static::firstOrCreate([
                'user_id'               => $user->id,
                'user_name'             => $user->name,
                'platform_code'         => $platformCode,
            ]);
        }

        if (isset(static::$PlatformMappingTypes[$type])) {
            $report->increment(static::$PlatformMappingTypes[$type], $value);
        }
    }

    /**
     * 投注报表统计
     *
     * 一次统计多条数据，提高效率，减少事务争抢
     *
     * @param User $user
     * @param $productCode
     * @param $data
     * @param string $date
     */
    public static function productRecord(User $user, $productCode, $data, $date='')
    {
        if (!empty($date)) {
            $report = static::firstOrCreate([
                'user_id'       => $user->id,
                'user_name'     => $user->name,
                'product_code'  => $productCode,
                'date'          => $date,
            ]);
        } else {
            $report = static::firstOrCreate([
                'user_id'       => $user->id,
                'user_name'     => $user->name,
                'product_code'  => $productCode,
            ]);
        }

        # 记录平台code
        if ($product = GamePlatformProduct::findByCodeFromCache($productCode))
        {
            $report->platform_code = $product->platform_code;
        } else {
            $report->platform_code = $productCode;
        }

        foreach ($data as $k=>$v) {
            $report->$k += $v;
        }

        $report->save();
    }
}
