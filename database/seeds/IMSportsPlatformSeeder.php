<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;
use App\Models\ChangingConfig;

class IMSportsPlatformSeeder extends Seeder
{

    const CODE = 'IMSports';
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
                    'request_url'           => 'http://imone.imaegisapi.com',
                    'report_request_url'    => 'http://imone.imaegisapi.com',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"vnd_merchant_name":"hl8prod","vnd_merchant_code":"OldS75ct3vlZmCl1lePN9iNgvPLbP5qT", "thb_merchant_name":"empiregemprod","thb_merchant_code":"O6Ge8ufoCpqXRfug8Qs83Zh5JFWdvogM"}',
                    'exchange_currencies'   => null,
                    'is_update_list'        => false,
                    'update_interval'       => 1,
                    'interval'              => 1, # 间隔时间
                    'delay'                 => 20, # 延迟时间
                    'offset'                => 10, # 时间跨度
                    'limit'                 => 1, # 每分钟拉取次数
                    'status'                => true,
                    'icon'                  => '',
            ];
        } else {
            $platform = [
                'name'                  => self::CODE,
                'code'                  => self::CODE,
                'request_url'           => 'http://imone.imaegisapi.com',
                'report_request_url'    => 'http://imone.imaegisapi.com',
                'launcher_request_url'  => '',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"vnd_merchant_name":"hl8prod","vnd_merchant_code":"OldS75ct3vlZmCl1lePN9iNgvPLbP5qT", "thb_merchant_name":"empiregemprod","thb_merchant_code":"O6Ge8ufoCpqXRfug8Qs83Zh5JFWdvogM"}',
                'exchange_currencies'   => null,
                'is_update_list'        => false,
                'update_interval'       => 1,
                'interval'              => 1, # 间隔时间
                'delay'                 => 20, # 延迟时间
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
            'code'          => 'IM_Sport',
            'type'          => GamePlatformProduct::TYPE_SPORT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'IM Sport',
                    'description' => 'IM Sport',
                    'content'     => 'IM Sport',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'IM Sport',
                    'description' => 'IM Sport',
                    'content'     => 'IM Sport',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'IM Sport',
                    'description' => 'IM Sport',
                    'content'     => 'IM Sport',
                ],
            ],
            'devices'       => [1, 2],
            ]
        ];

    foreach ($products as $product) {
        GamePlatformProduct::query()->create($product);
    }
    #Game Platform Product End


    #Game Platform Games Start
    $games=  [
        [
        'platform_code' => self::CODE,
        'product_code'  => 'IM_Sport',
        'code'          => 'IMSB',
        'type'          => GamePlatformProduct::TYPE_SPORT,
        'currencies'    => ['USD', 'VND', 'THB'],
        'languages'    => [
            [
                'language'      => 'vi-VN',
                'name'          => 'IM Sports',
                'description'   => 'IM Sports',
                'content'       => 'IM Sports'
            ],
            [
                'language'      => 'th',
                'name'          => 'IM Sports',
                'description'   => 'IM Sports',
                'content'       => 'IM Sports'
            ],
            [
                'language'      => 'en-US',
                'name'          => 'IM Sports',
                'description'   => 'IM Sports',
                'content'       => 'IM Sports'
            ],
        ],
        'devices'       => [1, 2],
        ]
    ];


    foreach ($games as $game) {
        Game::query()->create($game);
    }

    #Game Platform Games End

    }
}
