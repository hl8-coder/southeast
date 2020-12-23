<?php

namespace App\Models;

use App\Models\Traits\RememberCache;

class AffiliateLink extends Model
{
    use RememberCache;

    protected $rememberCacheTag = 'affiliate_links';

    public static $cacheExpireInMinutes = 43200;

    public $fillable = [
        'type', 'platform', 'link', 'sort', 'status', 'admin_name', 'currencies', 'languages',
    ];

    protected $casts = [
        'status'     => 'boolean',
        'languages'  => 'array',
        'currencies' => 'array',
    ];

    # Status 状态
    const STATUS_ACTIVE   = 1;
    const STATUS_INACTIVE = 0;

    # Platform 设备类型
    const DEVICE_DESKTOP = 1;
    const DEVICE_MOBILE  = 2;

    # Type
    const TYPE_FISH      = 1;
    const TYPE_SLOT      = 2;
    const TYPE_LIVE      = 3;
    const TYPE_SPORT     = 4;
    const TYPE_LOTTERY   = 5;
    const TYPE_P2P       = 6;
    const TYPE_ESPORT    = 7;
    const TYPE_VIRTUAL   = 8;
    const TYPE_PROMOTION = 9;

    public static $status = [
        self::STATUS_ACTIVE   => 'Active',
        self::STATUS_INACTIVE => 'Inactive',
    ];

    public static $platform = [
        self::DEVICE_DESKTOP => 'Desktop',
        self::DEVICE_MOBILE  => 'Mobile',
    ];

    public static $platformForTranslation = [
        self::DEVICE_DESKTOP => 'desktop',
        self::DEVICE_MOBILE  => 'mobile',
    ];

    public static $type = [
        self::TYPE_FISH      => 'Games',
        self::TYPE_SLOT      => 'Slots',
        self::TYPE_LIVE      => 'Live Casino',
        self::TYPE_SPORT     => 'Sportsbook',
        self::TYPE_LOTTERY   => 'Lottery',
        self::TYPE_P2P       => 'P2P',
        self::TYPE_ESPORT    => 'E-Sports',
        self::TYPE_VIRTUAL   => 'Virtual Sports',
        self::TYPE_PROMOTION => 'Promotion',
    ];

    public static $typesForTranslation = [
        self::TYPE_FISH         => 'games',
        self::TYPE_SLOT         => 'slots',
        self::TYPE_LIVE         => 'live_casino',
        self::TYPE_SPORT        => 'sportsbook',
        self::TYPE_LOTTERY      => 'lottery',
        self::TYPE_P2P          => 'p2p',
        self::TYPE_ESPORT       => 'e_sports',
        self::TYPE_VIRTUAL      => 'virtual_sports',
        self::TYPE_PROMOTION    => 'promotion',

    ];

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data) {
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }
}
