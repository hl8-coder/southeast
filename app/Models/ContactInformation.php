<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class ContactInformation extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'contact_us';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'currencies',
        'languages',
        'icon',
        'is_affiliate',
        'is_enable',
        'api_url',
    ];

    protected $casts = [
        'currencies'   => 'array',
        'languages'    => 'array',
        'is_affiliate' => 'boolean',
        'is_enable'    => 'boolean',
    ];

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

    public function scopeCurrency($query, $value)
    {
        return $query->where('currencies', 'like', '%' . $value . '%');
    }
}
