<?php

use App\Models\User;
use Illuminate\Database\Seeder;
use App\Models\Url;

class UrlsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Url::query()->truncate();

        $data = [
            [
                'type'       => Url::TYPE_MEMBER,
                'device'     => User::DEVICE_PC,
                'platform'   => Url::PLATFORM_EG,
                'address'    => "www.empiregem.club",
                'currencies' => json_encode(["THB", "USD", "CNY"]),
                'status'     => true,
            ],
            [
                'type'       => Url::TYPE_AFFILIATE,
                'device'     => User::DEVICE_PC,
                'platform'   => Url::PLATFORM_EG,
                'address'    => "www.empiregem.club",
                'currencies' => json_encode(["THB", "USD", "CNY"]),
                'status'     => true,
            ],
            [
                'type'       => Url::TYPE_MEMBER,
                'device'     => User::DEVICE_PC,
                'platform'   => Url::PLATFORM_HL8,
                'address'    => "hl8viet.fun",
                'currencies' => json_encode(["VND", "USD", "CNY"]),
                'status'     => true,
            ],
            [
                'type'       => Url::TYPE_AFFILIATE,
                'device'     => User::DEVICE_PC,
                'platform'   => Url::PLATFORM_HL8,
                'address'    => "hl8vn.fun",
                'currencies' => json_encode(["VND", "USD", "CNY"]),
                'status'     => true,
            ],
            [
                'type'       => Url::TYPE_MEMBER,
                'device'     => User::DEVICE_MOBILE,
                'platform'   => Url::PLATFORM_HL8,
                'address'    => "m.hl8viet.fun",
                'currencies' => json_encode(["VND", "USD", "CNY"]),
                'status'     => true,
            ],
            [
                'type'       => Url::TYPE_AFFILIATE,
                'device'     => User::DEVICE_MOBILE,
                'platform'   => Url::PLATFORM_HL8,
                'address'    => "m.hl8vn.fun",
                'currencies' => json_encode(["VND", "USD", "CNY"]),
                'status'     => true,
            ],
        ];
        Url::query()->insert($data);
    }
}
