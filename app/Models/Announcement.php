<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class Announcement extends Model
{

    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'announcements';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'name',
        'content',
        'currencies',
        'is_agent',
        'show_type',
        'category',
        'payment_group_ids',
        'vip_ids',
        'start_at',
        'end_at',
        'sort',
        'status',
        'pop_up',
        'admin_name',
        'content_type',
        'pop_up_setting',
        'access_pop_mobile_urls',
        'access_pop_pc_urls',
        'is_login_pop_up',
        'is_game',
    ];

    protected $casts = [
        'status'            => 'boolean',
        'is_agent'          => 'boolean',
        'content'           => 'array',
        'currencies'        => 'array',
        'payment_group_ids' => 'array',
        'vip_ids'           => 'array',
        'pop_up_setting'    => 'array',
        'access_pop_mobile_urls' => 'array',
        'access_pop_pc_urls'     => 'array',
        'is_login_pop_up'   => 'boolean'
    ];

    protected $dates = [
        'start_at', 'end_at',
    ];

    # 通知类型
    const SHOW_TYPE_ALL = 1;
    const SHOW_TYPE_PAYMENT = 2;
    const SHOW_TYPE_VIP = 3;

    public static $showTypes = [
        self::SHOW_TYPE_ALL     => 'All',
        self::SHOW_TYPE_PAYMENT => 'Payment',
        self::SHOW_TYPE_VIP     => 'Vip',
    ];

    # 分类
    const CATEGORY_PAYMENT          = 1;
    const CATEGORY_MAINTENANCE      = 2;
    const CATEGORY_PROMOTION        = 3;
    const CATEGORY_GENERAL          = 4;
    const CATEGORY_BANKING_OPTION   = 5;
    const CATEGORY_NEWS             = 7;

    #公告内容类型
    const CONTENT_TYPE_FONT = 1; // 文字类型.
    const CONTENT_TYPE_IMAGE = 2; // 图片类型

    public static $categories = [
        self::CATEGORY_PAYMENT        => 'Payment',
        self::CATEGORY_MAINTENANCE    => 'Maintenance',
        self::CATEGORY_PROMOTION      => 'Promotion',
        self::CATEGORY_GENERAL        => 'General',
    ];

    public static $categoryForTranslation = [
        self::CATEGORY_PAYMENT        => 'announcement_payment',
        self::CATEGORY_MAINTENANCE    => 'announcement_maintenance',
        self::CATEGORY_PROMOTION      => 'announcement_promotion',
        self::CATEGORY_GENERAL        => 'announcement_general',
    ];

    public static $contentTypes = [
        self::CONTENT_TYPE_FONT => 'font', // 文字类型.
        self::CONTENT_TYPE_IMAGE => 'image', // 图片类型.
    ];

    public function admin()
    {
        return $this->belongsTo(Admin::class, 'name', 'admin_name');
    }

    public function checkCurrencySet($currency)
    {
        return in_array($currency, $this->currencies);
    }

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->content)->where('language', $language)->first();
        if ($data) {
            return $data;
        }
        return collect($this->content)->where('language', $default)->first();
    }

    public function scopeCurrency($query, $value)
    {
        return $query->where('currencies', 'like', '%' . $value . '%');
    }
}
