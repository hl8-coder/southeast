<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class Reward extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'rewards';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'level', 'rule', 'remark',
    ];

    /**
     * 获取下一等级vip
     *
     * @return  mixed
     */
    public function findNext()
    {
        $rewards = static::getAll();

        return $rewards->where('level', '>', $this->level)->sortBy('level')->first();
    }

    public static function getDropList()
    {
        return static::getAll()->pluck('level', 'id')->toArray();
    }
}
