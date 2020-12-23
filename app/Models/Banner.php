<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Database\Eloquent\SoftDeletes;

class Banner extends Model
{
    use RememberCache, FlushCache, SoftDeletes;

    protected $rememberCacheTag = 'banners';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'code',
        'currency',
        'languages',
        'show_type',
        'position',
        'target_type',
        'web_img_path',
        'mobile_img_path',
        'web_link_url',
        'mobile_link_url',
        'display_start_at',
        'display_end_at',
        'sort',
        'status',
        'admin_name',
        'is_agent',
    ];

    protected $casts = [
        'is_agent'  => 'boolean',
        'status'    => 'boolean',
        'languages' => 'array',
    ];

    protected $dates = [
        'display_start_at', 'display_end_at', 'deleted_at'
    ];

    # show_types
    const SHOW_TYPE_FISH = 1;
    const SHOW_TYPE_SLOT = 2;
    const SHOW_TYPE_LIVE = 3;
    const SHOW_TYPE_SPORT = 4;
    const SHOW_TYPE_HEAD = 10;

    public static $showTypes = [
        self::SHOW_TYPE_FISH  => 'fish',
        self::SHOW_TYPE_SLOT  => 'slot',
        self::SHOW_TYPE_LIVE  => 'live',
        self::SHOW_TYPE_SPORT => 'sport',
        self::SHOW_TYPE_HEAD  => 'head',
    ];

    # 跳转方式
    const TARGET_TYPE_NONE  = 1;
    const TARGET_TYPE_INNER = 2;
    const TARGET_TYPE_OUTER = 3;
    const TARGET_TYPE_DEEP  = 4; # 游戏深度链接

    public static $targetTypes = [
        self::TARGET_TYPE_NONE  => 'none',
        self::TARGET_TYPE_INNER => 'inner',
        self::TARGET_TYPE_OUTER => 'outer',
        self::TARGET_TYPE_DEEP  => 'deep',
    ];

    # positions
    const POSITION_BANNER = 1;
    const POSITION_TOP = 2;
    const POSITION_BOTTOM = 3;

    public static $positions = [
        self::POSITION_BANNER => 'banner',
        self::POSITION_TOP    => 'top',
        self::POSITION_BOTTOM => 'bottom',
    ];

    # is_agent
    const IS_AGENT = 1;
    const NO_AGENT = 0;

    public static $is_agent = [
        self::IS_AGENT => 'is agent',
        self::NO_AGENT => 'not agent',
    ];

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data){
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }

    public function scopeDisplayStartAt($query, $value)
    {
        return $query->where('display_start_at', '>=', $value);
    }

    public function scopeDisplayEndAt($query, $value)
    {
        return $query->where('display_end_at', '<=', $value);
    }


}
