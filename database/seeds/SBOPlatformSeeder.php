<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;
use App\Models\ChangingConfig;

class SBOPlatformSeeder extends Seeder
{

    const CODE = 'SBO';
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
                    'request_url'           => 'http://hl8.test.gf-gaming.com/gf',
                    'report_request_url'    => 'http://hl8.test.gf-gaming.com/gf',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"vnd_operator_token":"c8efe952c12164152508079873c53273","vnd_secret_key":"f78e344d8d374f2bfa3186a99bd6e321", "thb_operator_token":"fbe46383db390907541a234bec7f2424","thb_secret_key":"5fd7c6cb5bbecfb589d01251de289485"}',
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
                'request_url'           => 'http://hl8.test.gf-gaming.com/gf',
                'report_request_url'    => 'http://hl8.test.gf-gaming.com/gf',
                'launcher_request_url'  => '',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"vnd_operator_token":"c8efe952c12164152508079873c53273","vnd_secret_key":"f78e344d8d374f2bfa3186a99bd6e321", "thb_operator_token":"fbe46383db390907541a234bec7f2424","thb_secret_key":"5fd7c6cb5bbecfb589d01251de289485"}',
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
            'code'          => 'SBO_Sport',
            'type'          => GamePlatformProduct::TYPE_SPORT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'SBO Sport',
                    'description' => 'SBO Sport',
                    'content'     => 'SBO Sport',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'SBO Sport',
                    'description' => 'SBO Sport',
                    'content'     => 'SBO Sport',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'SBO Sport',
                    'description' => 'SBO Sport',
                    'content'     => 'SBO Sport',
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
        'product_code'  => 'SBO_Sport',
        'code'          => 'sbo_1',
        'type'          => GamePlatformProduct::TYPE_SPORT,
        'currencies'    => ['VND'],
        'languages'    => [
            [
                'language'      => 'vi-VN',
                'name'          => 'SBO Sports',
                'description'   => 'SBO Sports',
                'content'       => 'SBO Sports'
            ],
            [
                'language'      => 'th',
                'name'          => 'SBO Sports',
                'description'   => 'SBO Sports',
                'content'       => 'SBO Sports'
            ],
            [
                'language'      => 'en-US',
                'name'          => 'SBO Sports',
                'description'   => 'SBO Sports',
                'content'       => 'SBO Sports'
            ],
        ],
        'devices'       => [1, 2],
        ]
    ];


    foreach ($games as $game) {
        Game::query()->create($game);
    }

    #Changing config
    $configs = [
        [
            'code'          => 'sb_vnd_row_version',
            'name'          => 'SB sb_vnd_row_version',
            'remark'        => 'SB sb_vnd_row_version',
            'is_front_show' => false,
            'type'          => 'string',
            'value'         => 0,
        ],
        [
            'code'          => 'sb_thb_row_version',
            'name'          => 'SB sb_thb_row_version',
            'remark'        => 'SB sb_thb_row_version',
            'is_front_show' => false,
            'type'          => 'string',
            'value'         => 0,
        ]
    ];
    ChangingConfig::insert($configs);

    #Game Platform Games End


    }
}
