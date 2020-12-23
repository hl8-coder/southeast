<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class Language extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'languages';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'name', 'code', 'status'
    ];

    public static $frontLanguageMap = [
        'vi-VN' => 'vn',
        'th'    => 'th',
        'en-US' => 'en',
        'zh-CN' => 'cn'
    ];

    public static function getDropList($isFront = false)
    {
        $language = $isFront ? static::getAll()->where('status', true) : static::getAll();
        return $language->pluck('name', 'code')->toArray();
    }

    public static function getTranslationDropList($isFront = false)
    {
        if ($isFront) {
            $data = static::getAll()->where('status', true)->pluck('name', 'code')->toArray();
        } else {
            $data = static::getAll()->pluck('name', 'code')->toArray();
        }
        array_walk($data, function (&$name, $key) {
            $name = __('language.' . strtolower($key));
        });
        return $data;
    }
}
