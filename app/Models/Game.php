<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;
use Illuminate\Database\Eloquent\SoftDeletes;
use OwenIt\Auditing\Contracts\Auditable;

class Game extends Model implements Auditable
{
    use SoftDeletes, RememberCache, FlushCache;
    use \OwenIt\Auditing\Auditable;

    const STATUS_ACTIVE = 1;
    const STATUS_INACTIVE = 0;

    protected $rememberCacheTag = 'games';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'platform_code',
        'product_code',
        'currencies',
        'languages',
        'code',
        'type',
        'devices',
        'web_img_path',
        'mobile_img_path',
        'small_img_path',
        'droplist_img_path',
        'is_effective_bet',
        'is_hot',
        'is_soon',
        'is_new',
        'is_iframe',
        'is_mobile_iframe',
        'is_using_cookie',
        'is_close_bonus',
        'is_close_cash_back',
        'is_close_adjustment',
        'is_calculate_reward',
        'is_calculate_cash_back',
        'is_calculate_rebate',
        'remark',
        'sort',
        'status',
        'last_save_admin',
        'last_save_at',
    ];

    protected $casts = [
        'currencies'             => 'array',
        'languages'              => 'array',
        'devices'                => 'array',
        'is_hot'                 => 'boolean',
        'is_new'                 => 'boolean',
        'is_soon'                => 'boolean',
        'is_iframe'              => 'boolean',
        'is_mobile_iframe'       => 'boolean',
        'is_using_cookie'        => 'boolean',
        'is_effective_bet'       => 'boolean',
        'is_close_bonus'         => 'boolean',
        'is_close_cash_back'     => 'boolean',
        'is_close_adjustment'    => 'boolean',
        'is_calculate_reward'    => 'boolean',
        'is_calculate_cash_back' => 'boolean',
        'is_calculate_rebate'    => 'boolean',
        'status'                 => 'boolean',
    ];

    protected $auditableEvents = ['updated',];

    protected $auditInclude = [
        'platform_code',
        'product_code',
        'type',
        'code',
        'currencies',
        'languages',
        'devices',
        //'web_img_path',
        //'mobile_img_path',
        'is_hot',
        'is_new',
        'is_iframe',
        'is_using_cookie',
        'is_effective_bet',
        'is_close_bonus',
        'is_close_cash_back',
        'is_close_adjustment',
        'is_calculate_reward',
        'is_calculate_cash_back',
        'is_calculate_rebate',
        'remark',
        'sort',
        'status',
        //'last_save_admin',
        //'last_save_at',
        'deleted_at',
        //'small_img_path',
        'is_soon',
        'is_mobile_iframe',
        //'web_menu_img_path',
        'droplist_img_path'
    ];

    public function getAuditFields()
    {
        return $this->auditInclude;
    }

    public function getAuditEvents(): array
    {
        return $this->auditableEvents;
    }

    protected $date = [
        'deleted_at'
    ];

    public static $imgFields = [
        'web_img_id'            => 'web_img_path',
        'small_img_path_id'     => 'small_img_path',
        'droplist_img_path_id'  => 'droplist_img_path',
        'mobile_img_id'         => 'mobile_img_path',
    ];

    public static $imgShowFields = [
        'web_img_id'            => 'Web Img Path',
        'small_img_path_id'     => 'Small Img Path',
        'droplist_img_path_id'  => 'Droplist Img Path',
        'mobile_img_id'         => 'Mobile Img Path',
    ];

    public static $statusList = [
        self::STATUS_ACTIVE   => 'active',
        self::STATUS_INACTIVE => 'inactive',
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
        'web_img_id',
        'small_img_path_id',
        'droplist_img_path_id',
        'mobile_img_id',
    ];

    # 模型关联 start
    public function platform()
    {
        return $this->belongsTo(GamePlatform::class, 'platform_code', 'code');
    }

    public function product()
    {
        return $this->belongsTo(GamePlatformProduct::class, 'product_code', 'code');
    }
    # 模型关联 end

    # 方法 start
    /**
     * 是否计算有效投注
     *
     * @return mixed
     */
    public function isEffectiveBet()
    {
        return $this->is_effective_bet;
    }

    /**
     * 是否可用于关闭红利
     *
     * @return mixed
     */
    public function isCloseBonus()
    {
        return $this->is_close_bonus && $this->product->is_close_bonus;
    }

    /**
     * 是否可用于关闭红利
     *
     * @return mixed
     */
    public function isCloseAdjustment()
    {
        return $this->is_close_adjustment && $this->product->is_close_adjustment;
    }

    /**
     * 是否可用于关闭赎返
     *
     * @return mixed
     */
    public function isCloseCashBack()
    {
        return $this->is_close_cash_back && $this->product->is_close_cash_back;
    }

    /**
     * 是否可用于关闭充值
     *
     * @return mixed
     */
    public function isCloseDeposit()
    {
        return true;
    }


    /**
     * 是否可用于计算积分
     *
     * @return mixed
     */
    public function isCalculateReward()
    {
        return $this->is_calculate_reward;
    }

    /**
     * 是否可用于计算赎返
     *
     * @return mixed
     */
    public function isCalculateCashBack()
    {
        return $this->is_calculate_cash_back;
    }

    /**
     * 是否可用于计算返点
     *
     * @return mixed
     */
    public function isCalculateRebate()
    {
        return $this->is_calculate_rebate;
    }

    /**
     * 根据平台和唯一码获取游戏
     *
     * @param $platformCode
     * @param $code
     * @return Game
     */
    public static function findByPlatformAndCode($platformCode, $code)
    {
        return static::getAll()->where('platform_code', $platformCode)->where('code', $code)->first();
    }

    public function checkCurrencySet($currency)
    {
        return in_array($currency, $this->currencies);
    }

    public function getLanguageSet($language, $default = 'en-US')
    {
        $data = collect($this->languages)->where('language', $language)->first();
        if ($data) {
            return $data;
        }
        return collect($this->languages)->where('language', $default)->first();
    }

    /**
     * 获取游戏英文名字
     *
     * @return string
     */
    public function getEnName()
    {
        $currencySet = $this->getLanguageSet('en-US');

        if (!is_null($currencySet)) {
            return $currencySet['name'];
        } else {
            return '';
        }
    }

    /**
     * 通过游戏code获取所有游戏【这里不能用缓存，需要用db直接查，因为有的厂商大小写返回不一致】
     *
     * @param $platformCode
     * @param $gameCodes
     * @return mixed
     */
    public static function getByCodes($platformCode, $gameCodes)
    {
        return static::query()->where('platform_code', $platformCode)->whereIn('code', $gameCodes)->get();
    }
    # 方法 end
}
