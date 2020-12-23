<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class AffiliateAnnouncement extends Model
{

    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'affiliate_announcement';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'name', 'content', 'currencies', 'display_type', 'category', 'admin_name',
        'start_at', 'end_at', 'sort', 'status', 'pop_up',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'content'    => 'array',
        'currencies' => 'array',
    ];

    protected $dates = [
        'start_at', 'end_at',
    ];


    # 分类
    const CATEGORY_BANKING_OPTION = 1;
    const CATEGORY_PROMOTION      = 2;
    const CATEGORY_NEWS           = 3;

    public static $categories = [
        self::CATEGORY_BANKING_OPTION => 'Banking Option',
        self::CATEGORY_PROMOTION      => 'Promotion',
        self::CATEGORY_NEWS           => 'News',
    ];

    # 模型关联 start
    public function admin()
    {
        return $this->belongsTo(Admin::class, 'name', 'admin_name');
    }

    # 模型关联 end

    # 修改器 end
    public function checkCurrencySet($currency)
    {
        return in_array($currency, $this->currencies);
    }

    public function getLanguageSet()
    {
        return collect($this->content)->where('language', app()->getLocale())->first();
    }
}
