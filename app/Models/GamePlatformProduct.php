<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class GamePlatformProduct extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'game_platform_products';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'platform_code',
        'name',
        'code',
        'description',
        'content',
        'type',
        'currencies',
        'languages',
        'devices',
        'one_web_img_path',
        'two_web_img_path',
        'mobile_img_path',
        'is_close_bonus',
        'is_close_cash_back',
        'is_close_adjustment',
        'is_calculate_reward',
        'is_calculate_cash_back',
        'is_calculate_rebate',
        'is_can_try',
        'sort',
        'status',
    ];

    protected $casts = [
        'currencies'            => 'array',
        'languages'             => 'array',
        'devices'               => 'array',
        'is_can_try'            => 'boolean',
        'is_close_bonus'        => 'boolean',
        'is_close_cash_back'    => 'boolean',
        'is_close_adjustment'   => 'boolean',
        'is_calculate_reward'   => 'boolean',
        'is_calculate_cash_back'=> 'boolean',
        'is_calculate_rebate'   => 'boolean',
    ];

    const TYPE_FISH     = 1;
    const TYPE_SLOT     = 2;
    const TYPE_LIVE     = 3;
    const TYPE_SPORT    = 4;
    const TYPE_LOTTERY  = 5;
    const TYPE_P2P      = 6;
    const TYPE_ESPORT   = 7;
    const TYPE_VIRTUAL  = 8;

    public static $types = [
        self::TYPE_FISH     => 'Games',
        self::TYPE_SLOT     => 'Slots',
        self::TYPE_LIVE     => 'Live Casino',
        self::TYPE_SPORT    => 'Sportsbook',
        self::TYPE_LOTTERY  => 'Lottery',
        self::TYPE_P2P      => 'P2P',
        self::TYPE_ESPORT   => 'E-Sports',
        self::TYPE_VIRTUAL  => 'Virtual Sports',
    ];

    public static $typesForTranslation = [
        self::TYPE_FISH     => 'games',
        self::TYPE_SLOT     => 'slots',
        self::TYPE_LIVE     => 'live_casino',
        self::TYPE_SPORT    => 'sportsbook',
        self::TYPE_LOTTERY  => 'lottery',
        self::TYPE_P2P      => 'p2p',
        self::TYPE_ESPORT   => 'e_sports',
        self::TYPE_VIRTUAL  => 'virtual_sports',
    ];

    public static $affiliateType = [
        self::TYPE_FISH     => 'Games',
        self::TYPE_SLOT     => 'Slots',
        self::TYPE_LIVE     => 'Live Casino',
        self::TYPE_SPORT    => 'Sportsbook',
        self::TYPE_LOTTERY  => 'Lottery',
    ];

    public static $imgFields = [
        'one_web_img_id'        => 'one_web_img_path',
        'mobile_img_id'         => 'mobile_img_path',
    ];

    public static $imgShowFields = [
        'one_web_img_id'        => 'One Web Img Path',
        'mobile_img_id'         => 'Mobile Img Path',
    ];

    /**
     * request 过滤字段
     * @var array
     */
    public static $languageRequestFields = [
        'language',
        'name',
        'description',
        'content',
        'one_web_img_id',
        'mobile_img_id',
    ];

    public function platform()
    {
        return $this->belongsTo(GamePlatform::class, 'platform_code', 'code');
    }

    public static function findProductByType($platformCode, $type)
    {
        return GamePlatformProduct::getAll()->where('platform_code', $platformCode)
            ->where('type', $type)
            ->first();
    }

    public static function getDropList()
    {
        return static::getAll()->pluck('code', 'code')->toArray();
    }

    public static function getFrontDropList()
    {
        $products = static::getAll()->pluck('code', 'code')->toArray();

        foreach ($products as $key => $product) {
            $products[$key] = str_replace('Fish', 'Games' ,$product);
        }

        return $products;
    }

    public function isCloseBonus()
    {
        return $this->is_close_bonus;
    }

    public function isCloseCashBack()
    {
        return $this->is_close_cash_back;
    }

    public function isCalculateReward()
    {
        return $this->is_calculate_reward;
    }

    public function isCalculateCashBack()
    {
        return $this->is_calculate_cash_back;
    }

    public function isCalculateRebate()
    {
        return $this->is_calculate_rebate;
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
