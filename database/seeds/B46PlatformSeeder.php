<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;

class B46PlatformSeeder extends Seeder
{

    const CODE = 'B46';
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        #Game Platform Start
        if (app()->isLocal()) {
            $platform = [
                    'name'                  => self::CODE,
                    'code'                  => self::CODE,
                    'request_url'           => 'https://paapistg.oreo88.com/b2b',
                    'report_request_url'    => 'https://paapistg.oreo88.com/b2b',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"vnd_agent_code":"PXD03","vnd_agent_key":"f447b260-4489-4214-82f0-c6f73ac6d0c9","vnd_secret_key":"H1arBU8hGVeDIeQV", "thb_agent_code":"PXC04","thb_agent_key":"f447b260-4489-4214-82f0-c6f73ac6d0c9","thb_secret_key":"H1arBU8hGVeDIeQV", "try_url":"https://kgzfyl0.oreo88.com"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 10, # 时间跨度
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
            ];
        } else {

            $platform = [
                'name'                  => self::CODE,
                'code'                  => self::CODE,
                'request_url'           => 'http://api.ps3838.com/b2b',
                'report_request_url'    => 'http://api.ps3838.com/b2b',
                'launcher_request_url'  => '',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"vnd_agent_code":"4610101","vnd_agent_key":"cefa3ed0-30c5-41a4-a126-c10d1ab3fb23","vnd_secret_key":"jRV0MpbdNMvvvO2h", "thb_agent_code":"4620101","thb_agent_key":"cefa3ed0-30c5-41a4-a126-c10d1ab3fb23","thb_secret_key":"jRV0MpbdNMvvvO2h", "try_url":"https://kgzfyl0.oreo88.com"}',
                'exchange_currencies'   => null,
                'is_update_list'        => false,
                'update_interval'       => 1,
                'interval'              => 1, # 间隔时间
                'delay'                 => 10, # 延迟时间
                'offset'                => 10, # 时间跨度
                'limit'                 => 1, # 每分钟拉取次数
                'status'                => true,
                'icon'                  => '',
            ];

        }

        GamePlatform::insert($platform);
        #Game Platform End


        #Game Platform Product Start
        # Sports
        $products = [
            [
            'platform_code' => self::CODE,
            'code'          => 'B46_Sport',
            'type'          => GamePlatformProduct::TYPE_SPORT,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'B46_Sport',
                    'description' => 'B46_Sport',
                    'content'     => 'B46_Sport',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'B46_Sport',
                    'description' => 'B46_Sport',
                    'content'     => 'B46_Sport',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'B46_Sport',
                    'description' => 'B46_Sport',
                    'content'     => 'B46_Sport',
                ],
            ],
            'devices'       => [1, 2],
        ],
     ];

    foreach ($products as $product) {
        GamePlatformProduct::query()->create($product);
    }
    #Game Platform Product End


    #Game Platform Games Start
    $games=  [
        [
        'platform_code' => self::CODE,
        'product_code'  => 'B46_Sport',
        'code'          => 'B46Sports',
        'type'          => GamePlatformProduct::TYPE_SPORT,
        'currencies'    => ['VND', 'THB', 'USD'],
        'languages'    => [
            [
                'language'      => 'vi-VN',
                'name'          => 'B46Sports',
                'description'   => 'B46Sports',
                'content'       => 'B46Sports'
            ],
            [
                'language'      => 'th',
                'name'          => 'B46Sports',
                'description'   => 'B46Sports',
                'content'       => 'B46Sports'
            ],
            [
                'language'      => 'en-US',
                'name'          => 'B46Sports',
                'description'   => 'B46Sports',
                'content'       => 'B46Sports'
            ],
        ],
        'devices'       => [1, 2],
        ],
    ];


    foreach ($games as $game) {
        Game::query()->create($game);
    }
    #Game Platform Games End

    }
}
