<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class PromotionType extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'promotion_types';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'code',
        'currencies',
        'languages',
        'description',
        'web_img_path',
        'mobile_img_path',
        'status',
        'sort',
        'admin_name',
    ];

    protected $casts = [
        'currencies'    => 'array',
        'languages'     => 'array',
        'status'        => 'boolean',
    ];

    public static function getDropList()
    {
        return static::getAll()->pluck('title', 'code')->toArray();
    }

    public function checkCurrencySet($currency)
    {
        return in_array($currency, $this->currencies);
    }

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data){
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }
}
