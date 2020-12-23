<?php

namespace App\Models\Traits;

trait RememberCache
{
    public static function getAll()
    {
        $result =  static::query()->remember(static::$cacheExpireInMinutes)->get();

        if ($result->isEmpty()) {
            $result = static::query()->get();
        }

        return $result;
    }

    public static function findByCache($id)
    {
        return static::getAll()->where('id', $id)->first();
    }

    public static function findByCodeFromCache($code)
    {
        return static::getAll()->where('code', $code)->first();
    }
}