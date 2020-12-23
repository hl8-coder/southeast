<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;

class SSPlatformSeeder extends Seeder
{

    const CODE = 'SS';
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
                'request_url'           => 'http://test.ssgportal.com:8101/GamblingService/GamblingWebService.asmx?WSDL',
                'report_request_url'    => 'http://test.ssgportal.com:8101/GamblingService/GamblingWebService.asmx?WSDL',
                'launcher_request_url'  => '',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"client_external_key":"1001","portal_name":"TestPortal","hash_value":"4f306f1b13bb49759aaced44a44d20d7", "slot_url":"http://staging.ssgportal.com:8101/Slots/Loader.aspx?GameType=Slots&StartPage=Game", "fish_url":"http://staging.ssgportal.com:8101/JetX/JetX/Loader.aspx?StartPage=Board"}',
                'exchange_currencies'   => null,
                'is_update_list'        => false,
                'update_interval'       => 1,
                'interval'              => 2, # 间隔时间
                'delay'                 => 10, # 延迟时间
                'offset'                => 20, # 向前偏移时间
                'limit'                 => 1, # 每分钟拉取次数
                'status'                => true,
                'icon'                  => '',
            ];
        } else {
            $platform = [
                'name'                  => self::CODE,
                'code'                  => self::CODE,
                'request_url'           => 'http://test.ssgportal.com:8101/GamblingService/GamblingWebService.asmx?WSDL',
                'report_request_url'    => 'http://test.ssgportal.com:8101/GamblingService/GamblingWebService.asmx?WSDL',
                'launcher_request_url'  => '',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"client_external_key":"1001","portal_name":"TestPortal","hash_value":"4f306f1b13bb49759aaced44a44d20d7", "slot_url":"http://staging.ssgportal.com:8101/Slots/Loader.aspx?GameType=OtherGamesSlots&StartPage=Game", "fish_url":"http://staging.ssgportal.com:8101/JetX/JetX/Loader.aspx?StartPage=Board"}',
                'exchange_currencies'   => null,
                'is_update_list'        => false,
                'update_interval'       => 1,
                'interval'              => 2, # 间隔时间
                'delay'                 => 10, # 延迟时间
                'offset'                => 20, # 向前偏移时间
                'limit'                 => 1, # 每分钟拉取次数
                'status'                => true,
                'icon'                  => '',
            ];

        }

        GamePlatform::insert($platform);
        #Game Platform End

        #Game Platform Product Start
        $products = [
            [
                'platform_code'  => self::CODE,
                'code'           => 'SS_Slot',
                'type'           => GamePlatformProduct::TYPE_SLOT,
                'currencies'     => ['VND', 'THB'],
                'languages'     => [
                    [
                        'language'    => 'en-US',
                        'name'        => 'SS_Slot',
                        'description' => 'SS_Slot',
                        'content'     => 'SS_Slot',
                    ],
                    [
                        'language'    => 'vi-VN',
                        'name'        => 'SS_Slot',
                        'description' => 'SS_Slot',
                        'content'     => 'SS_Slot',
                    ],
                    [
                        'language'    => 'th',
                        'name'        => 'SS_Slot',
                        'description' => 'SS_Slot',
                        'content'     => 'SS_Slot',
                    ],
                ],
                'devices'        => [1, 2],
            ],
            [
                'platform_code'  => self::CODE,
                'code'           => 'SS_Fish',
                'type'           => GamePlatformProduct::TYPE_FISH,
                'currencies'     => ['VND', 'THB'],
                'languages'     => [
                    [
                        'language'    => 'en',
                        'name'        => 'SS_Fish',
                        'description' => 'SS_Fish',
                        'content'     => 'SS_Fish',
                    ],
                    [
                        'language'    => 'vi',
                        'name'        => 'SS_Fish',
                        'description' => 'SS_Fish',
                        'content'     => 'SS_Fish',
                    ],
                    [
                        'language'    => 'th',
                        'name'        => 'SS_Fish',
                        'description' => 'SS_Fish',
                        'content'     => 'SS_Fish',
                    ],
                ],
                'devices'        => [1, 2],
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
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Viking',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Viking',
                        'description'   => 'Viking',
                        'content'       => 'Viking'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Viking',
                        'description'   => 'Viking',
                        'content'       => 'Viking'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Viking',
                        'description'   => 'Viking',
                        'content'       => 'Viking'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Aztec',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Aztec',
                        'description'   => 'Aztec',
                        'content'       => 'Aztec'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Aztec',
                        'description'   => 'Aztec',
                        'content'       => 'Aztec'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Aztec',
                        'description'   => 'Aztec',
                        'content'       => 'Aztec'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Birds',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Birds',
                        'description'   => 'Birds',
                        'content'       => 'Birds'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Birds',
                        'description'   => 'Birds',
                        'content'       => 'Birds'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Birds',
                        'description'   => 'Birds',
                        'content'       => 'Birds'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Galaxy',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Galaxy',
                        'description'   => 'Galaxy',
                        'content'       => 'Galaxy'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Galaxy',
                        'description'   => 'Galaxy',
                        'content'       => 'Galaxy'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Galaxy',
                        'description'   => 'Galaxy',
                        'content'       => 'Galaxy'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Cowboy',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Cowboy',
                        'description'   => 'Cowboy',
                        'content'       => 'Cowboy'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Cowboy',
                        'description'   => 'Cowboy',
                        'content'       => 'Cowboy'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Cowboy',
                        'description'   => 'Cowboy',
                        'content'       => 'Cowboy'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'BookOfWin',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'BookOfWin',
                        'description'   => 'BookOfWin',
                        'content'       => 'BookOfWin'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'BookOfWin',
                        'description'   => 'BookOfWin',
                        'content'       => 'BookOfWin'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'BookOfWin',
                        'description'   => 'BookOfWin',
                        'content'       => 'BookOfWin'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Christmas',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Christmas',
                        'description'   => 'Christmas',
                        'content'       => 'Christmas'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Christmas',
                        'description'   => 'Christmas',
                        'content'       => 'Christmas'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Christmas',
                        'description'   => 'Christmas',
                        'content'       => 'Christmas'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Pharaoh',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Pharaoh',
                        'description'   => 'Pharaoh',
                        'content'       => 'Pharaoh'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Pharaoh',
                        'description'   => 'Pharaoh',
                        'content'       => 'Pharaoh'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Pharaoh',
                        'description'   => 'Pharaoh',
                        'content'       => 'Pharaoh'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'DonutCity',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'DonutCity',
                        'description'   => 'DonutCity',
                        'content'       => 'DonutCity'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'DonutCity',
                        'description'   => 'DonutCity',
                        'content'       => 'DonutCity'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'DonutCity',
                        'description'   => 'DonutCity',
                        'content'       => 'DonutCity'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Samurai',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Samurai',
                        'description'   => 'Samurai',
                        'content'       => 'Samurai'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Samurai',
                        'description'   => 'Samurai',
                        'content'       => 'Samurai'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Samurai',
                        'description'   => 'Samurai',
                        'content'       => 'Samurai'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Football',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Football',
                        'description'   => 'Football',
                        'content'       => 'Football'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Football',
                        'description'   => 'Football',
                        'content'       => 'Football'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Football',
                        'description'   => 'Football',
                        'content'       => 'Football'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Slot',
                'type'          => GamePlatformProduct::TYPE_SLOT,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'Argo',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Argo',
                        'description'   => 'Argo',
                        'content'       => 'Argo'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Argo',
                        'description'   => 'Argo',
                        'content'       => 'Argo'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Argo',
                        'description'   => 'Argo',
                        'content'       => 'Argo'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => self::CODE,
                'product_code'  => 'SS_Fish',
                'type'          => GamePlatformProduct::TYPE_FISH,
                'currencies'    =>  ['VND', 'THB', 'USD'],
                'code'          => 'JetX',
                'languages'    => [
                    [
                        'language'      => 'vi',
                        'name'          => 'JetX',
                        'description'   => 'JetX',
                        'content'       => 'JetX'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'JetX',
                        'description'   => 'JetX',
                        'content'       => 'JetX'
                    ],
                    [
                        'language'      => 'en',
                        'name'          => 'JetX',
                        'description'   => 'JetX',
                        'content'       => 'JetX'
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
