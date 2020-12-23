<?php

namespace App\Models;

class Advertisement extends Model
{

    protected $fillable = [
        'currency',  'description', 'img_link_url', 'alone_link_url', 'target_type', 'show_type', 'sort',
    ];

    protected $casts = [
        'status' => 'boolean',
    ];

    # 跳转方式
    const TARGET_TYPE_NO_JUMP   = 1;
    const TARGET_TYPE_INNER     = 2;
    const TARGET_TYPE_OUTER     = 3;

    public static $targetTypes = [
        self::TARGET_TYPE_NO_JUMP   => 'no_jump',
        self::TARGET_TYPE_INNER     => 'inner',
        self::TARGET_TYPE_OUTER     => 'outer',
    ];

    # 显示位置
    const SHOW_TYPE_LOGIN   = 1;
    const SHOW_TYPE_HEADER  = 2;
    const SHOW_TYPE_ALL     = 3;

    public static $showTypes = [
        self::SHOW_TYPE_LOGIN   => 'login',
        self::SHOW_TYPE_HEADER  => 'home',
        self::SHOW_TYPE_ALL     => 'all',
    ];

    public function scopeEnable($query)
    {
        return $query->where('status', true);
    }
}
