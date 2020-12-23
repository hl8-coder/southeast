<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Support\Arr;

class Bank extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'bank';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'languages', 'currency', 'image', 'name', 'code', 'min_balance', 'daily_limit', 'annual_limit', 'status', 'admin_name', 'remark', 'icon', 'is_auto_deposit',
    ];

    protected $casts = [
        'min_balance'       => 'float',
        'daily_limit'       => 'float',
        'annual_limit'      => 'float',
        'languages'         => 'array',
        'is_auto_deposit'   => 'boolean',
    ];

    public static function getDropList()
    {
        return Bank::getAll()->pluck('code', 'id')->toArray();
    }

    public static function getFrontDropList($currency)
    {
        $bankLanguages = Bank::getAll()->where('status', true)->where('currency', $currency)->pluck('languages', 'id')->toArray();
        if (!empty($bankLanguages)) {
            array_walk($bankLanguages, function (&$bankLanguage){
                $language = collect($bankLanguage)->where('language', app()->getLocale())->first();
                if (empty($language)){
                    $language = collect($bankLanguage)->where('language', 'en-US')->first();
                }
                if (!empty($language)) {
                    $bankLanguage = array_pull($language, 'front_name');
                } else {
                    $bankLanguage = '';
                }
            });
        }
        return $bankLanguages;
    }

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data){
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }

    public function images()
    {
        return $this->morphMany(Image::class, 'imageable');
    }
}
