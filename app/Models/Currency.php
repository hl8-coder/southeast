<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class Currency extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'currencies';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'name', 'code', 'preset_language', 'sort', 'country', 'country_code', 'is_remove_three_zeros',
        'deposit_second_approve_amount', 'withdrawal_second_approve_amount', 'bank_account_verify_amount',
        'info_verify_prize_amount', 'max_deposit', 'min_deposit', 'max_withdrawal', 'min_withdrawal',
        'max_daily_withdrawal', 'min_transfer', 'max_transfer', 'status', 'commission', 'payout_comm_mini_limit',
        'deposit_pending_limit', 'withdrawal_pending_limit'
    ];

    protected $casts = [
        'is_remove_three_zeros'             => 'boolean',
        'deposit_second_approve_amount'     => 'float',
        'withdrawal_second_approve_amount'  => 'float',
        'bank_account_verify_amount'        => 'float',
        'info_verify_prize_amount'          => 'float',
        'max_deposit'                       => 'float',
        'min_deposit'                       => 'float',
        'max_withdrawal'                    => 'float',
        'min_withdrawal'                    => 'float',
    ];

    public static function getDropList($column1 = 'name', $column2 = 'code')
    {
        return static::getAll()->pluck($column1, $column2)->toArray();
    }

    /**
     * 是否去除3个0
     *
     * @return mixed
     */
    public function isCanRemoveThreeZeros()
    {
        return $this->is_remove_three_zeros;
    }

    # 方法 start
    public static function getValue($column)
    {
        return static::query()->pluck($column, 'code')->toArray();
    }

    public static function isVND($currency)
    {
        return $currency == 'VND';
    }
    # 方法 end

}

