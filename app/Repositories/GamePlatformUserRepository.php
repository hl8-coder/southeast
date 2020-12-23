<?php

namespace App\Repositories;

use App\Models\Config;
use App\Models\ExchangeRate;
use App\Models\GamePlatform;
use App\Models\GamePlatformUser;
use App\Models\User;

class GamePlatformUserRepository
{
    /**
     * 通过会员id和平台id获取平台会员
     *
     * @param $platformCode
     * @param $userId
     * @return mixed
     */
    public static function findByUserAndPlatform($userId, $platformCode)
    {
        return GamePlatformUser::query()->where('user_id', $userId)->where('platform_code', $platformCode)->first();
    }

    /**
     * 通过会员名称和平台id获取平台会员
     *
     * @param  string   $platformCode
     * @param  string   $name
     * @return mixed
     */
    public static function findByNameAndPlatform($platformCode, $name)
    {
        return GamePlatformUser::query()->where('platform_code', $platformCode)->where('name', $name)->first();
    }

    /**
     * 单会员注册单平台
     *
     * @param User $user
     * @param GamePlatform $platform
     * @return mixed
     */
    public static function userRegisterPlatform(User $user, GamePlatform $platform)
    {
        $data = static::getPlatformUserData($user, $platform);

        return GamePlatformUser::create($data);
    }

    /**
     * 单会员注册所有平台
     *
     * @param User $user
     */
    public static function userRegisterAllPlatform(User $user)
    {
        foreach (GamePlatform::getAll() as $platform) {
            $data[] = static::getPlatformUserData($user, $platform);
        }

        if (!empty($data)) {
            batch_insert(app(GamePlatformUser::class)->getTable(), $data, true);
        }
    }

    /**
     * 所有会员注册单平台
     *
     * @param GamePlatform $platform
     */
    public static function allUserRegisterPlatform(GamePlatform $platform)
    {
        foreach (User::get() as $user) {
            $data[] = static::getPlatformUserData($user, $platform);
        }

        if (!empty($data)) {
            $count = count($data);
            $slice = 2000;
            $num   = ceil($count / $slice);
            for ($i = 1; $i <= $num; $i++) {
                $start = ($i - 1) * $slice;
                $insertData = array_slice($data, $start, $slice, true);
                batch_insert(app(GamePlatformUser::class)->getTable(), $insertData, true);
            }

        }
    }

    /**
     * 拼接第三方游戏会员数据
     *
     * @param User $user
     * @param GamePlatform $platform
     * @return array
     */
    protected static function getPlatformUserData(User $user, GamePlatform $platform)
    {
        # 判断是否需要转换币别
        $currency = $user->currency;
        if ($rate = ExchangeRate::findRateByUserAndPlatform($user, $platform)) {
            $currency = $rate->platform_currency;
        }

        return [
            'platform_code'    => $platform->code,
            'user_id'          => $user->id,
            'user_name'        => $user->name,
            'currency'         => $currency,
            'name'             => static::getPlatformUserName($user, $platform->code),
            'password'         => str_random(GamePlatformUser::PASSWORD_LENGTH),
        ];
    }

    /**
     * 获取第三方游戏会员名称[品牌_会员名字]
     *
     * @param User $user
     * @param string $platformCode
     * @return string
     */
    public static function getPlatformUserName(User $user, $platformCode)
    {
        $operationId = Config::findValue('operation_id');

        # 特殊处理PP
        if ('PP' == $platformCode) {
            $operationId .=  '_';
        }

        return $operationId . $user->name;
    }
}
