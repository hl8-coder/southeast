<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class Vip extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'vips';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'level', 'name',  'display_name', 'rule', 'remark',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    /**
     * 获取下一等级vip
     *
     * @return  mixed
     */
    public function findNext()
    {
        $vips = static::getAll();

        return $vips->where('level', '>', $this->level)
            ->sortBy('level')
            ->first();
    }

    public static function getDropList()
    {
        return static::getAll()->pluck('name', 'id')->toArray();
    }
}
