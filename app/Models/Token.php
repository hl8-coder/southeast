<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Token extends Model
{
    public $guarded = [];

    public static function findAvailableCode()
    {
        do {
            $code = str_random(10);
        } while (static::query()->where('code', $code)->exists());

        return $code;
    }

    public static function findTokenBy($code, $default = '')
    {
        $token = static::query()->where('code', $code)->first();

        if ($token) {
            $default = $token->token;
        }

        return $default;
    }
}
