<?php

namespace App\Models;

class ChangingConfig extends Model
{
    protected $casts = [
        'is_front_show' => 'boolean',
    ];

    public static function findValue($code, $default='')
    {
        if ($config = static::query()->where('code', $code)->first()) {
            return $config->value;
        } else {
            return $default;
        }
    }

    public static function setValue($code, $value)
    {
        return static::query()->where('code', $code)->update([
            'value' => $value,
        ]);
    }
}
