<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class Config extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'configs';

    public static $cacheExpireInMinutes = 1440;

    protected $casts = [
        'is_front_show' => 'boolean',
    ];

    public $fillable = ['value'];

    # 分组
    const GROUP_USER      = 1; # 会员
    const GROUP_FUNCTION  = 2; # 功能
    const GROUP_PRIZE     = 3; # 奖金
    const GROUP_BANK      = 4; # 银行
    const GROUP_AFFILIATE = 5; # 代理

    /**
     *
     * 显示全部分組
     *
     * @var array
     */
    public static $groups = [
        self::GROUP_USER      => "会员",
        self::GROUP_FUNCTION  => "功能",
        self::GROUP_PRIZE     => "奖金",
        self::GROUP_BANK      => "银行",
        self::GROUP_AFFILIATE => "代理",
    ];

    # 自动更新秒数
    const AUTO_REFRESH_30_SECONDS   = 30; # 30秒数更新
    const AUTO_REFRESH_15_SECONDS   = 15; # 15秒数更新
    const AUTO_REFRESH_5_SECONDS    = 5;  # 5秒数更新
    const AUTO_REFRESH_DEFAULT      = 3;  # 预设秒数

    /**
     *
     * 显示自动更新秒数
     *
     * @var array
     */
    public static $autoRefreshes = [
        self::AUTO_REFRESH_30_SECONDS     => "30SECS",
        self::AUTO_REFRESH_15_SECONDS     => "15SECS",
        self::AUTO_REFRESH_5_SECONDS      => "5SECS",
        self::AUTO_REFRESH_DEFAULT        => "3SECS",
    ];

    # 方法 start
    public static function findValue($code, $default='')
    {
        if ($config = static::findByCodeFromCache($code)) {
            return $config->value;
        } else {
            return $default;
        }
    }
    # 方法 end
}
