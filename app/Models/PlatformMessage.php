<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class PlatformMessage extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'platform_messages';

    public static $cacheExpireInMinutes = 43200;

    protected $guarded = [];

    const TYPE_LEAGUE   = 1; # 联盟
    const TYPE_TEAM     = 2; # 队伍

    public static function getValue($platformCode, $type, $key)
    {
        $message =  static::getAll()->where('platform_code', $platformCode)->where('type', $type)->where('key', $key)->first();

        return $message ? $message->value : '';
    }

    public static function isExists($platformCode, $type, $key)
    {
        return !static::getAll()->where('platform_code', $platformCode)->where('type', $type)->where('key', $key)->isEmpty();
    }

    public static function setValue($platformCode, $type, $key, $value)
    {
        return static::query()->create([
            'platform_code' => $platformCode,
            'type'      => $type,
            'key'       => $key,
            'value'     => $value,
        ]);
    }

}
