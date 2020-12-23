<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class ExchangeRate extends Model
{
    public $timestamps = false;

    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'exchange_rates';

    protected static $cacheExpireInMinutes = 10080;

    protected $fillable = [
        'user_currency', 'platform_currency', 'conversion_value', 'inverse_conversion_value',
    ];

    protected $casts = [
        'conversion_value'          => 'float',
        'inverse_conversion_value'  => 'float',
    ];

    public static function findRate($userCurrency, $platformCurrency)
    {
        return static::getAll()->where('user_currency', $userCurrency)
                ->where('platform_currency', $platformCurrency)
                ->first();
    }

    public static function findRateByUserAndPlatform(User $user, GamePlatform $platform)
    {
        $rate = null;
        if ($platform->exchange_currencies
            && isset($platform->exchange_currencies[$user->currency])
        ) {
            $rate = ExchangeRate::findRate($user->currency, $platform->exchange_currencies[$user->currency]);
        }
        return $rate;
    }
}
