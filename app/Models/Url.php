<?php

namespace App\Models;

class Url extends Model
{
    protected $fillable = [
        'type', 'device', 'platform', 'currencies', 'address', 'status', 'remark', 'update_by'
    ];

    protected $casts = [
        'status'     => 'boolean',
        'currencies' => 'array',
    ];

    # Type
    const TYPE_MEMBER    = 1;
    const TYPE_AFFILIATE = 2;

    public static $type = [
        self::TYPE_MEMBER    => 'MEMBER',
        self::TYPE_AFFILIATE => 'AFFILIATE',
    ];

    # Platform
    const PLATFORM_EG  = 1;
    const PLATFORM_HL8 = 2;

    public static $platform = [
        self::PLATFORM_EG  => 'EG',
        self::PLATFORM_HL8 => 'HL8',
    ];

    # 注册地址
    public static $suffix = [
        self::PLATFORM_EG  => '/reg',
        self::PLATFORM_HL8 => '/reg',
    ];
}
