<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Promotion extends Model
{
    use RememberCache, FlushCache, SoftDeletes;

    protected $rememberCacheTag = 'promotions';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'currencies',
        'languages',
        'show_types',
        'promotion_type_code',
        'code',
        'codes',
        'backstage_title',
        'display_start_at',
        'display_end_at',
        'web_img_path',
        'web_content_img_path',
        'mobile_img_path',
        'mobile_content_img_path',
        'admin_name',
        'status',
        'is_agent',
        'is_verified',
        'related_type',
        'sort',
        'is_can_claim',
    ];

    protected $dates = [
        'display_start_at', 'display_end_at', 'deleted_at',
    ];

    protected $casts = [
        'currencies'    => 'array',
        'languages'     => 'array',
        'codes'         => 'array',
        'show_types'    => 'array',
        'related_type'  => 'int',
        'is_can_claim'  => 'boolean',
        'is_agent'      => 'boolean',
    ];

    const RELATED_TYPE_BONUS = 1;

    public static $relatedTypes = [
        self::RELATED_TYPE_BONUS  => 'bonus',
    ];

    # 查询本地作用域 start
    public function scopeDisplayStartAt($query, $value)
    {
        return $query->where('display_start_at', '>=', $value);
    }

    public function scopeDisplayEndAt($query, $value)
    {
        return $query->where('display_end_at', '<=', $value);
    }

    public function scopeCurrency($query, $value)
    {
        return $query->where('currencies', 'like', '%' . $value . '%');
    }
    # 查询本地作用域 end

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

    public function isActive()
    {
        return true == $this->status;
    }

    public function isInActive()
    {
        return false == $this->status;
    }

    public function isNeedVerified()
    {
        return $this->is_verified;
    }

    public function isAgent()
    {
        return true == $this->is_agent;
    }

    public function isUser()
    {
        return false == $this->is_agent;
    }
}
