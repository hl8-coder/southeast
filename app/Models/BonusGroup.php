<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class BonusGroup extends Model
{
    use RememberCache, FlushCache;

    protected $guarded = [];

    protected $rememberCacheTag = 'bonus_group';

    public static $cacheExpireInMinutes = 43200;

    public static function getDropList()
    {
        return static::getAll()->pluck('name', 'id')->toArray();
    }

}
