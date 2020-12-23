<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Database\Eloquent\Model;

class BetToRewardRule extends Model
{
    use RememberCache, FlushCache;

    protected  $rememberCacheTag = 'bet_to_reward_rules';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'currency', 'rule',
    ];
    /**
     * 获取转换后的积分
     *
     * @param $currency
     * @param $amount
     * @return float
     */
    public static function getRewards($currency, $amount)
    {
        $rule = static::getAll()->where('currency', $currency)->first();

        return floor($amount/$rule->rule);
    }
}
