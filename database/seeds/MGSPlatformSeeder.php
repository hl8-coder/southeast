<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;
use App\Models\ChangingConfig;

class MGSPlatformSeeder extends Seeder
{

    const CODE = 'MGS';
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
                    'request_url'           => 'https://api-jugaminga12.k2net.io/api/v1',
                    'report_request_url'    => 'https://api-jugaminga12.k2net.io/api/v1',
                    'launcher_request_url'  => '',
                    'rsa_our_private_key'   => '',
                    'rsa_our_public_key'    => '',
                    'rsa_public_key'        => '',
                    'account'               => '{"agent_code":"TM50HL8","secret_key":"562c1869afb346348b329560322e3f","token_url":"https://sts-jugaminga12.k2net.io/connect/token"}',
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
                'request_url'           => 'https://api-jugaminga12.k2net.io/api/v1',
                'report_request_url'    => 'https://api-jugaminga12.k2net.io/api/v1',
                'launcher_request_url'  => '',
                'rsa_our_private_key'   => '',
                'rsa_our_public_key'    => '',
                'rsa_public_key'        => '',
                'account'               => '{"agent_code":"TM50HL8","secret_key":"562c1869afb346348b329560322e3f" ,"token_url":"https://sts-jugaminga12.k2net.io/connect/token"}',
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
            'code'          => 'MGS_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'MGS_Live',
                    'description' => 'MGS_Live',
                    'content'     => 'MGS_Live',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'MGS_Live',
                    'description' => 'MGS_Live',
                    'content'     => 'MGS_Live',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'MGS_Live',
                    'description' => 'MGS_Live',
                    'content'     => 'MGS_Live',
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'code'          => 'MGS_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'MGS_Slot',
                    'description' => 'MGS_Slot',
                    'content'     => 'MGS_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'MGS_Slot',
                    'description' => 'MGS_Slot',
                    'content'     => 'MGS_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'MGS_Slot',
                    'description' => 'MGS_Slot',
                    'content'     => 'MGS_Slot',
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'code'          => 'MGS_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'MGS_Fish',
                    'description' => 'MGS_Fish',
                    'content'     => 'MGS_Fish',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'MGS_Fish',
                    'description' => 'MGS_Fish',
                    'content'     => 'MGS_Fish',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'MGS_Fish',
                    'description' => 'MGS_Fish',
                    'content'     => 'MGS_Fish',
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
        'product_code'  => 'MGS_Live',
        'code'          => 'MGS_Live',
        'type'          => GamePlatformProduct::TYPE_LIVE,
        'currencies'    => ['VND'],
        'languages'    => [
            [
                'language'      => 'vi-VN',
                'name'          => 'Roullete, Baccarat, Sic-Bo',
                'description'   => 'Roullete, Baccarat, Sic-Bo',
                'content'       => 'Roullete, Baccarat, Sic-Bo'
            ],
            [
                'language'      => 'th',
                'name'          => 'Roullete, Baccarat, Sic-Bo',
                'description'   => 'Roullete, Baccarat, Sic-Bo',
                'content'       => 'Roullete, Baccarat, Sic-Bo'
            ],
            [
                'language'      => 'en-US',
                'name'          => 'Roullete, Baccarat, Sic-Bo',
                'description'   => 'Roullete, Baccarat, Sic-Bo',
                'content'       => 'Roullete, Baccarat, Sic-Bo'
            ],
        ],
        'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_Baccarat_Playboy',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat - Playboy',
                    'description'   => 'Baccarat - Playboy',
                    'content'       => 'Baccarat - Playboy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat - Playboy',
                    'description'   => 'Baccarat - Playboy',
                    'content'       => 'Baccarat - Playboy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat - Playboy',
                    'description'   => 'Baccarat - Playboy',
                    'content'       => 'Baccarat - Playboy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_BaccaratplayboyNC',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat - Playboy (NC)',
                    'description'   => 'Baccarat - Playboy (NC)',
                    'content'       => 'Baccarat - Playboy (NC)'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat - Playboy (NC)',
                    'description'   => 'Baccarat - Playboy (NC)',
                    'content'       => 'Baccarat - Playboy (NC)'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat - Playboy (NC)',
                    'description'   => 'Baccarat - Playboy (NC)',
                    'content'       => 'Baccarat - Playboy (NC)'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_BaccaratNC',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat (NC)',
                    'description'   => 'Baccarat (NC)',
                    'content'       => 'Baccarat (NC)'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat (NC)',
                    'description'   => 'Baccarat (NC)',
                    'content'       => 'Baccarat (NC)'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat (NC)',
                    'description'   => 'Baccarat (NC)',
                    'content'       => 'Baccarat (NC)'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_Baccarat',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat Live',
                    'description'   => 'Baccarat Live',
                    'content'       => 'Baccarat Live'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat Live',
                    'description'   => 'Baccarat Live',
                    'content'       => 'Baccarat Live'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat Live',
                    'description'   => 'Baccarat Live',
                    'content'       => 'Baccarat Live'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_MP_Baccarat',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'MP Baccarat',
                    'description'   => 'MP Baccarat',
                    'content'       => 'MP Baccarat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'MP Baccarat',
                    'description'   => 'MP Baccarat',
                    'content'       => 'MP Baccarat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'MP Baccarat',
                    'description'   => 'MP Baccarat',
                    'content'       => 'MP Baccarat'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_MP_Baccarat_Playboy',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'MP Baccarat - Playboy',
                    'description'   => 'MP Baccarat - Playboy',
                    'content'       => 'MP Baccarat - Playboy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'MP Baccarat - Playboy',
                    'description'   => 'MP Baccarat - Playboy',
                    'content'       => 'MP Baccarat - Playboy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'MP Baccarat - Playboy',
                    'description'   => 'MP Baccarat - Playboy',
                    'content'       => 'MP Baccarat - Playboy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_Roulette_Playboy',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Roulette - Playboy',
                    'description'   => 'Roulette - Playboy',
                    'content'       => 'Roulette - Playboy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Roulette - Playboy',
                    'description'   => 'Roulette - Playboy',
                    'content'       => 'Roulette - Playboy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Roulette - Playboy',
                    'description'   => 'Roulette - Playboy',
                    'content'       => 'Roulette - Playboy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_Roulette',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Roulette',
                    'description'   => 'Roulette',
                    'content'       => 'Roulette'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Roulette',
                    'description'   => 'Roulette',
                    'content'       => 'Roulette'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Roulette',
                    'description'   => 'Roulette',
                    'content'       => 'Roulette'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Live',
            'code'          => 'SMG_titaniumLiveGames_Sicbo',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND'],
            'status'        => 0,
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sicbo Live',
                    'description'   => 'Sicbo Live',
                    'content'       => 'Sicbo Live'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sicbo Live',
                    'description'   => 'Sicbo Live',
                    'content'       => 'Sicbo Live'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sicbo Live',
                    'description'   => 'Sicbo Live',
                    'content'       => 'Sicbo Live'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_108Heroes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => '108 Heroes',
                    'description'   => '108 Heroes',
                    'content'       => '108 Heroes'
                ],
                [
                    'language'      => 'th',
                    'name'          => '108 Heroes',
                    'description'   => '108 Heroes',
                    'content'       => '108 Heroes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => '108 Heroes',
                    'description'   => '108 Heroes',
                    'content'       => '108 Heroes'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_108heroesMultiplierFortunes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => '108 Heroes Multiplier Fortunes',
                    'description'   => '108 Heroes Multiplier Fortunes',
                    'content'       => '108 Heroes Multiplier Fortunes'
                ],
                [
                    'language'      => 'th',
                    'name'          => '108 Heroes Multiplier Fortunes',
                    'description'   => '108 Heroes Multiplier Fortunes',
                    'content'       => '108 Heroes Multiplier Fortunes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => '108 Heroes Multiplier Fortunes',
                    'description'   => '108 Heroes Multiplier Fortunes',
                    'content'       => '108 Heroes Multiplier Fortunes'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_5ReelDrive',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => '5 Reel Drive',
                    'description'   => '5 Reel Drive',
                    'content'       => '5 Reel Drive'
                ],
                [
                    'language'      => 'th',
                    'name'          => '5 Reel Drive',
                    'description'   => '5 Reel Drive',
                    'content'       => '5 Reel Drive'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => '5 Reel Drive',
                    'description'   => '5 Reel Drive',
                    'content'       => '5 Reel Drive'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_actionOpsSnowAndSable',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'ActionOps Snow and Sable',
                    'description'   => 'ActionOps Snow and Sable',
                    'content'       => 'ActionOps Snow and Sable'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'ActionOps Snow and Sable',
                    'description'   => 'ActionOps Snow and Sable',
                    'content'       => 'ActionOps Snow and Sable'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'ActionOps Snow and Sable',
                    'description'   => 'ActionOps Snow and Sable',
                    'content'       => 'ActionOps Snow and Sable'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_adventurePalace',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Adventure Palace',
                    'description'   => 'Adventure Palace',
                    'content'       => 'Adventure Palace'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Adventure Palace',
                    'description'   => 'Adventure Palace',
                    'content'       => 'Adventure Palace'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Adventure Palace',
                    'description'   => 'Adventure Palace',
                    'content'       => 'Adventure Palace'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ageOfDiscovery',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age Of Discovery',
                    'description'   => 'Age Of Discovery',
                    'content'       => 'Age Of Discovery'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age Of Discovery',
                    'description'   => 'Age Of Discovery',
                    'content'       => 'Age Of Discovery'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age Of Discovery',
                    'description'   => 'Age Of Discovery',
                    'content'       => 'Age Of Discovery'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_agentJaneBlonde',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Agent Jane Blonde',
                    'description'   => 'Agent Jane Blonde',
                    'content'       => 'Agent Jane Blonde'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Agent Jane Blonde',
                    'description'   => 'Agent Jane Blonde',
                    'content'       => 'Agent Jane Blonde'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Agent Jane Blonde',
                    'description'   => 'Agent Jane Blonde',
                    'content'       => 'Agent Jane Blonde'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_agentjaneblondereturns',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Agent Jane Blonde Returns',
                    'description'   => 'Agent Jane Blonde Returns',
                    'content'       => 'Agent Jane Blonde Returns'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Agent Jane Blonde Returns',
                    'description'   => 'Agent Jane Blonde Returns',
                    'content'       => 'Agent Jane Blonde Returns'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Agent Jane Blonde Returns',
                    'description'   => 'Agent Jane Blonde Returns',
                    'content'       => 'Agent Jane Blonde Returns'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_alaskanFishing',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Alaskan Fishing',
                    'description'   => 'Alaskan Fishing',
                    'content'       => 'Alaskan Fishing'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Alaskan Fishing',
                    'description'   => 'Alaskan Fishing',
                    'content'       => 'Alaskan Fishing'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Alaskan Fishing',
                    'description'   => 'Alaskan Fishing',
                    'content'       => 'Alaskan Fishing'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ancientFortunesZeus',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ancient Fortunes: Zeus',
                    'description'   => 'Ancient Fortunes: Zeus',
                    'content'       => 'Ancient Fortunes: Zeus'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ancient Fortunes: Zeus',
                    'description'   => 'Ancient Fortunes: Zeus',
                    'content'       => 'Ancient Fortunes: Zeus'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ancient Fortunes: Zeus',
                    'description'   => 'Ancient Fortunes: Zeus',
                    'content'       => 'Ancient Fortunes: Zeus'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ariana',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ariana',
                    'description'   => 'Ariana',
                    'content'       => 'Ariana'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ariana',
                    'description'   => 'Ariana',
                    'content'       => 'Ariana'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ariana',
                    'description'   => 'Ariana',
                    'content'       => 'Ariana'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_asianBeauty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Asian Beauty',
                    'description'   => 'Asian Beauty',
                    'content'       => 'Asian Beauty'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Asian Beauty',
                    'description'   => 'Asian Beauty',
                    'content'       => 'Asian Beauty'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Asian Beauty',
                    'description'   => 'Asian Beauty',
                    'content'       => 'Asian Beauty'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_avalon',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Avalon',
                    'description'   => 'Avalon',
                    'content'       => 'Avalon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Avalon',
                    'description'   => 'Avalon',
                    'content'       => 'Avalon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Avalon',
                    'description'   => 'Avalon',
                    'content'       => 'Avalon'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_badmintonHero',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Badminton Hero',
                    'description'   => 'Badminton Hero',
                    'content'       => 'Badminton Hero'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Badminton Hero',
                    'description'   => 'Badminton Hero',
                    'content'       => 'Badminton Hero'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Badminton Hero',
                    'description'   => 'Badminton Hero',
                    'content'       => 'Badminton Hero'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_barBarBlackSheep5Reel',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bar Bar Black Sheep 5 Reel',
                    'description'   => 'Bar Bar Black Sheep 5 Reel',
                    'content'       => 'Bar Bar Black Sheep 5 Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bar Bar Black Sheep 5 Reel',
                    'description'   => 'Bar Bar Black Sheep 5 Reel',
                    'content'       => 'Bar Bar Black Sheep 5 Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bar Bar Black Sheep 5 Reel',
                    'description'   => 'Bar Bar Black Sheep 5 Reel',
                    'content'       => 'Bar Bar Black Sheep 5 Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BarsAndStripes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bars And Stripes',
                    'description'   => 'Bars And Stripes',
                    'content'       => 'Bars And Stripes'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bars And Stripes',
                    'description'   => 'Bars And Stripes',
                    'content'       => 'Bars And Stripes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bars And Stripes',
                    'description'   => 'Bars And Stripes',
                    'content'       => 'Bars And Stripes'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_basketballStar',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Basketball Star',
                    'description'   => 'Basketball Star',
                    'content'       => 'Basketball Star'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Basketball Star',
                    'description'   => 'Basketball Star',
                    'content'       => 'Basketball Star'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Basketball Star',
                    'description'   => 'Basketball Star',
                    'content'       => 'Basketball Star'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_basketballStarDeluxe',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Basketball Star Deluxe',
                    'description'   => 'Basketball Star Deluxe',
                    'content'       => 'Basketball Star Deluxe'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Basketball Star Deluxe',
                    'description'   => 'Basketball Star Deluxe',
                    'content'       => 'Basketball Star Deluxe'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Basketball Star Deluxe',
                    'description'   => 'Basketball Star Deluxe',
                    'content'       => 'Basketball Star Deluxe'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_beachBabes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Beach Babes',
                    'description'   => 'Beach Babes',
                    'content'       => 'Beach Babes'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Beach Babes',
                    'description'   => 'Beach Babes',
                    'content'       => 'Beach Babes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Beach Babes',
                    'description'   => 'Beach Babes',
                    'content'       => 'Beach Babes'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_beautifulBones',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Beautiful Bones',
                    'description'   => 'Beautiful Bones',
                    'content'       => 'Beautiful Bones'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Beautiful Bones',
                    'description'   => 'Beautiful Bones',
                    'content'       => 'Beautiful Bones'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Beautiful Bones',
                    'description'   => 'Beautiful Bones',
                    'content'       => 'Beautiful Bones'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bigKahuna',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Kahuna',
                    'description'   => 'Big Kahuna',
                    'content'       => 'Big Kahuna'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Kahuna',
                    'description'   => 'Big Kahuna',
                    'content'       => 'Big Kahuna'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Kahuna',
                    'description'   => 'Big Kahuna',
                    'content'       => 'Big Kahuna'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bigTop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Top',
                    'description'   => 'Big Top',
                    'content'       => 'Big Top'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Top',
                    'description'   => 'Big Top',
                    'content'       => 'Big Top'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Top',
                    'description'   => 'Big Top',
                    'content'       => 'Big Top'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bikiniParty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bikini Party',
                    'description'   => 'Bikini Party',
                    'content'       => 'Bikini Party'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bikini Party',
                    'description'   => 'Bikini Party',
                    'content'       => 'Bikini Party'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bikini Party',
                    'description'   => 'Bikini Party',
                    'content'       => 'Bikini Party'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_boogieMonsters',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Boogie Monsters',
                    'description'   => 'Boogie Monsters',
                    'content'       => 'Boogie Monsters'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Boogie Monsters',
                    'description'   => 'Boogie Monsters',
                    'content'       => 'Boogie Monsters'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Boogie Monsters',
                    'description'   => 'Boogie Monsters',
                    'content'       => 'Boogie Monsters'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bookOfOz',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Book of Oz',
                    'description'   => 'Book of Oz',
                    'content'       => 'Book of Oz'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Book of Oz',
                    'description'   => 'Book of Oz',
                    'content'       => 'Book of Oz'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Book of Oz',
                    'description'   => 'Book of Oz',
                    'content'       => 'Book of Oz'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bookieOfOdds',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bookie of Odds',
                    'description'   => 'Bookie of Odds',
                    'content'       => 'Bookie of Odds'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bookie of Odds',
                    'description'   => 'Bookie of Odds',
                    'content'       => 'Bookie of Odds'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bookie of Odds',
                    'description'   => 'Bookie of Odds',
                    'content'       => 'Bookie of Odds'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_breakAway',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Break Away',
                    'description'   => 'Break Away',
                    'content'       => 'Break Away'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Break Away',
                    'description'   => 'Break Away',
                    'content'       => 'Break Away'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Break Away',
                    'description'   => 'Break Away',
                    'content'       => 'Break Away'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_breakAwayDeluxe',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Break Away Deluxe',
                    'description'   => 'Break Away Deluxe',
                    'content'       => 'Break Away Deluxe'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Break Away Deluxe',
                    'description'   => 'Break Away Deluxe',
                    'content'       => 'Break Away Deluxe'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Break Away Deluxe',
                    'description'   => 'Break Away Deluxe',
                    'content'       => 'Break Away Deluxe'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_breakDaBank',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Break da Bank',
                    'description'   => 'Break da Bank',
                    'content'       => 'Break da Bank'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Break da Bank',
                    'description'   => 'Break da Bank',
                    'content'       => 'Break da Bank'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Break da Bank',
                    'description'   => 'Break da Bank',
                    'content'       => 'Break da Bank'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_breakDaBankAgain',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Break da Bank Again',
                    'description'   => 'Break da Bank Again',
                    'content'       => 'Break da Bank Again'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Break da Bank Again',
                    'description'   => 'Break da Bank Again',
                    'content'       => 'Break da Bank Again'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Break da Bank Again',
                    'description'   => 'Break da Bank Again',
                    'content'       => 'Break da Bank Again'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bridesmaids',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bridesmaids',
                    'description'   => 'Bridesmaids',
                    'content'       => 'Bridesmaids'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bridesmaids',
                    'description'   => 'Bridesmaids',
                    'content'       => 'Bridesmaids'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bridesmaids',
                    'description'   => 'Bridesmaids',
                    'content'       => 'Bridesmaids'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bullseyeGameshow',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bullseye Gameshow',
                    'description'   => 'Bullseye Gameshow',
                    'content'       => 'Bullseye Gameshow'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bullseye Gameshow',
                    'description'   => 'Bullseye Gameshow',
                    'content'       => 'Bullseye Gameshow'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bullseye Gameshow',
                    'description'   => 'Bullseye Gameshow',
                    'content'       => 'Bullseye Gameshow'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_burningDesire',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Burning Desire',
                    'description'   => 'Burning Desire',
                    'content'       => 'Burning Desire'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Burning Desire',
                    'description'   => 'Burning Desire',
                    'content'       => 'Burning Desire'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Burning Desire',
                    'description'   => 'Burning Desire',
                    'content'       => 'Burning Desire'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bushTelegraph',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bush Telegraph',
                    'description'   => 'Bush Telegraph',
                    'content'       => 'Bush Telegraph'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bush Telegraph',
                    'description'   => 'Bush Telegraph',
                    'content'       => 'Bush Telegraph'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bush Telegraph',
                    'description'   => 'Bush Telegraph',
                    'content'       => 'Bush Telegraph'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_bustTheBank',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bust the Bank',
                    'description'   => 'Bust the Bank',
                    'content'       => 'Bust the Bank'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bust the Bank',
                    'description'   => 'Bust the Bank',
                    'content'       => 'Bust the Bank'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bust the Bank',
                    'description'   => 'Bust the Bank',
                    'content'       => 'Bust the Bank'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_candyDreams',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Candy Dreams',
                    'description'   => 'Candy Dreams',
                    'content'       => 'Candy Dreams'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Candy Dreams',
                    'description'   => 'Candy Dreams',
                    'content'       => 'Candy Dreams'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Candy Dreams',
                    'description'   => 'Candy Dreams',
                    'content'       => 'Candy Dreams'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_carnaval',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Carnaval',
                    'description'   => 'Carnaval',
                    'content'       => 'Carnaval'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Carnaval',
                    'description'   => 'Carnaval',
                    'content'       => 'Carnaval'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Carnaval',
                    'description'   => 'Carnaval',
                    'content'       => 'Carnaval'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cashCrazy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cash Crazy',
                    'description'   => 'Cash Crazy',
                    'content'       => 'Cash Crazy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cash Crazy',
                    'description'   => 'Cash Crazy',
                    'content'       => 'Cash Crazy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cash Crazy',
                    'description'   => 'Cash Crazy',
                    'content'       => 'Cash Crazy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cashOfKingdoms',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cash of Kingdoms',
                    'description'   => 'Cash of Kingdoms',
                    'content'       => 'Cash of Kingdoms'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cash of Kingdoms',
                    'description'   => 'Cash of Kingdoms',
                    'content'       => 'Cash of Kingdoms'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cash of Kingdoms',
                    'description'   => 'Cash of Kingdoms',
                    'content'       => 'Cash of Kingdoms'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cashapillar',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cashapillar',
                    'description'   => 'Cashapillar',
                    'content'       => 'Cashapillar'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cashapillar',
                    'description'   => 'Cashapillar',
                    'content'       => 'Cashapillar'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cashapillar',
                    'description'   => 'Cashapillar',
                    'content'       => 'Cashapillar'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cashoccino',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'CashOccino',
                    'description'   => 'CashOccino',
                    'content'       => 'CashOccino'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'CashOccino',
                    'description'   => 'CashOccino',
                    'content'       => 'CashOccino'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'CashOccino',
                    'description'   => 'CashOccino',
                    'content'       => 'CashOccino'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cashville',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cashville',
                    'description'   => 'Cashville',
                    'content'       => 'Cashville'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cashville',
                    'description'   => 'Cashville',
                    'content'       => 'Cashville'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cashville',
                    'description'   => 'Cashville',
                    'content'       => 'Cashville'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_castleBuilder2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Castle Builder 2',
                    'description'   => 'Castle Builder 2',
                    'content'       => 'Castle Builder 2'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Castle Builder 2',
                    'description'   => 'Castle Builder 2',
                    'content'       => 'Castle Builder 2'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Castle Builder 2',
                    'description'   => 'Castle Builder 2',
                    'content'       => 'Castle Builder 2'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_centreCourt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Centre Court',
                    'description'   => 'Centre Court',
                    'content'       => 'Centre Court'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Centre Court',
                    'description'   => 'Centre Court',
                    'content'       => 'Centre Court'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Centre Court',
                    'description'   => 'Centre Court',
                    'content'       => 'Centre Court'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_classic243',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Classic 243',
                    'description'   => 'Classic 243',
                    'content'       => 'Classic 243'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Classic 243',
                    'description'   => 'Classic 243',
                    'content'       => 'Classic 243'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Classic 243',
                    'description'   => 'Classic 243',
                    'content'       => 'Classic 243'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_coolBuck5Reel',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cool Buck - 5 Reel',
                    'description'   => 'Cool Buck - 5 Reel',
                    'content'       => 'Cool Buck - 5 Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cool Buck - 5 Reel',
                    'description'   => 'Cool Buck - 5 Reel',
                    'content'       => 'Cool Buck - 5 Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cool Buck - 5 Reel',
                    'description'   => 'Cool Buck - 5 Reel',
                    'content'       => 'Cool Buck - 5 Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_coolWolf',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cool Wolf',
                    'description'   => 'Cool Wolf',
                    'content'       => 'Cool Wolf'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cool Wolf',
                    'description'   => 'Cool Wolf',
                    'content'       => 'Cool Wolf'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cool Wolf',
                    'description'   => 'Cool Wolf',
                    'content'       => 'Cool Wolf'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_couchPotato',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Couch Potato',
                    'description'   => 'Couch Potato',
                    'content'       => 'Couch Potato'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Couch Potato',
                    'description'   => 'Couch Potato',
                    'content'       => 'Couch Potato'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Couch Potato',
                    'description'   => 'Couch Potato',
                    'content'       => 'Couch Potato'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_crazyChameleons',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crazy Chameleons',
                    'description'   => 'Crazy Chameleons',
                    'content'       => 'Crazy Chameleons'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crazy Chameleons',
                    'description'   => 'Crazy Chameleons',
                    'content'       => 'Crazy Chameleons'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crazy Chameleons',
                    'description'   => 'Crazy Chameleons',
                    'content'       => 'Crazy Chameleons'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cricketStar',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cricket Star',
                    'description'   => 'Cricket Star',
                    'content'       => 'Cricket Star'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cricket Star',
                    'description'   => 'Cricket Star',
                    'content'       => 'Cricket Star'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cricket Star',
                    'description'   => 'Cricket Star',
                    'content'       => 'Cricket Star'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_CrystalRift',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crystal Rift',
                    'description'   => 'Crystal Rift',
                    'content'       => 'Crystal Rift'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crystal Rift',
                    'description'   => 'Crystal Rift',
                    'content'       => 'Crystal Rift'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crystal Rift',
                    'description'   => 'Crystal Rift',
                    'content'       => 'Crystal Rift'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_deckTheHalls',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Deck the Halls',
                    'description'   => 'Deck the Halls',
                    'content'       => 'Deck the Halls'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Deck the Halls',
                    'description'   => 'Deck the Halls',
                    'content'       => 'Deck the Halls'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Deck the Halls',
                    'description'   => 'Deck the Halls',
                    'content'       => 'Deck the Halls'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_decoDiamonds',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Deco Diamonds',
                    'description'   => 'Deco Diamonds',
                    'content'       => 'Deco Diamonds'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Deco Diamonds',
                    'description'   => 'Deco Diamonds',
                    'content'       => 'Deco Diamonds'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Deco Diamonds',
                    'description'   => 'Deco Diamonds',
                    'content'       => 'Deco Diamonds'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_diamondEmpire',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Diamond Empire',
                    'description'   => 'Diamond Empire',
                    'content'       => 'Diamond Empire'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Diamond Empire',
                    'description'   => 'Diamond Empire',
                    'content'       => 'Diamond Empire'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Diamond Empire',
                    'description'   => 'Diamond Empire',
                    'content'       => 'Diamond Empire'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dolphinCoast',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dolphin Coast',
                    'description'   => 'Dolphin Coast',
                    'content'       => 'Dolphin Coast'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dolphin Coast',
                    'description'   => 'Dolphin Coast',
                    'content'       => 'Dolphin Coast'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dolphin Coast',
                    'description'   => 'Dolphin Coast',
                    'content'       => 'Dolphin Coast'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dolphinQuest',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dolphin Quest',
                    'description'   => 'Dolphin Quest',
                    'content'       => 'Dolphin Quest'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dolphin Quest',
                    'description'   => 'Dolphin Quest',
                    'content'       => 'Dolphin Quest'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dolphin Quest',
                    'description'   => 'Dolphin Quest',
                    'content'       => 'Dolphin Quest'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_doubleWammy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Double Wammy',
                    'description'   => 'Double Wammy',
                    'content'       => 'Double Wammy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Double Wammy',
                    'description'   => 'Double Wammy',
                    'content'       => 'Double Wammy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Double Wammy',
                    'description'   => 'Double Wammy',
                    'content'       => 'Double Wammy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dragonDance',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Dance',
                    'description'   => 'Dragon Dance',
                    'content'       => 'Dragon Dance'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Dance',
                    'description'   => 'Dragon Dance',
                    'content'       => 'Dragon Dance'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Dance',
                    'description'   => 'Dragon Dance',
                    'content'       => 'Dragon Dance'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dragonShard',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Shard',
                    'description'   => 'Dragon Shard',
                    'content'       => 'Dragon Shard'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Shard',
                    'description'   => 'Dragon Shard',
                    'content'       => 'Dragon Shard'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Shard',
                    'description'   => 'Dragon Shard',
                    'content'       => 'Dragon Shard'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dragonz',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragonz',
                    'description'   => 'Dragonz',
                    'content'       => 'Dragonz'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragonz',
                    'description'   => 'Dragonz',
                    'content'       => 'Dragonz'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragonz',
                    'description'   => 'Dragonz',
                    'content'       => 'Dragonz'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dreamDate',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dream Date',
                    'description'   => 'Dream Date',
                    'content'       => 'Dream Date'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dream Date',
                    'description'   => 'Dream Date',
                    'content'       => 'Dream Date'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dream Date',
                    'description'   => 'Dream Date',
                    'content'       => 'Dream Date'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_eaglesWings',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Eagle\'s Wings',
                    'description'   => 'Eagle\'s Wings',
                    'content'       => 'Eagle\'s Wings'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Eagle\'s Wings',
                    'description'   => 'Eagle\'s Wings',
                    'content'       => 'Eagle\'s Wings'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Eagle\'s Wings',
                    'description'   => 'Eagle\'s Wings',
                    'content'       => 'Eagle\'s Wings'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_emotiCoins',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'EmotiCoins',
                    'description'   => 'EmotiCoins',
                    'content'       => 'EmotiCoins'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'EmotiCoins',
                    'description'   => 'EmotiCoins',
                    'content'       => 'EmotiCoins'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'EmotiCoins',
                    'description'   => 'EmotiCoins',
                    'content'       => 'EmotiCoins'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_emperorOfTheSea',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Emperor Of The Sea',
                    'description'   => 'Emperor Of The Sea',
                    'content'       => 'Emperor Of The Sea'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Emperor Of The Sea',
                    'description'   => 'Emperor Of The Sea',
                    'content'       => 'Emperor Of The Sea'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Emperor Of The Sea',
                    'description'   => 'Emperor Of The Sea',
                    'content'       => 'Emperor Of The Sea'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_exoticCats',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Exotic Cats',
                    'description'   => 'Exotic Cats',
                    'content'       => 'Exotic Cats'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Exotic Cats',
                    'description'   => 'Exotic Cats',
                    'content'       => 'Exotic Cats'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Exotic Cats',
                    'description'   => 'Exotic Cats',
                    'content'       => 'Exotic Cats'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_extremeSpeedDash',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Extreme Speed Dash',
                    'description'   => 'Extreme Speed Dash',
                    'content'       => 'Extreme Speed Dash'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Extreme Speed Dash',
                    'description'   => 'Extreme Speed Dash',
                    'content'       => 'Extreme Speed Dash'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Extreme Speed Dash',
                    'description'   => 'Extreme Speed Dash',
                    'content'       => 'Extreme Speed Dash'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_fishParty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fish Party',
                    'description'   => 'Fish Party',
                    'content'       => 'Fish Party'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fish Party',
                    'description'   => 'Fish Party',
                    'content'       => 'Fish Party'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fish Party',
                    'description'   => 'Fish Party',
                    'content'       => 'Fish Party'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_footballStar',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Football Star',
                    'description'   => 'Football Star',
                    'content'       => 'Football Star'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Football Star',
                    'description'   => 'Football Star',
                    'content'       => 'Football Star'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Football Star',
                    'description'   => 'Football Star',
                    'content'       => 'Football Star'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_forbiddenThrone',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Forbidden Throne',
                    'description'   => 'Forbidden Throne',
                    'content'       => 'Forbidden Throne'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Forbidden Throne',
                    'description'   => 'Forbidden Throne',
                    'content'       => 'Forbidden Throne'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Forbidden Throne',
                    'description'   => 'Forbidden Throne',
                    'content'       => 'Forbidden Throne'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_fortuneGirl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortune Girl',
                    'description'   => 'Fortune Girl',
                    'content'       => 'Fortune Girl'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortune Girl',
                    'description'   => 'Fortune Girl',
                    'content'       => 'Fortune Girl'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortune Girl',
                    'description'   => 'Fortune Girl',
                    'content'       => 'Fortune Girl'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_fortunium',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortunium',
                    'description'   => 'Fortunium',
                    'content'       => 'Fortunium'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortunium',
                    'description'   => 'Fortunium',
                    'content'       => 'Fortunium'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortunium',
                    'description'   => 'Fortunium',
                    'content'       => 'Fortunium'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_frozenDiamonds',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Frozen Diamonds',
                    'description'   => 'Frozen Diamonds',
                    'content'       => 'Frozen Diamonds'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Frozen Diamonds',
                    'description'   => 'Frozen Diamonds',
                    'content'       => 'Frozen Diamonds'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Frozen Diamonds',
                    'description'   => 'Frozen Diamonds',
                    'content'       => 'Frozen Diamonds'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_fruitVSCandy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fruit vs Candy',
                    'description'   => 'Fruit vs Candy',
                    'content'       => 'Fruit vs Candy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fruit vs Candy',
                    'description'   => 'Fruit vs Candy',
                    'content'       => 'Fruit vs Candy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fruit vs Candy',
                    'description'   => 'Fruit vs Candy',
                    'content'       => 'Fruit vs Candy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_giantRiches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Giant Riches',
                    'description'   => 'Giant Riches',
                    'content'       => 'Giant Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Giant Riches',
                    'description'   => 'Giant Riches',
                    'content'       => 'Giant Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Giant Riches',
                    'description'   => 'Giant Riches',
                    'content'       => 'Giant Riches'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_girlsWithGunsJungleHeat',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Girls With Guns - Jungle Heat',
                    'description'   => 'Girls With Guns - Jungle Heat',
                    'content'       => 'Girls With Guns - Jungle Heat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Girls With Guns - Jungle Heat',
                    'description'   => 'Girls With Guns - Jungle Heat',
                    'content'       => 'Girls With Guns - Jungle Heat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Girls With Guns - Jungle Heat',
                    'description'   => 'Girls With Guns - Jungle Heat',
                    'content'       => 'Girls With Guns - Jungle Heat'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_gnomeWood',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gnome Wood',
                    'description'   => 'Gnome Wood',
                    'content'       => 'Gnome Wood'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gnome Wood',
                    'description'   => 'Gnome Wood',
                    'content'       => 'Gnome Wood'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gnome Wood',
                    'description'   => 'Gnome Wood',
                    'content'       => 'Gnome Wood'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_goldFactory',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gold Factory',
                    'description'   => 'Gold Factory',
                    'content'       => 'Gold Factory'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gold Factory',
                    'description'   => 'Gold Factory',
                    'content'       => 'Gold Factory'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gold Factory',
                    'description'   => 'Gold Factory',
                    'content'       => 'Gold Factory'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_goldenEra',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Golden Era',
                    'description'   => 'Golden Era',
                    'content'       => 'Golden Era'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Golden Era',
                    'description'   => 'Golden Era',
                    'content'       => 'Golden Era'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Golden Era',
                    'description'   => 'Golden Era',
                    'content'       => 'Golden Era'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_goldenPrincess',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Golden Princess',
                    'description'   => 'Golden Princess',
                    'content'       => 'Golden Princess'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Golden Princess',
                    'description'   => 'Golden Princess',
                    'content'       => 'Golden Princess'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Golden Princess',
                    'description'   => 'Golden Princess',
                    'content'       => 'Golden Princess'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_gopherGold',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gopher Gold',
                    'description'   => 'Gopher Gold',
                    'content'       => 'Gopher Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gopher Gold',
                    'description'   => 'Gopher Gold',
                    'content'       => 'Gopher Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gopher Gold',
                    'description'   => 'Gopher Gold',
                    'content'       => 'Gopher Gold'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_halloween',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Halloween',
                    'description'   => 'Halloween',
                    'content'       => 'Halloween'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Halloween',
                    'description'   => 'Halloween',
                    'content'       => 'Halloween'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Halloween',
                    'description'   => 'Halloween',
                    'content'       => 'Halloween'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Halloweeniesies',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Halloweenies',
                    'description'   => 'Halloweenies',
                    'content'       => 'Halloweenies'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Halloweenies',
                    'description'   => 'Halloweenies',
                    'content'       => 'Halloweenies'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Halloweenies',
                    'description'   => 'Halloweenies',
                    'content'       => 'Halloweenies'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HappyHolidays',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Happy Holidays',
                    'description'   => 'Happy Holidays',
                    'content'       => 'Happy Holidays'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Happy Holidays',
                    'description'   => 'Happy Holidays',
                    'content'       => 'Happy Holidays'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Happy Holidays',
                    'description'   => 'Happy Holidays',
                    'content'       => 'Happy Holidays'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_harveys',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Harveys',
                    'description'   => 'Harveys',
                    'content'       => 'Harveys'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Harveys',
                    'description'   => 'Harveys',
                    'content'       => 'Harveys'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Harveys',
                    'description'   => 'Harveys',
                    'content'       => 'Harveys'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_hellboy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hellboy',
                    'description'   => 'Hellboy',
                    'content'       => 'Hellboy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hellboy',
                    'description'   => 'Hellboy',
                    'content'       => 'Hellboy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hellboy',
                    'description'   => 'Hellboy',
                    'content'       => 'Hellboy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_highSociety',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'High Society',
                    'description'   => 'High Society',
                    'content'       => 'High Society'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'High Society',
                    'description'   => 'High Society',
                    'content'       => 'High Society'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'High Society',
                    'description'   => 'High Society',
                    'content'       => 'High Society'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_highlander',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Highlander',
                    'description'   => 'Highlander',
                    'content'       => 'Highlander'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Highlander',
                    'description'   => 'Highlander',
                    'content'       => 'Highlander'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Highlander',
                    'description'   => 'Highlander',
                    'content'       => 'Highlander'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_hitman',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hitman',
                    'description'   => 'Hitman',
                    'content'       => 'Hitman'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hitman',
                    'description'   => 'Hitman',
                    'content'       => 'Hitman'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hitman',
                    'description'   => 'Hitman',
                    'content'       => 'Hitman'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_hollyJollyPenguins',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Holly Jolly Penguins',
                    'description'   => 'Holly Jolly Penguins',
                    'content'       => 'Holly Jolly Penguins'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Holly Jolly Penguins',
                    'description'   => 'Holly Jolly Penguins',
                    'content'       => 'Holly Jolly Penguins'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Holly Jolly Penguins',
                    'description'   => 'Holly Jolly Penguins',
                    'content'       => 'Holly Jolly Penguins'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HoundHotel',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hound Hotel',
                    'description'   => 'Hound Hotel',
                    'content'       => 'Hound Hotel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hound Hotel',
                    'description'   => 'Hound Hotel',
                    'content'       => 'Hound Hotel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hound Hotel',
                    'description'   => 'Hound Hotel',
                    'content'       => 'Hound Hotel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_huangdiTheYellowEmperor',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Huangdi - The Yellow Emperor',
                    'description'   => 'Huangdi - The Yellow Emperor',
                    'content'       => 'Huangdi - The Yellow Emperor'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Huangdi - The Yellow Emperor',
                    'description'   => 'Huangdi - The Yellow Emperor',
                    'content'       => 'Huangdi - The Yellow Emperor'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Huangdi - The Yellow Emperor',
                    'description'   => 'Huangdi - The Yellow Emperor',
                    'content'       => 'Huangdi - The Yellow Emperor'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_immortalRomance',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Immortal Romance',
                    'description'   => 'Immortal Romance',
                    'content'       => 'Immortal Romance'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Immortal Romance',
                    'description'   => 'Immortal Romance',
                    'content'       => 'Immortal Romance'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Immortal Romance',
                    'description'   => 'Immortal Romance',
                    'content'       => 'Immortal Romance'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_isis',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Isis',
                    'description'   => 'Isis',
                    'content'       => 'Isis'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Isis',
                    'description'   => 'Isis',
                    'content'       => 'Isis'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Isis',
                    'description'   => 'Isis',
                    'content'       => 'Isis'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jacksOrBetter',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jacks or Better',
                    'description'   => 'Jacks or Better',
                    'content'       => 'Jacks or Better'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jacks or Better',
                    'description'   => 'Jacks or Better',
                    'content'       => 'Jacks or Better'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jacks or Better',
                    'description'   => 'Jacks or Better',
                    'content'       => 'Jacks or Better'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jungleJimElDorado',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jungle Jim - El Dorado',
                    'description'   => 'Jungle Jim - El Dorado',
                    'content'       => 'Jungle Jim - El Dorado'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jungle Jim - El Dorado',
                    'description'   => 'Jungle Jim - El Dorado',
                    'content'       => 'Jungle Jim - El Dorado'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jungle Jim - El Dorado',
                    'description'   => 'Jungle Jim - El Dorado',
                    'content'       => 'Jungle Jim - El Dorado'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jurassicWorld',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jurassic World',
                    'description'   => 'Jurassic World',
                    'content'       => 'Jurassic World'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jurassic World',
                    'description'   => 'Jurassic World',
                    'content'       => 'Jurassic World'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jurassic World',
                    'description'   => 'Jurassic World',
                    'content'       => 'Jurassic World'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_karaokeParty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Karaoke Party',
                    'description'   => 'Karaoke Party',
                    'content'       => 'Karaoke Party'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Karaoke Party',
                    'description'   => 'Karaoke Party',
                    'content'       => 'Karaoke Party'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Karaoke Party',
                    'description'   => 'Karaoke Party',
                    'content'       => 'Karaoke Party'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_kathmandu',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Kathmandu',
                    'description'   => 'Kathmandu',
                    'content'       => 'Kathmandu'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Kathmandu',
                    'description'   => 'Kathmandu',
                    'content'       => 'Kathmandu'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Kathmandu',
                    'description'   => 'Kathmandu',
                    'content'       => 'Kathmandu'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_kingTusk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'King Tusk',
                    'description'   => 'King Tusk',
                    'content'       => 'King Tusk'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'King Tusk',
                    'description'   => 'King Tusk',
                    'content'       => 'King Tusk'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'King Tusk',
                    'description'   => 'King Tusk',
                    'content'       => 'King Tusk'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_kingsOfCash',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Kings Of Cash',
                    'description'   => 'Kings Of Cash',
                    'content'       => 'Kings Of Cash'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Kings Of Cash',
                    'description'   => 'Kings Of Cash',
                    'content'       => 'Kings Of Cash'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Kings Of Cash',
                    'description'   => 'Kings Of Cash',
                    'content'       => 'Kings Of Cash'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_KittyCabana',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Kitty Cabana',
                    'description'   => 'Kitty Cabana',
                    'content'       => 'Kitty Cabana'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Kitty Cabana',
                    'description'   => 'Kitty Cabana',
                    'content'       => 'Kitty Cabana'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Kitty Cabana',
                    'description'   => 'Kitty Cabana',
                    'content'       => 'Kitty Cabana'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ladiesNite',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ladies Nite',
                    'description'   => 'Ladies Nite',
                    'content'       => 'Ladies Nite'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ladies Nite',
                    'description'   => 'Ladies Nite',
                    'content'       => 'Ladies Nite'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ladies Nite',
                    'description'   => 'Ladies Nite',
                    'content'       => 'Ladies Nite'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ladyInRed',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lady In Red',
                    'description'   => 'Lady In Red',
                    'content'       => 'Lady In Red'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lady In Red',
                    'description'   => 'Lady In Red',
                    'content'       => 'Lady In Red'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lady In Red',
                    'description'   => 'Lady In Red',
                    'content'       => 'Lady In Red'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_laraCroftTemplesAndTombs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lara Croft: Temples and Tombs',
                    'description'   => 'Lara Croft: Temples and Tombs',
                    'content'       => 'Lara Croft: Temples and Tombs'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lara Croft: Temples and Tombs',
                    'description'   => 'Lara Croft: Temples and Tombs',
                    'content'       => 'Lara Croft: Temples and Tombs'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lara Croft: Temples and Tombs',
                    'description'   => 'Lara Croft: Temples and Tombs',
                    'content'       => 'Lara Croft: Temples and Tombs'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_lifeOfRiches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Life Of Riches',
                    'description'   => 'Life Of Riches',
                    'content'       => 'Life Of Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Life Of Riches',
                    'description'   => 'Life Of Riches',
                    'content'       => 'Life Of Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Life Of Riches',
                    'description'   => 'Life Of Riches',
                    'content'       => 'Life Of Riches'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_lionsPride',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lion\'s Pride',
                    'description'   => 'Lion\'s Pride',
                    'content'       => 'Lion\'s Pride'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lion\'s Pride',
                    'description'   => 'Lion\'s Pride',
                    'content'       => 'Lion\'s Pride'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lion\'s Pride',
                    'description'   => 'Lion\'s Pride',
                    'content'       => 'Lion\'s Pride'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_liquidGold',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Liquid Gold',
                    'description'   => 'Liquid Gold',
                    'content'       => 'Liquid Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Liquid Gold',
                    'description'   => 'Liquid Gold',
                    'content'       => 'Liquid Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Liquid Gold',
                    'description'   => 'Liquid Gold',
                    'content'       => 'Liquid Gold'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_loaded',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Loaded',
                    'description'   => 'Loaded',
                    'content'       => 'Loaded'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Loaded',
                    'description'   => 'Loaded',
                    'content'       => 'Loaded'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Loaded',
                    'description'   => 'Loaded',
                    'content'       => 'Loaded'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_lostVegas',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lost Vegas',
                    'description'   => 'Lost Vegas',
                    'content'       => 'Lost Vegas'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lost Vegas',
                    'description'   => 'Lost Vegas',
                    'content'       => 'Lost Vegas'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lost Vegas',
                    'description'   => 'Lost Vegas',
                    'content'       => 'Lost Vegas'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luchaLegends',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucha Legends',
                    'description'   => 'Lucha Legends',
                    'content'       => 'Lucha Legends'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucha Legends',
                    'description'   => 'Lucha Legends',
                    'content'       => 'Lucha Legends'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucha Legends',
                    'description'   => 'Lucha Legends',
                    'content'       => 'Lucha Legends'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luckyfirecracker',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Firecracker',
                    'description'   => 'Lucky Firecracker',
                    'content'       => 'Lucky Firecracker'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Firecracker',
                    'description'   => 'Lucky Firecracker',
                    'content'       => 'Lucky Firecracker'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Firecracker',
                    'description'   => 'Lucky Firecracker',
                    'content'       => 'Lucky Firecracker'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luckyKoi',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Koi',
                    'description'   => 'Lucky Koi',
                    'content'       => 'Lucky Koi'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Koi',
                    'description'   => 'Lucky Koi',
                    'content'       => 'Lucky Koi'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Koi',
                    'description'   => 'Lucky Koi',
                    'content'       => 'Lucky Koi'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luckyLeprechaun',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Leprechaun',
                    'description'   => 'Lucky Leprechaun',
                    'content'       => 'Lucky Leprechaun'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Leprechaun',
                    'description'   => 'Lucky Leprechaun',
                    'content'       => 'Lucky Leprechaun'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Leprechaun',
                    'description'   => 'Lucky Leprechaun',
                    'content'       => 'Lucky Leprechaun'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luckyLittleGods',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Little Gods',
                    'description'   => 'Lucky Little Gods',
                    'content'       => 'Lucky Little Gods'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Little Gods',
                    'description'   => 'Lucky Little Gods',
                    'content'       => 'Lucky Little Gods'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Little Gods',
                    'description'   => 'Lucky Little Gods',
                    'content'       => 'Lucky Little Gods'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luckyTwins',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Twins',
                    'description'   => 'Lucky Twins',
                    'content'       => 'Lucky Twins'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Twins',
                    'description'   => 'Lucky Twins',
                    'content'       => 'Lucky Twins'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Twins',
                    'description'   => 'Lucky Twins',
                    'content'       => 'Lucky Twins'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_luckyZodiac',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Zodiac',
                    'description'   => 'Lucky Zodiac',
                    'content'       => 'Lucky Zodiac'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Zodiac',
                    'description'   => 'Lucky Zodiac',
                    'content'       => 'Lucky Zodiac'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Zodiac',
                    'description'   => 'Lucky Zodiac',
                    'content'       => 'Lucky Zodiac'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_madHatters',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mad Hatters',
                    'description'   => 'Mad Hatters',
                    'content'       => 'Mad Hatters'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mad Hatters',
                    'description'   => 'Mad Hatters',
                    'content'       => 'Mad Hatters'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mad Hatters',
                    'description'   => 'Mad Hatters',
                    'content'       => 'Mad Hatters'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_mayanPrincess',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mayan Princess',
                    'description'   => 'Mayan Princess',
                    'content'       => 'Mayan Princess'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mayan Princess',
                    'description'   => 'Mayan Princess',
                    'content'       => 'Mayan Princess'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mayan Princess',
                    'description'   => 'Mayan Princess',
                    'content'       => 'Mayan Princess'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_megaMoneyMultiplier',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mega Money Multiplier',
                    'description'   => 'Mega Money Multiplier',
                    'content'       => 'Mega Money Multiplier'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mega Money Multiplier',
                    'description'   => 'Mega Money Multiplier',
                    'content'       => 'Mega Money Multiplier'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mega Money Multiplier',
                    'description'   => 'Mega Money Multiplier',
                    'content'       => 'Mega Money Multiplier'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_mermaidsMillions',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mermaids Millions',
                    'description'   => 'Mermaids Millions',
                    'content'       => 'Mermaids Millions'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mermaids Millions',
                    'description'   => 'Mermaids Millions',
                    'content'       => 'Mermaids Millions'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mermaids Millions',
                    'description'   => 'Mermaids Millions',
                    'content'       => 'Mermaids Millions'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_mobyDickOnlineSlot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Moby Dick',
                    'description'   => 'Moby Dick',
                    'content'       => 'Moby Dick'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Moby Dick',
                    'description'   => 'Moby Dick',
                    'content'       => 'Moby Dick'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Moby Dick',
                    'description'   => 'Moby Dick',
                    'content'       => 'Moby Dick'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_monsterWheels',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Monster Wheels',
                    'description'   => 'Monster Wheels',
                    'content'       => 'Monster Wheels'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Monster Wheels',
                    'description'   => 'Monster Wheels',
                    'content'       => 'Monster Wheels'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Monster Wheels',
                    'description'   => 'Monster Wheels',
                    'content'       => 'Monster Wheels'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_munchkins',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Munchkins',
                    'description'   => 'Munchkins',
                    'content'       => 'Munchkins'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Munchkins',
                    'description'   => 'Munchkins',
                    'content'       => 'Munchkins'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Munchkins',
                    'description'   => 'Munchkins',
                    'content'       => 'Munchkins'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_mysticDreams',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mystic Dreams',
                    'description'   => 'Mystic Dreams',
                    'content'       => 'Mystic Dreams'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mystic Dreams',
                    'description'   => 'Mystic Dreams',
                    'content'       => 'Mystic Dreams'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mystic Dreams',
                    'description'   => 'Mystic Dreams',
                    'content'       => 'Mystic Dreams'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_oinkCountryLove',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Oink Country Love',
                    'description'   => 'Oink Country Love',
                    'content'       => 'Oink Country Love'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Oink Country Love',
                    'description'   => 'Oink Country Love',
                    'content'       => 'Oink Country Love'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Oink Country Love',
                    'description'   => 'Oink Country Love',
                    'content'       => 'Oink Country Love'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ourDaysA',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Our Days',
                    'description'   => 'Our Days',
                    'content'       => 'Our Days'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Our Days',
                    'description'   => 'Our Days',
                    'content'       => 'Our Days'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Our Days',
                    'description'   => 'Our Days',
                    'content'       => 'Our Days'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_partyIsland',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Party Island',
                    'description'   => 'Party Island',
                    'content'       => 'Party Island'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Party Island',
                    'description'   => 'Party Island',
                    'content'       => 'Party Island'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Party Island',
                    'description'   => 'Party Island',
                    'content'       => 'Party Island'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_peekABoo5Reel',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Peek-a-Boo - 5 Reel',
                    'description'   => 'Peek-a-Boo - 5 Reel',
                    'content'       => 'Peek-a-Boo - 5 Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Peek-a-Boo - 5 Reel',
                    'description'   => 'Peek-a-Boo - 5 Reel',
                    'content'       => 'Peek-a-Boo - 5 Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Peek-a-Boo - 5 Reel',
                    'description'   => 'Peek-a-Boo - 5 Reel',
                    'content'       => 'Peek-a-Boo - 5 Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_pistoleras',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pistoleras',
                    'description'   => 'Pistoleras',
                    'content'       => 'Pistoleras'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pistoleras',
                    'description'   => 'Pistoleras',
                    'content'       => 'Pistoleras'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pistoleras',
                    'description'   => 'Pistoleras',
                    'content'       => 'Pistoleras'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_playboy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Playboy',
                    'description'   => 'Playboy',
                    'content'       => 'Playboy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Playboy',
                    'description'   => 'Playboy',
                    'content'       => 'Playboy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Playboy',
                    'description'   => 'Playboy',
                    'content'       => 'Playboy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_playboyGold',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Playboy Gold',
                    'description'   => 'Playboy Gold',
                    'content'       => 'Playboy Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Playboy Gold',
                    'description'   => 'Playboy Gold',
                    'content'       => 'Playboy Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Playboy Gold',
                    'description'   => 'Playboy Gold',
                    'content'       => 'Playboy Gold'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_pollenParty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pollen Party',
                    'description'   => 'Pollen Party',
                    'content'       => 'Pollen Party'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pollen Party',
                    'description'   => 'Pollen Party',
                    'content'       => 'Pollen Party'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pollen Party',
                    'description'   => 'Pollen Party',
                    'content'       => 'Pollen Party'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_prettyKitty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pretty Kitty',
                    'description'   => 'Pretty Kitty',
                    'content'       => 'Pretty Kitty'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pretty Kitty',
                    'description'   => 'Pretty Kitty',
                    'content'       => 'Pretty Kitty'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pretty Kitty',
                    'description'   => 'Pretty Kitty',
                    'content'       => 'Pretty Kitty'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_purePlatinum',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pure Platinum',
                    'description'   => 'Pure Platinum',
                    'content'       => 'Pure Platinum'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pure Platinum',
                    'description'   => 'Pure Platinum',
                    'content'       => 'Pure Platinum'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pure Platinum',
                    'description'   => 'Pure Platinum',
                    'content'       => 'Pure Platinum'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_reelGems',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Gems',
                    'description'   => 'Reel Gems',
                    'content'       => 'Reel Gems'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Gems',
                    'description'   => 'Reel Gems',
                    'content'       => 'Reel Gems'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Gems',
                    'description'   => 'Reel Gems',
                    'content'       => 'Reel Gems'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_reelSpinner',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Spinner',
                    'description'   => 'Reel Spinner',
                    'content'       => 'Reel Spinner'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Spinner',
                    'description'   => 'Reel Spinner',
                    'content'       => 'Reel Spinner'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Spinner',
                    'description'   => 'Reel Spinner',
                    'content'       => 'Reel Spinner'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_reelStrike',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Strike',
                    'description'   => 'Reel Strike',
                    'content'       => 'Reel Strike'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Strike',
                    'description'   => 'Reel Strike',
                    'content'       => 'Reel Strike'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Strike',
                    'description'   => 'Reel Strike',
                    'content'       => 'Reel Strike'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ReelTalent',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Talent',
                    'description'   => 'Reel Talent',
                    'content'       => 'Reel Talent'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Talent',
                    'description'   => 'Reel Talent',
                    'content'       => 'Reel Talent'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Talent',
                    'description'   => 'Reel Talent',
                    'content'       => 'Reel Talent'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_reelThunder',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Thunder',
                    'description'   => 'Reel Thunder',
                    'content'       => 'Reel Thunder'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Thunder',
                    'description'   => 'Reel Thunder',
                    'content'       => 'Reel Thunder'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Thunder',
                    'description'   => 'Reel Thunder',
                    'content'       => 'Reel Thunder'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_relicReels',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Relic Seekers',
                    'description'   => 'Relic Seekers',
                    'content'       => 'Relic Seekers'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Relic Seekers',
                    'description'   => 'Relic Seekers',
                    'content'       => 'Relic Seekers'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Relic Seekers',
                    'description'   => 'Relic Seekers',
                    'content'       => 'Relic Seekers'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_retroReels',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Retro Reels',
                    'description'   => 'Retro Reels',
                    'content'       => 'Retro Reels'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Retro Reels',
                    'description'   => 'Retro Reels',
                    'content'       => 'Retro Reels'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Retro Reels',
                    'description'   => 'Retro Reels',
                    'content'       => 'Retro Reels'
                ],
            ],
            'devices'       => [1, 2],
        ],        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_retroReelsDiamondGlitz',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Retro Reels - Diamond Glitz',
                    'description'   => 'Retro Reels - Diamond Glitz',
                    'content'       => 'Retro Reels - Diamond Glitz'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Retro Reels - Diamond Glitz',
                    'description'   => 'Retro Reels - Diamond Glitz',
                    'content'       => 'Retro Reels - Diamond Glitz'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Retro Reels - Diamond Glitz',
                    'description'   => 'Retro Reels - Diamond Glitz',
                    'content'       => 'Retro Reels - Diamond Glitz'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_retroReelsExtremeHeat',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Retro Reels - Extreme Heat',
                    'description'   => 'Retro Reels - Extreme Heat',
                    'content'       => 'Retro Reels - Extreme Heat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Retro Reels - Extreme Heat',
                    'description'   => 'Retro Reels - Extreme Heat',
                    'content'       => 'Retro Reels - Extreme Heat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Retro Reels - Extreme Heat',
                    'description'   => 'Retro Reels - Extreme Heat',
                    'content'       => 'Retro Reels - Extreme Heat'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_rhymingReelsGeorgiePorgie',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rhyming Reels Georgie Porgie',
                    'description'   => 'Rhyming Reels Georgie Porgie',
                    'content'       => 'Rhyming Reels Georgie Porgie'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rhyming Reels Georgie Porgie',
                    'description'   => 'Rhyming Reels Georgie Porgie',
                    'content'       => 'Rhyming Reels Georgie Porgie'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rhyming Reels Georgie Porgie',
                    'description'   => 'Rhyming Reels Georgie Porgie',
                    'content'       => 'Rhyming Reels Georgie Porgie'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_rhymingReelsHeartsAndTarts',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rhyming Reels Hearts And Tarts',
                    'description'   => 'Rhyming Reels Hearts And Tarts',
                    'content'       => 'Rhyming Reels Hearts And Tarts'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rhyming Reels Hearts And Tarts',
                    'description'   => 'Rhyming Reels Hearts And Tarts',
                    'content'       => 'Rhyming Reels Hearts And Tarts'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rhyming Reels Hearts And Tarts',
                    'description'   => 'Rhyming Reels Hearts And Tarts',
                    'content'       => 'Rhyming Reels Hearts And Tarts'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_rivieraRiches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Riviera Riches',
                    'description'   => 'Riviera Riches',
                    'content'       => 'Riviera Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Riviera Riches',
                    'description'   => 'Riviera Riches',
                    'content'       => 'Riviera Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Riviera Riches',
                    'description'   => 'Riviera Riches',
                    'content'       => 'Riviera Riches'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_robinOfSherwoodOnlineSlot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Robin of Sherwood',
                    'description'   => 'Robin of Sherwood',
                    'content'       => 'Robin of Sherwood'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Robin of Sherwood',
                    'description'   => 'Robin of Sherwood',
                    'content'       => 'Robin of Sherwood'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Robin of Sherwood',
                    'description'   => 'Robin of Sherwood',
                    'content'       => 'Robin of Sherwood'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_romanovRiches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Romanov Riches',
                    'description'   => 'Romanov Riches',
                    'content'       => 'Romanov Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Romanov Riches',
                    'description'   => 'Romanov Riches',
                    'content'       => 'Romanov Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Romanov Riches',
                    'description'   => 'Romanov Riches',
                    'content'       => 'Romanov Riches'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_rugbyStar',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rugby Star',
                    'description'   => 'Rugby Star',
                    'content'       => 'Rugby Star'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rugby Star',
                    'description'   => 'Rugby Star',
                    'content'       => 'Rugby Star'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rugby Star',
                    'description'   => 'Rugby Star',
                    'content'       => 'Rugby Star'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_santaPaws',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Santa Paws',
                    'description'   => 'Santa Paws',
                    'content'       => 'Santa Paws'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Santa Paws',
                    'description'   => 'Santa Paws',
                    'content'       => 'Santa Paws'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Santa Paws',
                    'description'   => 'Santa Paws',
                    'content'       => 'Santa Paws'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_santasWildRide',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Santa\'s Wild Ride',
                    'description'   => 'Santa\'s Wild Ride',
                    'content'       => 'Santa\'s Wild Ride'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Santa\'s Wild Ride',
                    'description'   => 'Santa\'s Wild Ride',
                    'content'       => 'Santa\'s Wild Ride'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Santa\'s Wild Ride',
                    'description'   => 'Santa\'s Wild Ride',
                    'content'       => 'Santa\'s Wild Ride'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_scrooge',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Scrooge',
                    'description'   => 'Scrooge',
                    'content'       => 'Scrooge'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Scrooge',
                    'description'   => 'Scrooge',
                    'content'       => 'Scrooge'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Scrooge',
                    'description'   => 'Scrooge',
                    'content'       => 'Scrooge'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_secretAdmirer',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Secret Admirer',
                    'description'   => 'Secret Admirer',
                    'content'       => 'Secret Admirer'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Secret Admirer',
                    'description'   => 'Secret Admirer',
                    'content'       => 'Secret Admirer'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Secret Admirer',
                    'description'   => 'Secret Admirer',
                    'content'       => 'Secret Admirer'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_secretRomance',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Secret Romance',
                    'description'   => 'Secret Romance',
                    'content'       => 'Secret Romance'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Secret Romance',
                    'description'   => 'Secret Romance',
                    'content'       => 'Secret Romance'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Secret Romance',
                    'description'   => 'Secret Romance',
                    'content'       => 'Secret Romance'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_shanghaiBeauty',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Shanghai Beauty',
                    'description'   => 'Shanghai Beauty',
                    'content'       => 'Shanghai Beauty'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Shanghai Beauty',
                    'description'   => 'Shanghai Beauty',
                    'content'       => 'Shanghai Beauty'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Shanghai Beauty',
                    'description'   => 'Shanghai Beauty',
                    'content'       => 'Shanghai Beauty'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_shogunofTime',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Shogun of Time',
                    'description'   => 'Shogun of Time',
                    'content'       => 'Shogun of Time'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Shogun of Time',
                    'description'   => 'Shogun of Time',
                    'content'       => 'Shogun of Time'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Shogun of Time',
                    'description'   => 'Shogun of Time',
                    'content'       => 'Shogun of Time'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_shoot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Shoot!',
                    'description'   => 'Shoot!',
                    'content'       => 'Shoot!'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Shoot!',
                    'description'   => 'Shoot!',
                    'content'       => 'Shoot!'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Shoot!',
                    'description'   => 'Shoot!',
                    'content'       => 'Shoot!'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_showdownSaloon',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Showdown Saloon',
                    'description'   => 'Showdown Saloon',
                    'content'       => 'Showdown Saloon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Showdown Saloon',
                    'description'   => 'Showdown Saloon',
                    'content'       => 'Showdown Saloon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Showdown Saloon',
                    'description'   => 'Showdown Saloon',
                    'content'       => 'Showdown Saloon'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_silverFang',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Silver Fang',
                    'description'   => 'Silver Fang',
                    'content'       => 'Silver Fang'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Silver Fang',
                    'description'   => 'Silver Fang',
                    'content'       => 'Silver Fang'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Silver Fang',
                    'description'   => 'Silver Fang',
                    'content'       => 'Silver Fang'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_silverLioness4x',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Silver Lioness 4x',
                    'description'   => 'Silver Lioness 4x',
                    'content'       => 'Silver Lioness 4x'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Silver Lioness 4x',
                    'description'   => 'Silver Lioness 4x',
                    'content'       => 'Silver Lioness 4x'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Silver Lioness 4x',
                    'description'   => 'Silver Lioness 4x',
                    'content'       => 'Silver Lioness 4x'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_sixAcrobats',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Six Acrobats',
                    'description'   => 'Six Acrobats',
                    'content'       => 'Six Acrobats'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Six Acrobats',
                    'description'   => 'Six Acrobats',
                    'content'       => 'Six Acrobats'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Six Acrobats',
                    'description'   => 'Six Acrobats',
                    'content'       => 'Six Acrobats'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_soManyMonsters',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'So Many Monsters',
                    'description'   => 'So Many Monsters',
                    'content'       => 'So Many Monsters'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'So Many Monsters',
                    'description'   => 'So Many Monsters',
                    'content'       => 'So Many Monsters'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'So Many Monsters',
                    'description'   => 'So Many Monsters',
                    'content'       => 'So Many Monsters'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_soMuchCandy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'So Much Candy',
                    'description'   => 'So Much Candy',
                    'content'       => 'So Much Candy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'So Much Candy',
                    'description'   => 'So Much Candy',
                    'content'       => 'So Much Candy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'So Much Candy',
                    'description'   => 'So Much Candy',
                    'content'       => 'So Much Candy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_soMuchSushi',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'So Much Sushi',
                    'description'   => 'So Much Sushi',
                    'content'       => 'So Much Sushi'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'So Much Sushi',
                    'description'   => 'So Much Sushi',
                    'content'       => 'So Much Sushi'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'So Much Sushi',
                    'description'   => 'So Much Sushi',
                    'content'       => 'So Much Sushi'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_springBreak',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Spring Break',
                    'description'   => 'Spring Break',
                    'content'       => 'Spring Break'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Spring Break',
                    'description'   => 'Spring Break',
                    'content'       => 'Spring Break'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Spring Break',
                    'description'   => 'Spring Break',
                    'content'       => 'Spring Break'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_stardust',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Stardust',
                    'description'   => 'Stardust',
                    'content'       => 'Stardust'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Stardust',
                    'description'   => 'Stardust',
                    'content'       => 'Stardust'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Stardust',
                    'description'   => 'Stardust',
                    'content'       => 'Stardust'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_starlightKiss',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Starlight Kiss',
                    'description'   => 'Starlight Kiss',
                    'content'       => 'Starlight Kiss'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Starlight Kiss',
                    'description'   => 'Starlight Kiss',
                    'content'       => 'Starlight Kiss'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Starlight Kiss',
                    'description'   => 'Starlight Kiss',
                    'content'       => 'Starlight Kiss'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_stashOfTheTitans',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Stash of the Titans',
                    'description'   => 'Stash of the Titans',
                    'content'       => 'Stash of the Titans'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Stash of the Titans',
                    'description'   => 'Stash of the Titans',
                    'content'       => 'Stash of the Titans'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Stash of the Titans',
                    'description'   => 'Stash of the Titans',
                    'content'       => 'Stash of the Titans'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_sterlingSilver',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sterling Silver',
                    'description'   => 'Sterling Silver',
                    'content'       => 'Sterling Silver'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sterling Silver',
                    'description'   => 'Sterling Silver',
                    'content'       => 'Sterling Silver'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sterling Silver',
                    'description'   => 'Sterling Silver',
                    'content'       => 'Sterling Silver'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_sugarParade',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sugar Parade',
                    'description'   => 'Sugar Parade',
                    'content'       => 'Sugar Parade'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sugar Parade',
                    'description'   => 'Sugar Parade',
                    'content'       => 'Sugar Parade'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sugar Parade',
                    'description'   => 'Sugar Parade',
                    'content'       => 'Sugar Parade'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_summerHoliday',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Summer Holiday',
                    'description'   => 'Summer Holiday',
                    'content'       => 'Summer Holiday'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Summer Holiday',
                    'description'   => 'Summer Holiday',
                    'content'       => 'Summer Holiday'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Summer Holiday',
                    'description'   => 'Summer Holiday',
                    'content'       => 'Summer Holiday'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_summertime',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Summertime',
                    'description'   => 'Summertime',
                    'content'       => 'Summertime'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Summertime',
                    'description'   => 'Summertime',
                    'content'       => 'Summertime'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Summertime',
                    'description'   => 'Summertime',
                    'content'       => 'Summertime'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_sunQuest',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sun Quest',
                    'description'   => 'Sun Quest',
                    'content'       => 'Sun Quest'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sun Quest',
                    'description'   => 'Sun Quest',
                    'content'       => 'Sun Quest'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sun Quest',
                    'description'   => 'Sun Quest',
                    'content'       => 'Sun Quest'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_sunTide',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'SunTide',
                    'description'   => 'SunTide',
                    'content'       => 'SunTide'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'SunTide',
                    'description'   => 'SunTide',
                    'content'       => 'SunTide'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'SunTide',
                    'description'   => 'SunTide',
                    'content'       => 'SunTide'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_supeItUp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Supe It Up',
                    'description'   => 'Supe It Up',
                    'content'       => 'Supe It Up'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Supe It Up',
                    'description'   => 'Supe It Up',
                    'content'       => 'Supe It Up'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Supe It Up',
                    'description'   => 'Supe It Up',
                    'content'       => 'Supe It Up'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_sureWin',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sure Win',
                    'description'   => 'Sure Win',
                    'content'       => 'Sure Win'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sure Win',
                    'description'   => 'Sure Win',
                    'content'       => 'Sure Win'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sure Win',
                    'description'   => 'Sure Win',
                    'content'       => 'Sure Win'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_tallyHo',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tally Ho',
                    'description'   => 'Tally Ho',
                    'content'       => 'Tally Ho'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tally Ho',
                    'description'   => 'Tally Ho',
                    'content'       => 'Tally Ho'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tally Ho',
                    'description'   => 'Tally Ho',
                    'content'       => 'Tally Ho'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_tarzan',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tarzan',
                    'description'   => 'Tarzan',
                    'content'       => 'Tarzan'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tarzan',
                    'description'   => 'Tarzan',
                    'content'       => 'Tarzan'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tarzan',
                    'description'   => 'Tarzan',
                    'content'       => 'Tarzan'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_tastyStreet',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tasty Street',
                    'description'   => 'Tasty Street',
                    'content'       => 'Tasty Street'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tasty Street',
                    'description'   => 'Tasty Street',
                    'content'       => 'Tasty Street'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tasty Street',
                    'description'   => 'Tasty Street',
                    'content'       => 'Tasty Street'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_theFinerReelsOfLife',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Finer Reels of Life',
                    'description'   => 'The Finer Reels of Life',
                    'content'       => 'The Finer Reels of Life'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Finer Reels of Life',
                    'description'   => 'The Finer Reels of Life',
                    'content'       => 'The Finer Reels of Life'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Finer Reels of Life',
                    'description'   => 'The Finer Reels of Life',
                    'content'       => 'The Finer Reels of Life'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_theGrandJourney',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Grand Journey',
                    'description'   => 'The Grand Journey',
                    'content'       => 'The Grand Journey'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Grand Journey',
                    'description'   => 'The Grand Journey',
                    'content'       => 'The Grand Journey'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Grand Journey',
                    'description'   => 'The Grand Journey',
                    'content'       => 'The Grand Journey'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_theGreatAlbini',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Great Albini',
                    'description'   => 'The Great Albini',
                    'content'       => 'The Great Albini'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Great Albini',
                    'description'   => 'The Great Albini',
                    'content'       => 'The Great Albini'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Great Albini',
                    'description'   => 'The Great Albini',
                    'content'       => 'The Great Albini'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_theHeatIsOn',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Heat Is On',
                    'description'   => 'The Heat Is On',
                    'content'       => 'The Heat Is On'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Heat Is On',
                    'description'   => 'The Heat Is On',
                    'content'       => 'The Heat Is On'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Heat Is On',
                    'description'   => 'The Heat Is On',
                    'content'       => 'The Heat Is On'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_thePhantomOfTheOpera',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Phantom of the Opera',
                    'description'   => 'The Phantom of the Opera',
                    'content'       => 'The Phantom of the Opera'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Phantom of the Opera',
                    'description'   => 'The Phantom of the Opera',
                    'content'       => 'The Phantom of the Opera'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Phantom of the Opera',
                    'description'   => 'The Phantom of the Opera',
                    'content'       => 'The Phantom of the Opera'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_theRatPack',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Rat Pack',
                    'description'   => 'The Rat Pack',
                    'content'       => 'The Rat Pack'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Rat Pack',
                    'description'   => 'The Rat Pack',
                    'content'       => 'The Rat Pack'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Rat Pack',
                    'description'   => 'The Rat Pack',
                    'content'       => 'The Rat Pack'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_theTwistedCircus',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Twisted Circus',
                    'description'   => 'The Twisted Circus',
                    'content'       => 'The Twisted Circus'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Twisted Circus',
                    'description'   => 'The Twisted Circus',
                    'content'       => 'The Twisted Circus'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Twisted Circus',
                    'description'   => 'The Twisted Circus',
                    'content'       => 'The Twisted Circus'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_thunderstruck',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Thunderstruck',
                    'description'   => 'Thunderstruck',
                    'content'       => 'Thunderstruck'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Thunderstruck',
                    'description'   => 'Thunderstruck',
                    'content'       => 'Thunderstruck'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Thunderstruck',
                    'description'   => 'Thunderstruck',
                    'content'       => 'Thunderstruck'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ThunderStruck2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'ThunderStruck II',
                    'description'   => 'ThunderStruck II',
                    'content'       => 'ThunderStruck II'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'ThunderStruck II',
                    'description'   => 'ThunderStruck II',
                    'content'       => 'ThunderStruck II'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'ThunderStruck II',
                    'description'   => 'ThunderStruck II',
                    'content'       => 'ThunderStruck II'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_tigersEye',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tiger\'s Eye',
                    'description'   => 'Tiger\'s Eye',
                    'content'       => 'Tiger\'s Eye'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tiger\'s Eye',
                    'description'   => 'Tiger\'s Eye',
                    'content'       => 'Tiger\'s Eye'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tiger\'s Eye',
                    'description'   => 'Tiger\'s Eye',
                    'content'       => 'Tiger\'s Eye'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_titansOfTheSunHyperion',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Titans of the Sun - Hyperion',
                    'description'   => 'Titans of the Sun - Hyperion',
                    'content'       => 'Titans of the Sun - Hyperion'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Titans of the Sun - Hyperion',
                    'description'   => 'Titans of the Sun - Hyperion',
                    'content'       => 'Titans of the Sun - Hyperion'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Titans of the Sun - Hyperion',
                    'description'   => 'Titans of the Sun - Hyperion',
                    'content'       => 'Titans of the Sun - Hyperion'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_titansOfTheSunTheia',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Titans of the Sun - Theia',
                    'description'   => 'Titans of the Sun - Theia',
                    'content'       => 'Titans of the Sun - Theia'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Titans of the Sun - Theia',
                    'description'   => 'Titans of the Sun - Theia',
                    'content'       => 'Titans of the Sun - Theia'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Titans of the Sun - Theia',
                    'description'   => 'Titans of the Sun - Theia',
                    'content'       => 'Titans of the Sun - Theia'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_tombRaider',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tomb Raider',
                    'description'   => 'Tomb Raider',
                    'content'       => 'Tomb Raider'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tomb Raider',
                    'description'   => 'Tomb Raider',
                    'content'       => 'Tomb Raider'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tomb Raider',
                    'description'   => 'Tomb Raider',
                    'content'       => 'Tomb Raider'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyTombRaiderII',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tomb Raider Secret of the Sword',
                    'description'   => 'Tomb Raider Secret of the Sword',
                    'content'       => 'Tomb Raider Secret of the Sword'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tomb Raider Secret of the Sword',
                    'description'   => 'Tomb Raider Secret of the Sword',
                    'content'       => 'Tomb Raider Secret of the Sword'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tomb Raider Secret of the Sword',
                    'description'   => 'Tomb Raider Secret of the Sword',
                    'content'       => 'Tomb Raider Secret of the Sword'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_treasurePalace',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Treasure Palace',
                    'description'   => 'Treasure Palace',
                    'content'       => 'Treasure Palace'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Treasure Palace',
                    'description'   => 'Treasure Palace',
                    'content'       => 'Treasure Palace'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Treasure Palace',
                    'description'   => 'Treasure Palace',
                    'content'       => 'Treasure Palace'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_untamedGiantPanda',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Giant Panda',
                    'description'   => 'Untamed - Giant Panda',
                    'content'       => 'Untamed - Giant Panda'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Giant Panda',
                    'description'   => 'Untamed - Giant Panda',
                    'content'       => 'Untamed - Giant Panda'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Giant Panda',
                    'description'   => 'Untamed - Giant Panda',
                    'content'       => 'Untamed - Giant Panda'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_villagePeople',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Village People Macho Moves',
                    'description'   => 'Village People Macho Moves',
                    'content'       => 'Village People Macho Moves'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Village People Macho Moves',
                    'description'   => 'Village People Macho Moves',
                    'content'       => 'Village People Macho Moves'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Village People Macho Moves',
                    'description'   => 'Village People Macho Moves',
                    'content'       => 'Village People Macho Moves'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_vinylCountdown',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Vinyl Countdown',
                    'description'   => 'Vinyl Countdown',
                    'content'       => 'Vinyl Countdown'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Vinyl Countdown',
                    'description'   => 'Vinyl Countdown',
                    'content'       => 'Vinyl Countdown'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Vinyl Countdown',
                    'description'   => 'Vinyl Countdown',
                    'content'       => 'Vinyl Countdown'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_voila',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Voila!',
                    'description'   => 'Voila!',
                    'content'       => 'Voila!'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Voila!',
                    'description'   => 'Voila!',
                    'content'       => 'Voila!'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Voila!',
                    'description'   => 'Voila!',
                    'content'       => 'Voila!'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_wackyPanda',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wacky Panda',
                    'description'   => 'Wacky Panda',
                    'content'       => 'Wacky Panda'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wacky Panda',
                    'description'   => 'Wacky Panda',
                    'content'       => 'Wacky Panda'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wacky Panda',
                    'description'   => 'Wacky Panda',
                    'content'       => 'Wacky Panda'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_whatAHoot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'What A Hoot',
                    'description'   => 'What A Hoot',
                    'content'       => 'What A Hoot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'What A Hoot',
                    'description'   => 'What A Hoot',
                    'content'       => 'What A Hoot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'What A Hoot',
                    'description'   => 'What A Hoot',
                    'content'       => 'What A Hoot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_wickedTalesDarkRed',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wicked Tales: Dark Red',
                    'description'   => 'Wicked Tales: Dark Red',
                    'content'       => 'Wicked Tales: Dark Red'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wicked Tales: Dark Red',
                    'description'   => 'Wicked Tales: Dark Red',
                    'content'       => 'Wicked Tales: Dark Red'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wicked Tales: Dark Red',
                    'description'   => 'Wicked Tales: Dark Red',
                    'content'       => 'Wicked Tales: Dark Red'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_wildOrient',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wild Orient',
                    'description'   => 'Wild Orient',
                    'content'       => 'Wild Orient'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wild Orient',
                    'description'   => 'Wild Orient',
                    'content'       => 'Wild Orient'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wild Orient',
                    'description'   => 'Wild Orient',
                    'content'       => 'Wild Orient'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_wildScarabs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wild Scarabs',
                    'description'   => 'Wild Scarabs',
                    'content'       => 'Wild Scarabs'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wild Scarabs',
                    'description'   => 'Wild Scarabs',
                    'content'       => 'Wild Scarabs'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wild Scarabs',
                    'description'   => 'Wild Scarabs',
                    'content'       => 'Wild Scarabs'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_winSumDimSum',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Win Sum Dim Sum',
                    'description'   => 'Win Sum Dim Sum',
                    'content'       => 'Win Sum Dim Sum'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Win Sum Dim Sum',
                    'description'   => 'Win Sum Dim Sum',
                    'content'       => 'Win Sum Dim Sum'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Win Sum Dim Sum',
                    'description'   => 'Win Sum Dim Sum',
                    'content'       => 'Win Sum Dim Sum'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_zombieHoard',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Zombie Hoard',
                    'description'   => 'Zombie Hoard',
                    'content'       => 'Zombie Hoard'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Zombie Hoard',
                    'description'   => 'Zombie Hoard',
                    'content'       => 'Zombie Hoard'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Zombie Hoard',
                    'description'   => 'Zombie Hoard',
                    'content'       => 'Zombie Hoard'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyThousandIslands',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => '1000 Islands',
                    'description'   => '1000 Islands',
                    'content'       => '1000 Islands'
                ],
                [
                    'language'      => 'th',
                    'name'          => '1000 Islands',
                    'description'   => '1000 Islands',
                    'content'       => '1000 Islands'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => '1000 Islands',
                    'description'   => '1000 Islands',
                    'content'       => '1000 Islands'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_3empires',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => '3 Empires',
                    'description'   => '3 Empires',
                    'content'       => '3 Empires'
                ],
                [
                    'language'      => 'th',
                    'name'          => '3 Empires',
                    'description'   => '3 Empires',
                    'content'       => '3 Empires'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => '3 Empires',
                    'description'   => '3 Empires',
                    'content'       => '3 Empires'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_5ReelDriveV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => '5 Reel Drive V90',
                    'description'   => '5 Reel Drive V90',
                    'content'       => '5 Reel Drive V90'
                ],
                [
                    'language'      => 'th',
                    'name'          => '5 Reel Drive V90',
                    'description'   => '5 Reel Drive V90',
                    'content'       => '5 Reel Drive V90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => '5 Reel Drive V90',
                    'description'   => '5 Reel Drive V90',
                    'content'       => '5 Reel Drive V90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_AlaskanFishingV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Alaskan Fishing v90',
                    'description'   => 'Alaskan Fishing v90',
                    'content'       => 'Alaskan Fishing v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Alaskan Fishing v90',
                    'description'   => 'Alaskan Fishing v90',
                    'content'       => 'Alaskan Fishing v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Alaskan Fishing v90',
                    'description'   => 'Alaskan Fishing v90',
                    'content'       => 'Alaskan Fishing v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ArcticAgents',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Arctic Agents',
                    'description'   => 'Arctic Agents',
                    'content'       => 'Arctic Agents'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Arctic Agents',
                    'description'   => 'Arctic Agents',
                    'content'       => 'Arctic Agents'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Arctic Agents',
                    'description'   => 'Arctic Agents',
                    'content'       => 'Arctic Agents'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ArcticFortune',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Arctic Fortune',
                    'description'   => 'Arctic Fortune',
                    'content'       => 'Arctic Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Arctic Fortune',
                    'description'   => 'Arctic Fortune',
                    'content'       => 'Arctic Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Arctic Fortune',
                    'description'   => 'Arctic Fortune',
                    'content'       => 'Arctic Fortune'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyAstronomical',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Astronomical',
                    'description'   => 'Astronomical',
                    'content'       => 'Astronomical'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Astronomical',
                    'description'   => 'Astronomical',
                    'content'       => 'Astronomical'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Astronomical',
                    'description'   => 'Astronomical',
                    'content'       => 'Astronomical'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Avalon2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Avalon II',
                    'description'   => 'Avalon II',
                    'content'       => 'Avalon II'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Avalon II',
                    'description'   => 'Avalon II',
                    'content'       => 'Avalon II'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Avalon II',
                    'description'   => 'Avalon II',
                    'content'       => 'Avalon II'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_AvalonV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Avalon v90',
                    'description'   => 'Avalon v90',
                    'content'       => 'Avalon v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Avalon v90',
                    'description'   => 'Avalon v90',
                    'content'       => 'Avalon v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Avalon v90',
                    'description'   => 'Avalon v90',
                    'content'       => 'Avalon v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBarBarBlackSheep',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bar Bar Black Sheep',
                    'description'   => 'Bar Bar Black Sheep',
                    'content'       => 'Bar Bar Black Sheep'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bar Bar Black Sheep',
                    'description'   => 'Bar Bar Black Sheep',
                    'content'       => 'Bar Bar Black Sheep'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bar Bar Black Sheep',
                    'description'   => 'Bar Bar Black Sheep',
                    'content'       => 'Bar Bar Black Sheep'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BarsAndStripesV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bars And Stripes V90',
                    'description'   => 'Bars And Stripes V90',
                    'content'       => 'Bars And Stripes V90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bars And Stripes V90',
                    'description'   => 'Bars And Stripes V90',
                    'content'       => 'Bars And Stripes V90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bars And Stripes V90',
                    'description'   => 'Bars And Stripes V90',
                    'content'       => 'Bars And Stripes V90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Belissimo',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Belissimo',
                    'description'   => 'Belissimo',
                    'content'       => 'Belissimo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Belissimo',
                    'description'   => 'Belissimo',
                    'content'       => 'Belissimo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Belissimo',
                    'description'   => 'Belissimo',
                    'content'       => 'Belissimo'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_big5',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big 5',
                    'description'   => 'Big 5',
                    'content'       => 'Big 5'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big 5',
                    'description'   => 'Big 5',
                    'content'       => 'Big 5'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big 5',
                    'description'   => 'Big 5',
                    'content'       => 'Big 5'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBigBreak',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Break',
                    'description'   => 'Big Break',
                    'content'       => 'Big Break'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Break',
                    'description'   => 'Big Break',
                    'content'       => 'Big Break'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Break',
                    'description'   => 'Big Break',
                    'content'       => 'Big Break'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyIWBigBreak',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Break',
                    'description'   => 'Big Break',
                    'content'       => 'Big Break'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Break',
                    'description'   => 'Big Break',
                    'content'       => 'Big Break'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Break',
                    'description'   => 'Big Break',
                    'content'       => 'Big Break'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BigChef',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Chef',
                    'description'   => 'Big Chef',
                    'content'       => 'Big Chef'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Chef',
                    'description'   => 'Big Chef',
                    'content'       => 'Big Chef'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Chef',
                    'description'   => 'Big Chef',
                    'content'       => 'Big Chef'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBigKahunaSnakesAndLadders',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Kahuna - Snakes and Ladders',
                    'description'   => 'Big Kahuna - Snakes and Ladders',
                    'content'       => 'Big Kahuna - Snakes and Ladders'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Kahuna - Snakes and Ladders',
                    'description'   => 'Big Kahuna - Snakes and Ladders',
                    'content'       => 'Big Kahuna - Snakes and Ladders'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Kahuna - Snakes and Ladders',
                    'description'   => 'Big Kahuna - Snakes and Ladders',
                    'content'       => 'Big Kahuna - Snakes and Ladders'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BigKahunav90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Kahuna v90',
                    'description'   => 'Big Kahuna v90',
                    'content'       => 'Big Kahuna v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Kahuna v90',
                    'description'   => 'Big Kahuna v90',
                    'content'       => 'Big Kahuna v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Kahuna v90',
                    'description'   => 'Big Kahuna v90',
                    'content'       => 'Big Kahuna v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BigTopV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big Top v90',
                    'description'   => 'Big Top v90',
                    'content'       => 'Big Top v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big Top v90',
                    'description'   => 'Big Top v90',
                    'content'       => 'Big Top v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big Top v90',
                    'description'   => 'Big Top v90',
                    'content'       => 'Big Top v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBingoBonanza',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Blackjack Bonanza',
                    'description'   => 'Blackjack Bonanza',
                    'content'       => 'Blackjack Bonanza'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Blackjack Bonanza',
                    'description'   => 'Blackjack Bonanza',
                    'content'       => 'Blackjack Bonanza'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Blackjack Bonanza',
                    'description'   => 'Blackjack Bonanza',
                    'content'       => 'Blackjack Bonanza'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Bobby7s',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bobby 7s',
                    'description'   => 'Bobby 7s',
                    'content'       => 'Bobby 7s'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bobby 7s',
                    'description'   => 'Bobby 7s',
                    'content'       => 'Bobby 7s'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bobby 7s',
                    'description'   => 'Bobby 7s',
                    'content'       => 'Bobby 7s'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BootyTime',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Booty Time',
                    'description'   => 'Booty Time',
                    'content'       => 'Booty Time'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Booty Time',
                    'description'   => 'Booty Time',
                    'content'       => 'Booty Time'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Booty Time',
                    'description'   => 'Booty Time',
                    'content'       => 'Booty Time'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BreakAwayV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Break Away v90',
                    'description'   => 'Break Away v90',
                    'content'       => 'Break Away v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Break Away v90',
                    'description'   => 'Break Away v90',
                    'content'       => 'Break Away v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Break Away v90',
                    'description'   => 'Break Away v90',
                    'content'       => 'Break Away v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBreakDaBankAgainV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Break da Bank Again v90',
                    'description'   => 'Break da Bank Again v90',
                    'content'       => 'Break da Bank Again v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Break da Bank Again v90',
                    'description'   => 'Break da Bank Again v90',
                    'content'       => 'Break da Bank Again v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Break da Bank Again v90',
                    'description'   => 'Break da Bank Again v90',
                    'content'       => 'Break da Bank Again v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Bridezilla',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bridezilla',
                    'description'   => 'Bridezilla',
                    'content'       => 'Bridezilla'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bridezilla',
                    'description'   => 'Bridezilla',
                    'content'       => 'Bridezilla'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bridezilla',
                    'description'   => 'Bridezilla',
                    'content'       => 'Bridezilla'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BubbleBonanza',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bubble Bonanza',
                    'description'   => 'Bubble Bonanza',
                    'content'       => 'Bubble Bonanza'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bubble Bonanza',
                    'description'   => 'Bubble Bonanza',
                    'content'       => 'Bubble Bonanza'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bubble Bonanza',
                    'description'   => 'Bubble Bonanza',
                    'content'       => 'Bubble Bonanza'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBullsEye',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bulls Eye',
                    'description'   => 'Bulls Eye',
                    'content'       => 'Bulls Eye'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bulls Eye',
                    'description'   => 'Bulls Eye',
                    'content'       => 'Bulls Eye'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bulls Eye',
                    'description'   => 'Bulls Eye',
                    'content'       => 'Bulls Eye'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyBurningDesireV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Burning Desire v90',
                    'description'   => 'Burning Desire v90',
                    'content'       => 'Burning Desire v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Burning Desire v90',
                    'description'   => 'Burning Desire v90',
                    'content'       => 'Burning Desire v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Burning Desire v90',
                    'description'   => 'Burning Desire v90',
                    'content'       => 'Burning Desire v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_BushTelegraphV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bush Telegraph v90',
                    'description'   => 'Bush Telegraph v90',
                    'content'       => 'Bush Telegraph v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bush Telegraph v90',
                    'description'   => 'Bush Telegraph v90',
                    'content'       => 'Bush Telegraph v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bush Telegraph v90',
                    'description'   => 'Bush Telegraph v90',
                    'content'       => 'Bush Telegraph v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ButterFlies',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'ButterFlies',
                    'description'   => 'ButterFlies',
                    'content'       => 'ButterFlies'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'ButterFlies',
                    'description'   => 'ButterFlies',
                    'content'       => 'ButterFlies'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'ButterFlies',
                    'description'   => 'ButterFlies',
                    'content'       => 'ButterFlies'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyCabinFever',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cabin Fever',
                    'description'   => 'Cabin Fever',
                    'content'       => 'Cabin Fever'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cabin Fever',
                    'description'   => 'Cabin Fever',
                    'content'       => 'Cabin Fever'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cabin Fever',
                    'description'   => 'Cabin Fever',
                    'content'       => 'Cabin Fever'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyCaptainCash',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Captain Cash',
                    'description'   => 'Captain Cash',
                    'content'       => 'Captain Cash'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Captain Cash',
                    'description'   => 'Captain Cash',
                    'content'       => 'Captain Cash'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Captain Cash',
                    'description'   => 'Captain Cash',
                    'content'       => 'Captain Cash'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Carnavalv90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Carnaval v90',
                    'description'   => 'Carnaval v90',
                    'content'       => 'Carnaval v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Carnaval v90',
                    'description'   => 'Carnaval v90',
                    'content'       => 'Carnaval v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Carnaval v90',
                    'description'   => 'Carnaval v90',
                    'content'       => 'Carnaval v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_CashClams',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cash Clams',
                    'description'   => 'Cash Clams',
                    'content'       => 'Cash Clams'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cash Clams',
                    'description'   => 'Cash Clams',
                    'content'       => 'Cash Clams'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cash Clams',
                    'description'   => 'Cash Clams',
                    'content'       => 'Cash Clams'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_CashanovaV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cashanova',
                    'description'   => 'Cashanova',
                    'content'       => 'Cashanova'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cashanova',
                    'description'   => 'Cashanova',
                    'content'       => 'Cashanova'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cashanova',
                    'description'   => 'Cashanova',
                    'content'       => 'Cashanova'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_CashapillarV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cashapillar v90',
                    'description'   => 'Cashapillar v90',
                    'content'       => 'Cashapillar v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cashapillar v90',
                    'description'   => 'Cashapillar v90',
                    'content'       => 'Cashapillar v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cashapillar v90',
                    'description'   => 'Cashapillar v90',
                    'content'       => 'Cashapillar v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyChainMail',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Chain Mail',
                    'description'   => 'Chain Mail',
                    'content'       => 'Chain Mail'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Chain Mail',
                    'description'   => 'Chain Mail',
                    'content'       => 'Chain Mail'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Chain Mail',
                    'description'   => 'Chain Mail',
                    'content'       => 'Chain Mail'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Chainmailnew',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Chain Mail New',
                    'description'   => 'Chain Mail New',
                    'content'       => 'Chain Mail New'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Chain Mail New',
                    'description'   => 'Chain Mail New',
                    'content'       => 'Chain Mail New'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Chain Mail New',
                    'description'   => 'Chain Mail New',
                    'content'       => 'Chain Mail New'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_CherryRed',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cherry Red',
                    'description'   => 'Cherry Red',
                    'content'       => 'Cherry Red'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cherry Red',
                    'description'   => 'Cherry Red',
                    'content'       => 'Cherry Red'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cherry Red',
                    'description'   => 'Cherry Red',
                    'content'       => 'Cherry Red'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyChiefsFortune',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Chiefs Magic',
                    'description'   => 'Chiefs Magic',
                    'content'       => 'Chiefs Magic'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Chiefs Magic',
                    'description'   => 'Chiefs Magic',
                    'content'       => 'Chiefs Magic'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Chiefs Magic',
                    'description'   => 'Chiefs Magic',
                    'content'       => 'Chiefs Magic'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyCityofGold',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'City of Gold',
                    'description'   => 'City of Gold',
                    'content'       => 'City of Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'City of Gold',
                    'description'   => 'City of Gold',
                    'content'       => 'City of Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'City of Gold',
                    'description'   => 'City of Gold',
                    'content'       => 'City of Gold'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_coolbuck',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cool Buck',
                    'description'   => 'Cool Buck',
                    'content'       => 'Cool Buck'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cool Buck',
                    'description'   => 'Cool Buck',
                    'content'       => 'Cool Buck'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cool Buck',
                    'description'   => 'Cool Buck',
                    'content'       => 'Cool Buck'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_cosmicc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cosmic Cat',
                    'description'   => 'Cosmic Cat',
                    'content'       => 'Cosmic Cat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cosmic Cat',
                    'description'   => 'Cosmic Cat',
                    'content'       => 'Cosmic Cat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cosmic Cat',
                    'description'   => 'Cosmic Cat',
                    'content'       => 'Cosmic Cat'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_crackerjack',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cracker Jack',
                    'description'   => 'Cracker Jack',
                    'content'       => 'Cracker Jack'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cracker Jack',
                    'description'   => 'Cracker Jack',
                    'content'       => 'Cracker Jack'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cracker Jack',
                    'description'   => 'Cracker Jack',
                    'content'       => 'Cracker Jack'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyCrazy80s',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crazy 80s',
                    'description'   => 'Crazy 80s',
                    'content'       => 'Crazy 80s'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crazy 80s',
                    'description'   => 'Crazy 80s',
                    'content'       => 'Crazy 80s'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crazy 80s',
                    'description'   => 'Crazy 80s',
                    'content'       => 'Crazy 80s'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_CrazyChameleonsV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crazy Chameleons v90',
                    'description'   => 'Crazy Chameleons v90',
                    'content'       => 'Crazy Chameleons v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crazy Chameleons v90',
                    'description'   => 'Crazy Chameleons v90',
                    'content'       => 'Crazy Chameleons v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crazy Chameleons v90',
                    'description'   => 'Crazy Chameleons v90',
                    'content'       => 'Crazy Chameleons v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_crocs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crazy Crocs',
                    'description'   => 'Crazy Crocs',
                    'content'       => 'Crazy Crocs'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crazy Crocs',
                    'description'   => 'Crazy Crocs',
                    'content'       => 'Crazy Crocs'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crazy Crocs',
                    'description'   => 'Crazy Crocs',
                    'content'       => 'Crazy Crocs'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Crocodopolis',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crocodopolis',
                    'description'   => 'Crocodopolis',
                    'content'       => 'Crocodopolis'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crocodopolis',
                    'description'   => 'Crocodopolis',
                    'content'       => 'Crocodopolis'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crocodopolis',
                    'description'   => 'Crocodopolis',
                    'content'       => 'Crocodopolis'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyCutesyPie',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cutesy Pie',
                    'description'   => 'Cutesy Pie',
                    'content'       => 'Cutesy Pie'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cutesy Pie',
                    'description'   => 'Cutesy Pie',
                    'content'       => 'Cutesy Pie'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cutesy Pie',
                    'description'   => 'Cutesy Pie',
                    'content'       => 'Cutesy Pie'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDeckTheHallsV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Deck The Halls v90',
                    'description'   => 'Deck The Halls v90',
                    'content'       => 'Deck The Halls v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Deck The Halls v90',
                    'description'   => 'Deck The Halls v90',
                    'content'       => 'Deck The Halls v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Deck The Halls v90',
                    'description'   => 'Deck The Halls v90',
                    'content'       => 'Deck The Halls v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDiamondDeal',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Diamond Deal',
                    'description'   => 'Diamond Deal',
                    'content'       => 'Diamond Deal'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Diamond Deal',
                    'description'   => 'Diamond Deal',
                    'content'       => 'Diamond Deal'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Diamond Deal',
                    'description'   => 'Diamond Deal',
                    'content'       => 'Diamond Deal'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDiamond7s',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Diamond Sevens',
                    'description'   => 'Diamond Sevens',
                    'content'       => 'Diamond Sevens'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Diamond Sevens',
                    'description'   => 'Diamond Sevens',
                    'content'       => 'Diamond Sevens'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Diamond Sevens',
                    'description'   => 'Diamond Sevens',
                    'content'       => 'Diamond Sevens'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_DinoMight',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dino Might',
                    'description'   => 'Dino Might',
                    'content'       => 'Dino Might'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dino Might',
                    'description'   => 'Dino Might',
                    'content'       => 'Dino Might'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dino Might',
                    'description'   => 'Dino Might',
                    'content'       => 'Dino Might'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_DoctorLove',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Doctor Love',
                    'description'   => 'Doctor Love',
                    'content'       => 'Doctor Love'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Doctor Love',
                    'description'   => 'Doctor Love',
                    'content'       => 'Doctor Love'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Doctor Love',
                    'description'   => 'Doctor Love',
                    'content'       => 'Doctor Love'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDogfather',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dogfather',
                    'description'   => 'Dogfather',
                    'content'       => 'Dogfather'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dogfather',
                    'description'   => 'Dogfather',
                    'content'       => 'Dogfather'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dogfather',
                    'description'   => 'Dogfather',
                    'content'       => 'Dogfather'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDonDeal',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Don Deal',
                    'description'   => 'Don Deal',
                    'content'       => 'Don Deal'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Don Deal',
                    'description'   => 'Don Deal',
                    'content'       => 'Don Deal'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Don Deal',
                    'description'   => 'Don Deal',
                    'content'       => 'Don Deal'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDoubleDose',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Double Dose',
                    'description'   => 'Double Dose',
                    'content'       => 'Double Dose'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Double Dose',
                    'description'   => 'Double Dose',
                    'content'       => 'Double Dose'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Double Dose',
                    'description'   => 'Double Dose',
                    'content'       => 'Double Dose'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_dm',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Double Magic',
                    'description'   => 'Double Magic',
                    'content'       => 'Double Magic'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Double Magic',
                    'description'   => 'Double Magic',
                    'content'       => 'Double Magic'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Double Magic',
                    'description'   => 'Double Magic',
                    'content'       => 'Double Magic'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_DrWattsUp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dr Watts Up',
                    'description'   => 'Dr Watts Up',
                    'content'       => 'Dr Watts Up'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dr Watts Up',
                    'description'   => 'Dr Watts Up',
                    'content'       => 'Dr Watts Up'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dr Watts Up',
                    'description'   => 'Dr Watts Up',
                    'content'       => 'Dr Watts Up'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyDragonsFortune',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon\'s Fortune',
                    'description'   => 'Dragon\'s Fortune',
                    'content'       => 'Dragon\'s Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon\'s Fortune',
                    'description'   => 'Dragon\'s Fortune',
                    'content'       => 'Dragon\'s Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon\'s Fortune',
                    'description'   => 'Dragon\'s Fortune',
                    'content'       => 'Dragon\'s Fortune'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ElectricDiva',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Electric Diva',
                    'description'   => 'Electric Diva',
                    'content'       => 'Electric Diva'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Electric Diva',
                    'description'   => 'Electric Diva',
                    'content'       => 'Electric Diva'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Electric Diva',
                    'description'   => 'Electric Diva',
                    'content'       => 'Electric Diva'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyElementals',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Elementals',
                    'description'   => 'Elementals',
                    'content'       => 'Elementals'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Elementals',
                    'description'   => 'Elementals',
                    'content'       => 'Elementals'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Elementals',
                    'description'   => 'Elementals',
                    'content'       => 'Elementals'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_EnchantedMermaid',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Enchanted Mermaid',
                    'description'   => 'Enchanted Mermaid',
                    'content'       => 'Enchanted Mermaid'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Enchanted Mermaid',
                    'description'   => 'Enchanted Mermaid',
                    'content'       => 'Enchanted Mermaid'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Enchanted Mermaid',
                    'description'   => 'Enchanted Mermaid',
                    'content'       => 'Enchanted Mermaid'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_EnchantedWoods',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Enchanted Woods',
                    'description'   => 'Enchanted Woods',
                    'content'       => 'Enchanted Woods'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Enchanted Woods',
                    'description'   => 'Enchanted Woods',
                    'content'       => 'Enchanted Woods'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Enchanted Woods',
                    'description'   => 'Enchanted Woods',
                    'content'       => 'Enchanted Woods'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFairyRing',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fairy Ring',
                    'description'   => 'Fairy Ring',
                    'content'       => 'Fairy Ring'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fairy Ring',
                    'description'   => 'Fairy Ring',
                    'content'       => 'Fairy Ring'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fairy Ring',
                    'description'   => 'Fairy Ring',
                    'content'       => 'Fairy Ring'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_fan7',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fantastic Sevens',
                    'description'   => 'Fantastic Sevens',
                    'content'       => 'Fantastic Sevens'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fantastic Sevens',
                    'description'   => 'Fantastic Sevens',
                    'content'       => 'Fantastic Sevens'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fantastic Sevens',
                    'description'   => 'Fantastic Sevens',
                    'content'       => 'Fantastic Sevens'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_FatLadySings',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fat Lady Sings',
                    'description'   => 'Fat Lady Sings',
                    'content'       => 'Fat Lady Sings'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fat Lady Sings',
                    'description'   => 'Fat Lady Sings',
                    'content'       => 'Fat Lady Sings'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fat Lady Sings',
                    'description'   => 'Fat Lady Sings',
                    'content'       => 'Fat Lady Sings'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_FireHawk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fire Hawk',
                    'description'   => 'Fire Hawk',
                    'content'       => 'Fire Hawk'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fire Hawk',
                    'description'   => 'Fire Hawk',
                    'content'       => 'Fire Hawk'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fire Hawk',
                    'description'   => 'Fire Hawk',
                    'content'       => 'Fire Hawk'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFloriditaFandango',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Floridita Fandango',
                    'description'   => 'Floridita Fandango',
                    'content'       => 'Floridita Fandango'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Floridita Fandango',
                    'description'   => 'Floridita Fandango',
                    'content'       => 'Floridita Fandango'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Floridita Fandango',
                    'description'   => 'Floridita Fandango',
                    'content'       => 'Floridita Fandango'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFlosDiner',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Flos Diner',
                    'description'   => 'Flos Diner',
                    'content'       => 'Flos Diner'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Flos Diner',
                    'description'   => 'Flos Diner',
                    'content'       => 'Flos Diner'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Flos Diner',
                    'description'   => 'Flos Diner',
                    'content'       => 'Flos Diner'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_flowerpower',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Flower Power',
                    'description'   => 'Flower Power',
                    'content'       => 'Flower Power'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Flower Power',
                    'description'   => 'Flower Power',
                    'content'       => 'Flower Power'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Flower Power',
                    'description'   => 'Flower Power',
                    'content'       => 'Flower Power'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFlyingAce',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Flying Ace',
                    'description'   => 'Flying Ace',
                    'content'       => 'Flying Ace'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Flying Ace',
                    'description'   => 'Flying Ace',
                    'content'       => 'Flying Ace'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Flying Ace',
                    'description'   => 'Flying Ace',
                    'content'       => 'Flying Ace'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ForsakenKingdom',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Forsaken Kingdom',
                    'description'   => 'Forsaken Kingdom',
                    'content'       => 'Forsaken Kingdom'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Forsaken Kingdom',
                    'description'   => 'Forsaken Kingdom',
                    'content'       => 'Forsaken Kingdom'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Forsaken Kingdom',
                    'description'   => 'Forsaken Kingdom',
                    'content'       => 'Forsaken Kingdom'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFortuna',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortuna',
                    'description'   => 'Fortuna',
                    'content'       => 'Fortuna'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortuna',
                    'description'   => 'Fortuna',
                    'content'       => 'Fortuna'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortuna',
                    'description'   => 'Fortuna',
                    'content'       => 'Fortuna'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_FortuneCookie',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortune Cookie',
                    'description'   => 'Fortune Cookie',
                    'content'       => 'Fortune Cookie'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortune Cookie',
                    'description'   => 'Fortune Cookie',
                    'content'       => 'Fortune Cookie'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortune Cookie',
                    'description'   => 'Fortune Cookie',
                    'content'       => 'Fortune Cookie'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFreeSpirit',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Free Spirit',
                    'description'   => 'Free Spirit',
                    'content'       => 'Free Spirit'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Free Spirit',
                    'description'   => 'Free Spirit',
                    'content'       => 'Free Spirit'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Free Spirit',
                    'description'   => 'Free Spirit',
                    'content'       => 'Free Spirit'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFrootLoot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Froot Loot',
                    'description'   => 'Froot Loot',
                    'content'       => 'Froot Loot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Froot Loot',
                    'description'   => 'Froot Loot',
                    'content'       => 'Froot Loot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Froot Loot',
                    'description'   => 'Froot Loot',
                    'content'       => 'Froot Loot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFrostBite',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Frost Bite',
                    'description'   => 'Frost Bite',
                    'content'       => 'Frost Bite'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Frost Bite',
                    'description'   => 'Frost Bite',
                    'content'       => 'Frost Bite'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Frost Bite',
                    'description'   => 'Frost Bite',
                    'content'       => 'Frost Bite'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFruitSalad',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fruit Salad',
                    'description'   => 'Fruit Salad',
                    'content'       => 'Fruit Salad'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fruit Salad',
                    'description'   => 'Fruit Salad',
                    'content'       => 'Fruit Salad'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fruit Salad',
                    'description'   => 'Fruit Salad',
                    'content'       => 'Fruit Salad'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyFunHouse',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Funhouse',
                    'description'   => 'Funhouse',
                    'content'       => 'Funhouse'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Funhouse',
                    'description'   => 'Funhouse',
                    'content'       => 'Funhouse'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Funhouse',
                    'description'   => 'Funhouse',
                    'content'       => 'Funhouse'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Galacticons',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Galacticons',
                    'description'   => 'Galacticons',
                    'content'       => 'Galacticons'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Galacticons',
                    'description'   => 'Galacticons',
                    'content'       => 'Galacticons'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Galacticons',
                    'description'   => 'Galacticons',
                    'content'       => 'Galacticons'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_geniesgems',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Genie\'s Gems',
                    'description'   => 'Genie\'s Gems',
                    'content'       => 'Genie\'s Gems'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Genie\'s Gems',
                    'description'   => 'Genie\'s Gems',
                    'content'       => 'Genie\'s Gems'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Genie\'s Gems',
                    'description'   => 'Genie\'s Gems',
                    'content'       => 'Genie\'s Gems'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GiftRap',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gift Rap',
                    'description'   => 'Gift Rap',
                    'content'       => 'Gift Rap'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gift Rap',
                    'description'   => 'Gift Rap',
                    'content'       => 'Gift Rap'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gift Rap',
                    'description'   => 'Gift Rap',
                    'content'       => 'Gift Rap'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GirlsWithGunsV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Girls With Guns - Jungle Heat  v90',
                    'description'   => 'Girls With Guns - Jungle Heat  v90',
                    'content'       => 'Girls With Guns - Jungle Heat  v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Girls With Guns - Jungle Heat  v90',
                    'description'   => 'Girls With Guns - Jungle Heat  v90',
                    'content'       => 'Girls With Guns - Jungle Heat  v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Girls With Guns - Jungle Heat  v90',
                    'description'   => 'Girls With Guns - Jungle Heat  v90',
                    'content'       => 'Girls With Guns - Jungle Heat  v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_girlswithgunsfrozenDawn',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Girls With Guns II - Frozen Dawn',
                    'description'   => 'Girls With Guns II - Frozen Dawn',
                    'content'       => 'Girls With Guns II - Frozen Dawn'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Girls With Guns II - Frozen Dawn',
                    'description'   => 'Girls With Guns II - Frozen Dawn',
                    'content'       => 'Girls With Guns II - Frozen Dawn'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Girls With Guns II - Frozen Dawn',
                    'description'   => 'Girls With Guns II - Frozen Dawn',
                    'content'       => 'Girls With Guns II - Frozen Dawn'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GladiatorsGold',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gladiators Gold',
                    'description'   => 'Gladiators Gold',
                    'content'       => 'Gladiators Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gladiators Gold',
                    'description'   => 'Gladiators Gold',
                    'content'       => 'Gladiators Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gladiators Gold',
                    'description'   => 'Gladiators Gold',
                    'content'       => 'Gladiators Gold'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_goblinsgold',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Goblins Gold',
                    'description'   => 'Goblins Gold',
                    'content'       => 'Goblins Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Goblins Gold',
                    'description'   => 'Goblins Gold',
                    'content'       => 'Goblins Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Goblins Gold',
                    'description'   => 'Goblins Gold',
                    'content'       => 'Goblins Gold'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyGoldCoast',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gold Coast',
                    'description'   => 'Gold Coast',
                    'content'       => 'Gold Coast'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gold Coast',
                    'description'   => 'Gold Coast',
                    'content'       => 'Gold Coast'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gold Coast',
                    'description'   => 'Gold Coast',
                    'content'       => 'Gold Coast'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GoldFactoryV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gold Factory V90',
                    'description'   => 'Gold Factory V90',
                    'content'       => 'Gold Factory V90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gold Factory V90',
                    'description'   => 'Gold Factory V90',
                    'content'       => 'Gold Factory V90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gold Factory V90',
                    'description'   => 'Gold Factory V90',
                    'content'       => 'Gold Factory V90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_gdragon',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Golden Dragon',
                    'description'   => 'Golden Dragon',
                    'content'       => 'Golden Dragon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Golden Dragon',
                    'description'   => 'Golden Dragon',
                    'content'       => 'Golden Dragon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Golden Dragon',
                    'description'   => 'Golden Dragon',
                    'content'       => 'Golden Dragon'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyGoodToGo',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Good To Go',
                    'description'   => 'Good To Go',
                    'content'       => 'Good To Go'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Good To Go',
                    'description'   => 'Good To Go',
                    'content'       => 'Good To Go'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Good To Go',
                    'description'   => 'Good To Go',
                    'content'       => 'Good To Go'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GopherGoldV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gopher Gold v90',
                    'description'   => 'Gopher Gold v90',
                    'content'       => 'Gopher Gold v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gopher Gold v90',
                    'description'   => 'Gopher Gold v90',
                    'content'       => 'Gopher Gold v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gopher Gold v90',
                    'description'   => 'Gopher Gold v90',
                    'content'       => 'Gopher Gold v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyGrand7s',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Grand Sevens',
                    'description'   => 'Grand Sevens',
                    'content'       => 'Grand Sevens'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Grand Sevens',
                    'description'   => 'Grand Sevens',
                    'content'       => 'Grand Sevens'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Grand Sevens',
                    'description'   => 'Grand Sevens',
                    'content'       => 'Grand Sevens'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GreatGriffin',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Great Griffin',
                    'description'   => 'Great Griffin',
                    'content'       => 'Great Griffin'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Great Griffin',
                    'description'   => 'Great Griffin',
                    'content'       => 'Great Griffin'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Great Griffin',
                    'description'   => 'Great Griffin',
                    'content'       => 'Great Griffin'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_GungPow',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gung Pow',
                    'description'   => 'Gung Pow',
                    'content'       => 'Gung Pow'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gung Pow',
                    'description'   => 'Gung Pow',
                    'content'       => 'Gung Pow'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gung Pow',
                    'description'   => 'Gung Pow',
                    'content'       => 'Gung Pow'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyHappyNewYear',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Happy New Year',
                    'description'   => 'Happy New Year',
                    'content'       => 'Happy New Year'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Happy New Year',
                    'description'   => 'Happy New Year',
                    'content'       => 'Happy New Year'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Happy New Year',
                    'description'   => 'Happy New Year',
                    'content'       => 'Happy New Year'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyHeavyMetal',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Heavy Metal',
                    'description'   => 'Heavy Metal',
                    'content'       => 'Heavy Metal'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Heavy Metal',
                    'description'   => 'Heavy Metal',
                    'content'       => 'Heavy Metal'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Heavy Metal',
                    'description'   => 'Heavy Metal',
                    'content'       => 'Heavy Metal'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Hexaline',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hexaline',
                    'description'   => 'Hexaline',
                    'content'       => 'Hexaline'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hexaline',
                    'description'   => 'Hexaline',
                    'content'       => 'Hexaline'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hexaline',
                    'description'   => 'Hexaline',
                    'content'       => 'Hexaline'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HighFive',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'High Five',
                    'description'   => 'High Five',
                    'content'       => 'High Five'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'High Five',
                    'description'   => 'High Five',
                    'content'       => 'High Five'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'High Five',
                    'description'   => 'High Five',
                    'content'       => 'High Five'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_hohoho',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ho Ho Ho',
                    'description'   => 'Ho Ho Ho',
                    'content'       => 'Ho Ho Ho'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ho Ho Ho',
                    'description'   => 'Ho Ho Ho',
                    'content'       => 'Ho Ho Ho'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ho Ho Ho',
                    'description'   => 'Ho Ho Ho',
                    'content'       => 'Ho Ho Ho'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HoHoHoV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ho Ho Ho v90',
                    'description'   => 'Ho Ho Ho v90',
                    'content'       => 'Ho Ho Ho v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ho Ho Ho v90',
                    'description'   => 'Ho Ho Ho v90',
                    'content'       => 'Ho Ho Ho v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ho Ho Ho v90',
                    'description'   => 'Ho Ho Ho v90',
                    'content'       => 'Ho Ho Ho v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HotasHades',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hot as Hades',
                    'description'   => 'Hot as Hades',
                    'content'       => 'Hot as Hades'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hot as Hades',
                    'description'   => 'Hot as Hades',
                    'content'       => 'Hot as Hades'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hot as Hades',
                    'description'   => 'Hot as Hades',
                    'content'       => 'Hot as Hades'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HotInk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hot Ink',
                    'description'   => 'Hot Ink',
                    'content'       => 'Hot Ink'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hot Ink',
                    'description'   => 'Hot Ink',
                    'content'       => 'Hot Ink'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hot Ink',
                    'description'   => 'Hot Ink',
                    'content'       => 'Hot Ink'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_HotInkV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hot Ink v90',
                    'description'   => 'Hot Ink v90',
                    'content'       => 'Hot Ink v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hot Ink v90',
                    'description'   => 'Hot Ink v90',
                    'content'       => 'Hot Ink v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hot Ink v90',
                    'description'   => 'Hot Ink v90',
                    'content'       => 'Hot Ink v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyHotShot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hot Shot',
                    'description'   => 'Hot Shot',
                    'content'       => 'Hot Shot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hot Shot',
                    'description'   => 'Hot Shot',
                    'content'       => 'Hot Shot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hot Shot',
                    'description'   => 'Hot Shot',
                    'content'       => 'Hot Shot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ImmortalRomancev90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Immortal Romance v90',
                    'description'   => 'Immortal Romance v90',
                    'content'       => 'Immortal Romance v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Immortal Romance v90',
                    'description'   => 'Immortal Romance v90',
                    'content'       => 'Immortal Romance v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Immortal Romance v90',
                    'description'   => 'Immortal Romance v90',
                    'content'       => 'Immortal Romance v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_InItToWinIt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'In It To Win It',
                    'description'   => 'In It To Win It',
                    'content'       => 'In It To Win It'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'In It To Win It',
                    'description'   => 'In It To Win It',
                    'content'       => 'In It To Win It'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'In It To Win It',
                    'description'   => 'In It To Win It',
                    'content'       => 'In It To Win It'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_IrishEyes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Irish Eyes',
                    'description'   => 'Irish Eyes',
                    'content'       => 'Irish Eyes'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Irish Eyes',
                    'description'   => 'Irish Eyes',
                    'content'       => 'Irish Eyes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Irish Eyes',
                    'description'   => 'Irish Eyes',
                    'content'       => 'Irish Eyes'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RRJackAndJill',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jack & Jill',
                    'description'   => 'Jack & Jill',
                    'content'       => 'Jack & Jill'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jack & Jill',
                    'description'   => 'Jack & Jill',
                    'content'       => 'Jack & Jill'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jack & Jill',
                    'description'   => 'Jack & Jill',
                    'content'       => 'Jack & Jill'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyJackintheBox',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jack in the Box',
                    'description'   => 'Jack in the Box',
                    'content'       => 'Jack in the Box'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jack in the Box',
                    'description'   => 'Jack in the Box',
                    'content'       => 'Jack in the Box'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jack in the Box',
                    'description'   => 'Jack in the Box',
                    'content'       => 'Jack in the Box'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jexpress',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jackpot Express',
                    'description'   => 'Jackpot Express',
                    'content'       => 'Jackpot Express'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jackpot Express',
                    'description'   => 'Jackpot Express',
                    'content'       => 'Jackpot Express'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jackpot Express',
                    'description'   => 'Jackpot Express',
                    'content'       => 'Jackpot Express'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_JasonAndTheGoldenFleece',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jason And The Golden Fleece',
                    'description'   => 'Jason And The Golden Fleece',
                    'content'       => 'Jason And The Golden Fleece'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jason And The Golden Fleece',
                    'description'   => 'Jason And The Golden Fleece',
                    'content'       => 'Jason And The Golden Fleece'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jason And The Golden Fleece',
                    'description'   => 'Jason And The Golden Fleece',
                    'content'       => 'Jason And The Golden Fleece'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jesters',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jesters Jackpot',
                    'description'   => 'Jesters Jackpot',
                    'content'       => 'Jesters Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jesters Jackpot',
                    'description'   => 'Jesters Jackpot',
                    'content'       => 'Jesters Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jesters Jackpot',
                    'description'   => 'Jesters Jackpot',
                    'content'       => 'Jesters Jackpot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyJewelThief',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jewel Thief',
                    'description'   => 'Jewel Thief',
                    'content'       => 'Jewel Thief'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jewel Thief',
                    'description'   => 'Jewel Thief',
                    'content'       => 'Jewel Thief'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jewel Thief',
                    'description'   => 'Jewel Thief',
                    'content'       => 'Jewel Thief'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_JewelsOfTheOrient',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jewels Of The Orient',
                    'description'   => 'Jewels Of The Orient',
                    'content'       => 'Jewels Of The Orient'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jewels Of The Orient',
                    'description'   => 'Jewels Of The Orient',
                    'content'       => 'Jewels Of The Orient'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jewels Of The Orient',
                    'description'   => 'Jewels Of The Orient',
                    'content'       => 'Jewels Of The Orient'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyJingleBells',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jingle Bells',
                    'description'   => 'Jingle Bells',
                    'content'       => 'Jingle Bells'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jingle Bells',
                    'description'   => 'Jingle Bells',
                    'content'       => 'Jingle Bells'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jingle Bells',
                    'description'   => 'Jingle Bells',
                    'content'       => 'Jingle Bells'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_joyofsix',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Joy Of Six',
                    'description'   => 'Joy Of Six',
                    'content'       => 'Joy Of Six'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Joy Of Six',
                    'description'   => 'Joy Of Six',
                    'content'       => 'Joy Of Six'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Joy Of Six',
                    'description'   => 'Joy Of Six',
                    'content'       => 'Joy Of Six'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyJungleJim',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jungle Jim',
                    'description'   => 'Jungle Jim',
                    'content'       => 'Jungle Jim'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jungle Jim',
                    'description'   => 'Jungle Jim',
                    'content'       => 'Jungle Jim'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jungle Jim',
                    'description'   => 'Jungle Jim',
                    'content'       => 'Jungle Jim'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jurassicbr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jurassic Big Reels',
                    'description'   => 'Jurassic Big Reels',
                    'content'       => 'Jurassic Big Reels'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jurassic Big Reels',
                    'description'   => 'Jurassic Big Reels',
                    'content'       => 'Jurassic Big Reels'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jurassic Big Reels',
                    'description'   => 'Jurassic Big Reels',
                    'content'       => 'Jurassic Big Reels'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jurassicjackpot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jurassic Jackpot',
                    'description'   => 'Jurassic Jackpot',
                    'content'       => 'Jurassic Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jurassic Jackpot',
                    'description'   => 'Jurassic Jackpot',
                    'content'       => 'Jurassic Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jurassic Jackpot',
                    'description'   => 'Jurassic Jackpot',
                    'content'       => 'Jurassic Jackpot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_jurassicpark',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jurassic Park',
                    'description'   => 'Jurassic Park',
                    'content'       => 'Jurassic Park'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jurassic Park',
                    'description'   => 'Jurassic Park',
                    'content'       => 'Jurassic Park'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jurassic Park',
                    'description'   => 'Jurassic Park',
                    'content'       => 'Jurassic Park'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_KaratePig',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Karate Pig',
                    'description'   => 'Karate Pig',
                    'content'       => 'Karate Pig'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Karate Pig',
                    'description'   => 'Karate Pig',
                    'content'       => 'Karate Pig'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Karate Pig',
                    'description'   => 'Karate Pig',
                    'content'       => 'Karate Pig'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_KaratePigv90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Karate Pig v90',
                    'description'   => 'Karate Pig v90',
                    'content'       => 'Karate Pig v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Karate Pig v90',
                    'description'   => 'Karate Pig v90',
                    'content'       => 'Karate Pig v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Karate Pig v90',
                    'description'   => 'Karate Pig v90',
                    'content'       => 'Karate Pig v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyKashatoa',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Kashatoa',
                    'description'   => 'Kashatoa',
                    'content'       => 'Kashatoa'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Kashatoa',
                    'description'   => 'Kashatoa',
                    'content'       => 'Kashatoa'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Kashatoa',
                    'description'   => 'Kashatoa',
                    'content'       => 'Kashatoa'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LadiesNiteV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ladies Nite v90',
                    'description'   => 'Ladies Nite v90',
                    'content'       => 'Ladies Nite v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ladies Nite v90',
                    'description'   => 'Ladies Nite v90',
                    'content'       => 'Ladies Nite v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ladies Nite v90',
                    'description'   => 'Ladies Nite v90',
                    'content'       => 'Ladies Nite v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LeaguesOfFortune',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Leagues Of Fortune',
                    'description'   => 'Leagues Of Fortune',
                    'content'       => 'Leagues Of Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Leagues Of Fortune',
                    'description'   => 'Leagues Of Fortune',
                    'content'       => 'Leagues Of Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Leagues Of Fortune',
                    'description'   => 'Leagues Of Fortune',
                    'content'       => 'Leagues Of Fortune'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyLegacy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Legacy',
                    'description'   => 'Legacy',
                    'content'       => 'Legacy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Legacy',
                    'description'   => 'Legacy',
                    'content'       => 'Legacy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Legacy',
                    'description'   => 'Legacy',
                    'content'       => 'Legacy'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LegendOfOlympus',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Legend of Olympus',
                    'description'   => 'Legend of Olympus',
                    'content'       => 'Legend of Olympus'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Legend of Olympus',
                    'description'   => 'Legend of Olympus',
                    'content'       => 'Legend of Olympus'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Legend of Olympus',
                    'description'   => 'Legend of Olympus',
                    'content'       => 'Legend of Olympus'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LooseCannon',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Loose Cannon',
                    'description'   => 'Loose Cannon',
                    'content'       => 'Loose Cannon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Loose Cannon',
                    'description'   => 'Loose Cannon',
                    'content'       => 'Loose Cannon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Loose Cannon',
                    'description'   => 'Loose Cannon',
                    'content'       => 'Loose Cannon'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LoveBugs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Love Bugs',
                    'description'   => 'Love Bugs',
                    'content'       => 'Love Bugs'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Love Bugs',
                    'description'   => 'Love Bugs',
                    'content'       => 'Love Bugs'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Love Bugs',
                    'description'   => 'Love Bugs',
                    'content'       => 'Love Bugs'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LuckyCharmer',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Charmer',
                    'description'   => 'Lucky Charmer',
                    'content'       => 'Lucky Charmer'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Charmer',
                    'description'   => 'Lucky Charmer',
                    'content'       => 'Lucky Charmer'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Charmer',
                    'description'   => 'Lucky Charmer',
                    'content'       => 'Lucky Charmer'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LuckyKoiV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Koi v90',
                    'description'   => 'Lucky Koi v90',
                    'content'       => 'Lucky Koi v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Koi v90',
                    'description'   => 'Lucky Koi v90',
                    'content'       => 'Lucky Koi v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Koi v90',
                    'description'   => 'Lucky Koi v90',
                    'content'       => 'Lucky Koi v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LuckyLeprechaunsLoot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Leprechauns Loot',
                    'description'   => 'Lucky Leprechauns Loot',
                    'content'       => 'Lucky Leprechauns Loot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Leprechauns Loot',
                    'description'   => 'Lucky Leprechauns Loot',
                    'content'       => 'Lucky Leprechauns Loot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Leprechauns Loot',
                    'description'   => 'Lucky Leprechauns Loot',
                    'content'       => 'Lucky Leprechauns Loot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LuckyWitch',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Witch',
                    'description'   => 'Lucky Witch',
                    'content'       => 'Lucky Witch'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Witch',
                    'description'   => 'Lucky Witch',
                    'content'       => 'Lucky Witch'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Witch',
                    'description'   => 'Lucky Witch',
                    'content'       => 'Lucky Witch'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_LuckyWitchv90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Witch v90',
                    'description'   => 'Lucky Witch v90',
                    'content'       => 'Lucky Witch v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Witch v90',
                    'description'   => 'Lucky Witch v90',
                    'content'       => 'Lucky Witch v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Witch v90',
                    'description'   => 'Lucky Witch v90',
                    'content'       => 'Lucky Witch v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MagicBoxes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Magic Boxes',
                    'description'   => 'Magic Boxes',
                    'content'       => 'Magic Boxes'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Magic Boxes',
                    'description'   => 'Magic Boxes',
                    'content'       => 'Magic Boxes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Magic Boxes',
                    'description'   => 'Magic Boxes',
                    'content'       => 'Magic Boxes'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_magiccharms',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Magic Charms',
                    'description'   => 'Magic Charms',
                    'content'       => 'Magic Charms'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Magic Charms',
                    'description'   => 'Magic Charms',
                    'content'       => 'Magic Charms'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Magic Charms',
                    'description'   => 'Magic Charms',
                    'content'       => 'Magic Charms'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyMagicSpell',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Magic Spell',
                    'description'   => 'Magic Spell',
                    'content'       => 'Magic Spell'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Magic Spell',
                    'description'   => 'Magic Spell',
                    'content'       => 'Magic Spell'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Magic Spell',
                    'description'   => 'Magic Spell',
                    'content'       => 'Magic Spell'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MaxDamageSlot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Max Damage',
                    'description'   => 'Max Damage',
                    'content'       => 'Max Damage'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Max Damage',
                    'description'   => 'Max Damage',
                    'content'       => 'Max Damage'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Max Damage',
                    'description'   => 'Max Damage',
                    'content'       => 'Max Damage'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MSBreakDaBankAgain',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mega Spin - Break Da Bank Again',
                    'description'   => 'Mega Spin - Break Da Bank Again',
                    'content'       => 'Mega Spin - Break Da Bank Again'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mega Spin - Break Da Bank Again',
                    'description'   => 'Mega Spin - Break Da Bank Again',
                    'content'       => 'Mega Spin - Break Da Bank Again'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mega Spin - Break Da Bank Again',
                    'description'   => 'Mega Spin - Break Da Bank Again',
                    'content'       => 'Mega Spin - Break Da Bank Again'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MSBreakDaBankAgainV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mega Spin - Break Da Bank Again v90',
                    'description'   => 'Mega Spin - Break Da Bank Again v90',
                    'content'       => 'Mega Spin - Break Da Bank Again v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mega Spin - Break Da Bank Again v90',
                    'description'   => 'Mega Spin - Break Da Bank Again v90',
                    'content'       => 'Mega Spin - Break Da Bank Again v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mega Spin - Break Da Bank Again v90',
                    'description'   => 'Mega Spin - Break Da Bank Again v90',
                    'content'       => 'Mega Spin - Break Da Bank Again v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MerlinsMillions',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Merlin\'s Millions',
                    'description'   => 'Merlin\'s Millions',
                    'content'       => 'Merlin\'s Millions'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Merlin\'s Millions',
                    'description'   => 'Merlin\'s Millions',
                    'content'       => 'Merlin\'s Millions'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Merlin\'s Millions',
                    'description'   => 'Merlin\'s Millions',
                    'content'       => 'Merlin\'s Millions'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MermaidsMillionsV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mermaids Millions v90',
                    'description'   => 'Mermaids Millions v90',
                    'content'       => 'Mermaids Millions v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mermaids Millions v90',
                    'description'   => 'Mermaids Millions v90',
                    'content'       => 'Mermaids Millions v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mermaids Millions v90',
                    'description'   => 'Mermaids Millions v90',
                    'content'       => 'Mermaids Millions v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyMochaOrange',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mocha Orange',
                    'description'   => 'Mocha Orange',
                    'content'       => 'Mocha Orange'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mocha Orange',
                    'description'   => 'Mocha Orange',
                    'content'       => 'Mocha Orange'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mocha Orange',
                    'description'   => 'Mocha Orange',
                    'content'       => 'Mocha Orange'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_monkeys',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Monkeys Money',
                    'description'   => 'Monkeys Money',
                    'content'       => 'Monkeys Money'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Monkeys Money',
                    'description'   => 'Monkeys Money',
                    'content'       => 'Monkeys Money'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Monkeys Money',
                    'description'   => 'Monkeys Money',
                    'content'       => 'Monkeys Money'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MonsterMania',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Monster Mania',
                    'description'   => 'Monster Mania',
                    'content'       => 'Monster Mania'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Monster Mania',
                    'description'   => 'Monster Mania',
                    'content'       => 'Monster Mania'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Monster Mania',
                    'description'   => 'Monster Mania',
                    'content'       => 'Monster Mania'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MoonshineV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Moonshine',
                    'description'   => 'Moonshine',
                    'content'       => 'Moonshine'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Moonshine',
                    'description'   => 'Moonshine',
                    'content'       => 'Moonshine'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Moonshine',
                    'description'   => 'Moonshine',
                    'content'       => 'Moonshine'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MugshotMadness',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mugshot Madness',
                    'description'   => 'Mugshot Madness',
                    'content'       => 'Mugshot Madness'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mugshot Madness',
                    'description'   => 'Mugshot Madness',
                    'content'       => 'Mugshot Madness'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mugshot Madness',
                    'description'   => 'Mugshot Madness',
                    'content'       => 'Mugshot Madness'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_MysticDreamsv90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mystic Dreams v90',
                    'description'   => 'Mystic Dreams v90',
                    'content'       => 'Mystic Dreams v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mystic Dreams v90',
                    'description'   => 'Mystic Dreams v90',
                    'content'       => 'Mystic Dreams v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mystic Dreams v90',
                    'description'   => 'Mystic Dreams v90',
                    'content'       => 'Mystic Dreams v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ninjamagic',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ninja Magic',
                    'description'   => 'Ninja Magic',
                    'content'       => 'Ninja Magic'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ninja Magic',
                    'description'   => 'Ninja Magic',
                    'content'       => 'Ninja Magic'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ninja Magic',
                    'description'   => 'Ninja Magic',
                    'content'       => 'Ninja Magic'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_octopays',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Octopays',
                    'description'   => 'Octopays',
                    'content'       => 'Octopays'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Octopays',
                    'description'   => 'Octopays',
                    'content'       => 'Octopays'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Octopays',
                    'description'   => 'Octopays',
                    'content'       => 'Octopays'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Octopays v90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Octopays v90',
                    'description'   => 'Octopays v90',
                    'content'       => 'Octopays v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Octopays v90',
                    'description'   => 'Octopays v90',
                    'content'       => 'Octopays v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Octopays v90',
                    'description'   => 'Octopays v90',
                    'content'       => 'Octopays v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RROldKingColeV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Old King Cole',
                    'description'   => 'Old King Cole',
                    'content'       => 'Old King Cole'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Old King Cole',
                    'description'   => 'Old King Cole',
                    'content'       => 'Old King Cole'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Old King Cole',
                    'description'   => 'Old King Cole',
                    'content'       => 'Old King Cole'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_OrientalFortune',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Oriental Fortune',
                    'description'   => 'Oriental Fortune',
                    'content'       => 'Oriental Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Oriental Fortune',
                    'description'   => 'Oriental Fortune',
                    'content'       => 'Oriental Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Oriental Fortune',
                    'description'   => 'Oriental Fortune',
                    'content'       => 'Oriental Fortune'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ParadiseFound',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Paradise Found',
                    'description'   => 'Paradise Found',
                    'content'       => 'Paradise Found'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Paradise Found',
                    'description'   => 'Paradise Found',
                    'content'       => 'Paradise Found'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Paradise Found',
                    'description'   => 'Paradise Found',
                    'content'       => 'Paradise Found'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_partytime',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Party Time',
                    'description'   => 'Party Time',
                    'content'       => 'Party Time'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Party Time',
                    'description'   => 'Party Time',
                    'content'       => 'Party Time'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Party Time',
                    'description'   => 'Party Time',
                    'content'       => 'Party Time'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyPeekaBoo',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Peek-a-Boo',
                    'description'   => 'Peek-a-Boo',
                    'content'       => 'Peek-a-Boo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Peek-a-Boo',
                    'description'   => 'Peek-a-Boo',
                    'content'       => 'Peek-a-Boo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Peek-a-Boo',
                    'description'   => 'Peek-a-Boo',
                    'content'       => 'Peek-a-Boo'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_PenguinSplash',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Penguin Splash',
                    'description'   => 'Penguin Splash',
                    'content'       => 'Penguin Splash'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Penguin Splash',
                    'description'   => 'Penguin Splash',
                    'content'       => 'Penguin Splash'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Penguin Splash',
                    'description'   => 'Penguin Splash',
                    'content'       => 'Penguin Splash'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_phantomcash',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Phantom Cash',
                    'description'   => 'Phantom Cash',
                    'content'       => 'Phantom Cash'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Phantom Cash',
                    'description'   => 'Phantom Cash',
                    'content'       => 'Phantom Cash'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Phantom Cash',
                    'description'   => 'Phantom Cash',
                    'content'       => 'Phantom Cash'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_PharaohBingo',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pharaoh Bingo',
                    'description'   => 'Pharaoh Bingo',
                    'content'       => 'Pharaoh Bingo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pharaoh Bingo',
                    'description'   => 'Pharaoh Bingo',
                    'content'       => 'Pharaoh Bingo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pharaoh Bingo',
                    'description'   => 'Pharaoh Bingo',
                    'content'       => 'Pharaoh Bingo'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_pharaohs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pharaohs Fortune',
                    'description'   => 'Pharaohs Fortune',
                    'content'       => 'Pharaohs Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pharaohs Fortune',
                    'description'   => 'Pharaohs Fortune',
                    'content'       => 'Pharaohs Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pharaohs Fortune',
                    'description'   => 'Pharaohs Fortune',
                    'content'       => 'Pharaohs Fortune'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_PiggyFortunes',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Piggy fortunes',
                    'description'   => 'Piggy fortunes',
                    'content'       => 'Piggy fortunes'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Piggy fortunes',
                    'description'   => 'Piggy fortunes',
                    'content'       => 'Piggy fortunes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Piggy fortunes',
                    'description'   => 'Piggy fortunes',
                    'content'       => 'Piggy fortunes'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_pirates',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pirates Paradise',
                    'description'   => 'Pirates Paradise',
                    'content'       => 'Pirates Paradise'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pirates Paradise',
                    'description'   => 'Pirates Paradise',
                    'content'       => 'Pirates Paradise'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pirates Paradise',
                    'description'   => 'Pirates Paradise',
                    'content'       => 'Pirates Paradise'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_PollenNation',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pollen Nation',
                    'description'   => 'Pollen Nation',
                    'content'       => 'Pollen Nation'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pollen Nation',
                    'description'   => 'Pollen Nation',
                    'content'       => 'Pollen Nation'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pollen Nation',
                    'description'   => 'Pollen Nation',
                    'content'       => 'Pollen Nation'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyPrimePropertyV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Prime Property',
                    'description'   => 'Prime Property',
                    'content'       => 'Prime Property'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Prime Property',
                    'description'   => 'Prime Property',
                    'content'       => 'Prime Property'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Prime Property',
                    'description'   => 'Prime Property',
                    'content'       => 'Prime Property'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_rabbitinthehat',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rabbit In The Hat',
                    'description'   => 'Rabbit In The Hat',
                    'content'       => 'Rabbit In The Hat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rabbit In The Hat',
                    'description'   => 'Rabbit In The Hat',
                    'content'       => 'Rabbit In The Hat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rabbit In The Hat',
                    'description'   => 'Rabbit In The Hat',
                    'content'       => 'Rabbit In The Hat'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RacingForPinks',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Racing For Pinks',
                    'description'   => 'Racing For Pinks',
                    'content'       => 'Racing For Pinks'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Racing For Pinks',
                    'description'   => 'Racing For Pinks',
                    'content'       => 'Racing For Pinks'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Racing For Pinks',
                    'description'   => 'Racing For Pinks',
                    'content'       => 'Racing For Pinks'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RacingForPinksV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Racing For Pinks v90',
                    'description'   => 'Racing For Pinks v90',
                    'content'       => 'Racing For Pinks v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Racing For Pinks v90',
                    'description'   => 'Racing For Pinks v90',
                    'content'       => 'Racing For Pinks v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Racing For Pinks v90',
                    'description'   => 'Racing For Pinks v90',
                    'content'       => 'Racing For Pinks v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RamessesRiches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ramesses Riches',
                    'description'   => 'Ramesses Riches',
                    'content'       => 'Ramesses Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ramesses Riches',
                    'description'   => 'Ramesses Riches',
                    'content'       => 'Ramesses Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ramesses Riches',
                    'description'   => 'Ramesses Riches',
                    'content'       => 'Ramesses Riches'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyRapidReels',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rapid Reels',
                    'description'   => 'Rapid Reels',
                    'content'       => 'Rapid Reels'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rapid Reels',
                    'description'   => 'Rapid Reels',
                    'content'       => 'Rapid Reels'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rapid Reels',
                    'description'   => 'Rapid Reels',
                    'content'       => 'Rapid Reels'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_redhotdevil',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Red Hot Devil',
                    'description'   => 'Red Hot Devil',
                    'content'       => 'Red Hot Devil'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Red Hot Devil',
                    'description'   => 'Red Hot Devil',
                    'content'       => 'Red Hot Devil'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Red Hot Devil',
                    'description'   => 'Red Hot Devil',
                    'content'       => 'Red Hot Devil'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ReelGemsV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Gems v90',
                    'description'   => 'Reel Gems v90',
                    'content'       => 'Reel Gems v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Gems v90',
                    'description'   => 'Reel Gems v90',
                    'content'       => 'Reel Gems v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Gems v90',
                    'description'   => 'Reel Gems v90',
                    'content'       => 'Reel Gems v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ReelStrikeV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reel Strike v90',
                    'description'   => 'Reel Strike v90',
                    'content'       => 'Reel Strike v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reel Strike v90',
                    'description'   => 'Reel Strike v90',
                    'content'       => 'Reel Strike v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reel Strike v90',
                    'description'   => 'Reel Strike v90',
                    'content'       => 'Reel Strike v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_royce',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Reels Royce',
                    'description'   => 'Reels Royce',
                    'content'       => 'Reels Royce'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Reels Royce',
                    'description'   => 'Reels Royce',
                    'content'       => 'Reels Royce'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Reels Royce',
                    'description'   => 'Reels Royce',
                    'content'       => 'Reels Royce'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyRingsnRoses',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rings and Roses',
                    'description'   => 'Rings and Roses',
                    'content'       => 'Rings and Roses'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rings and Roses',
                    'description'   => 'Rings and Roses',
                    'content'       => 'Rings and Roses'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rings and Roses',
                    'description'   => 'Rings and Roses',
                    'content'       => 'Rings and Roses'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RiverofRiches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'River of Riches',
                    'description'   => 'River of Riches',
                    'content'       => 'River of Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'River of Riches',
                    'description'   => 'River of Riches',
                    'content'       => 'River of Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'River of Riches',
                    'description'   => 'River of Riches',
                    'content'       => 'River of Riches'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_robojack',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RoboJack',
                    'description'   => 'RoboJack',
                    'content'       => 'RoboJack'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RoboJack',
                    'description'   => 'RoboJack',
                    'content'       => 'RoboJack'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RoboJack',
                    'description'   => 'RoboJack',
                    'content'       => 'RoboJack'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RockTheBoat',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rock The Boat',
                    'description'   => 'Rock The Boat',
                    'content'       => 'Rock The Boat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rock The Boat',
                    'description'   => 'Rock The Boat',
                    'content'       => 'Rock The Boat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rock The Boat',
                    'description'   => 'Rock The Boat',
                    'content'       => 'Rock The Boat'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_romanriches',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Roman Riches',
                    'description'   => 'Roman Riches',
                    'content'       => 'Roman Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Roman Riches',
                    'description'   => 'Roman Riches',
                    'content'       => 'Roman Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Roman Riches',
                    'description'   => 'Roman Riches',
                    'content'       => 'Roman Riches'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubySamuraiSevens',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Samurai 7s',
                    'description'   => 'Samurai 7s',
                    'content'       => 'Samurai 7s'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Samurai 7s',
                    'description'   => 'Samurai 7s',
                    'content'       => 'Samurai 7s'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Samurai 7s',
                    'description'   => 'Samurai 7s',
                    'content'       => 'Samurai 7s'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SecretSanta',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Secret Santa',
                    'description'   => 'Secret Santa',
                    'content'       => 'Secret Santa'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Secret Santa',
                    'description'   => 'Secret Santa',
                    'content'       => 'Secret Santa'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Secret Santa',
                    'description'   => 'Secret Santa',
                    'content'       => 'Secret Santa'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Serenity',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Serenity',
                    'description'   => 'Serenity',
                    'content'       => 'Serenity'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Serenity',
                    'description'   => 'Serenity',
                    'content'       => 'Serenity'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Serenity',
                    'description'   => 'Serenity',
                    'content'       => 'Serenity'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_oceans',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Seven Oceans',
                    'description'   => 'Seven Oceans',
                    'content'       => 'Seven Oceans'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Seven Oceans',
                    'description'   => 'Seven Oceans',
                    'content'       => 'Seven Oceans'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Seven Oceans',
                    'description'   => 'Seven Oceans',
                    'content'       => 'Seven Oceans'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SilverFangV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Silver Fang v90',
                    'description'   => 'Silver Fang v90',
                    'content'       => 'Silver Fang v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Silver Fang v90',
                    'description'   => 'Silver Fang v90',
                    'content'       => 'Silver Fang v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Silver Fang v90',
                    'description'   => 'Silver Fang v90',
                    'content'       => 'Silver Fang v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubySizzlingScorpions',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sizzling Scorpions',
                    'description'   => 'Sizzling Scorpions',
                    'content'       => 'Sizzling Scorpions'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sizzling Scorpions',
                    'description'   => 'Sizzling Scorpions',
                    'content'       => 'Sizzling Scorpions'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sizzling Scorpions',
                    'description'   => 'Sizzling Scorpions',
                    'content'       => 'Sizzling Scorpions'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SkullDuggery',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Skull Duggery',
                    'description'   => 'Skull Duggery',
                    'content'       => 'Skull Duggery'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Skull Duggery',
                    'description'   => 'Skull Duggery',
                    'content'       => 'Skull Duggery'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Skull Duggery',
                    'description'   => 'Skull Duggery',
                    'content'       => 'Skull Duggery'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubySoccerSafari',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Soccer Safari',
                    'description'   => 'Soccer Safari',
                    'content'       => 'Soccer Safari'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Soccer Safari',
                    'description'   => 'Soccer Safari',
                    'content'       => 'Soccer Safari'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Soccer Safari',
                    'description'   => 'Soccer Safari',
                    'content'       => 'Soccer Safari'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubySonicBoom',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sonic Boom',
                    'description'   => 'Sonic Boom',
                    'content'       => 'Sonic Boom'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sonic Boom',
                    'description'   => 'Sonic Boom',
                    'content'       => 'Sonic Boom'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sonic Boom',
                    'description'   => 'Sonic Boom',
                    'content'       => 'Sonic Boom'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SovereignOfTheSevenSeas',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sovereign Of The Seven Seas',
                    'description'   => 'Sovereign Of The Seven Seas',
                    'content'       => 'Sovereign Of The Seven Seas'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sovereign Of The Seven Seas',
                    'description'   => 'Sovereign Of The Seven Seas',
                    'content'       => 'Sovereign Of The Seven Seas'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sovereign Of The Seven Seas',
                    'description'   => 'Sovereign Of The Seven Seas',
                    'content'       => 'Sovereign Of The Seven Seas'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_Spectacular',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Spectacular Wheel of Wealth',
                    'description'   => 'Spectacular Wheel of Wealth',
                    'content'       => 'Spectacular Wheel of Wealth'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Spectacular Wheel of Wealth',
                    'description'   => 'Spectacular Wheel of Wealth',
                    'content'       => 'Spectacular Wheel of Wealth'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Spectacular Wheel of Wealth',
                    'description'   => 'Spectacular Wheel of Wealth',
                    'content'       => 'Spectacular Wheel of Wealth'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubySpellBound',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Spell Bound',
                    'description'   => 'Spell Bound',
                    'content'       => 'Spell Bound'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Spell Bound',
                    'description'   => 'Spell Bound',
                    'content'       => 'Spell Bound'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Spell Bound',
                    'description'   => 'Spell Bound',
                    'content'       => 'Spell Bound'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SpringBreakV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Spring Break v90',
                    'description'   => 'Spring Break v90',
                    'content'       => 'Spring Break v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Spring Break v90',
                    'description'   => 'Spring Break v90',
                    'content'       => 'Spring Break v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Spring Break v90',
                    'description'   => 'Spring Break v90',
                    'content'       => 'Spring Break v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_StarlightKissV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Starlight Kiss v90',
                    'description'   => 'Starlight Kiss v90',
                    'content'       => 'Starlight Kiss v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Starlight Kiss v90',
                    'description'   => 'Starlight Kiss v90',
                    'content'       => 'Starlight Kiss v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Starlight Kiss v90',
                    'description'   => 'Starlight Kiss v90',
                    'content'       => 'Starlight Kiss v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_StarscapeV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Starscape',
                    'description'   => 'Starscape',
                    'content'       => 'Starscape'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Starscape',
                    'description'   => 'Starscape',
                    'content'       => 'Starscape'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Starscape',
                    'description'   => 'Starscape',
                    'content'       => 'Starscape'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SterlingSilver3D',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sterling Silver 3D Stereo',
                    'description'   => 'Sterling Silver 3D Stereo',
                    'content'       => 'Sterling Silver 3D Stereo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sterling Silver 3D Stereo',
                    'description'   => 'Sterling Silver 3D Stereo',
                    'content'       => 'Sterling Silver 3D Stereo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sterling Silver 3D Stereo',
                    'description'   => 'Sterling Silver 3D Stereo',
                    'content'       => 'Sterling Silver 3D Stereo'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SunQuestV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sun Quest v90',
                    'description'   => 'Sun Quest v90',
                    'content'       => 'Sun Quest v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sun Quest v90',
                    'description'   => 'Sun Quest v90',
                    'content'       => 'Sun Quest v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sun Quest v90',
                    'description'   => 'Sun Quest v90',
                    'content'       => 'Sun Quest v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_SweetHarvest',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sweet Harvest',
                    'description'   => 'Sweet Harvest',
                    'content'       => 'Sweet Harvest'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sweet Harvest',
                    'description'   => 'Sweet Harvest',
                    'content'       => 'Sweet Harvest'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sweet Harvest',
                    'description'   => 'Sweet Harvest',
                    'content'       => 'Sweet Harvest'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_TheBermudaMysteries',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Bermuda Mysteries',
                    'description'   => 'The Bermuda Mysteries',
                    'content'       => 'The Bermuda Mysteries'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Bermuda Mysteries',
                    'description'   => 'The Bermuda Mysteries',
                    'content'       => 'The Bermuda Mysteries'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Bermuda Mysteries',
                    'description'   => 'The Bermuda Mysteries',
                    'content'       => 'The Bermuda Mysteries'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_TheFinerReelsOfLifeV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Finer Reels of Life v90',
                    'description'   => 'The Finer Reels of Life v90',
                    'content'       => 'The Finer Reels of Life v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Finer Reels of Life v90',
                    'description'   => 'The Finer Reels of Life v90',
                    'content'       => 'The Finer Reels of Life v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Finer Reels of Life v90',
                    'description'   => 'The Finer Reels of Life v90',
                    'content'       => 'The Finer Reels of Life v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ThroneOfEgypt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Throne Of Egypt',
                    'description'   => 'Throne Of Egypt',
                    'content'       => 'Throne Of Egypt'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Throne Of Egypt',
                    'description'   => 'Throne Of Egypt',
                    'content'       => 'Throne Of Egypt'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Throne Of Egypt',
                    'description'   => 'Throne Of Egypt',
                    'content'       => 'Throne Of Egypt'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ThroneOfEgyptv90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Throne Of Egypt v90',
                    'description'   => 'Throne Of Egypt v90',
                    'content'       => 'Throne Of Egypt v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Throne Of Egypt v90',
                    'description'   => 'Throne Of Egypt v90',
                    'content'       => 'Throne Of Egypt v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Throne Of Egypt v90',
                    'description'   => 'Throne Of Egypt v90',
                    'content'       => 'Throne Of Egypt v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ThunderStruck2V90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'ThunderStruck II v90',
                    'description'   => 'ThunderStruck II v90',
                    'content'       => 'ThunderStruck II v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'ThunderStruck II v90',
                    'description'   => 'ThunderStruck II v90',
                    'content'       => 'ThunderStruck II v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'ThunderStruck II v90',
                    'description'   => 'ThunderStruck II v90',
                    'content'       => 'ThunderStruck II v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_ThunderStruckV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Thunderstruck v90',
                    'description'   => 'Thunderstruck v90',
                    'content'       => 'Thunderstruck v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Thunderstruck v90',
                    'description'   => 'Thunderstruck v90',
                    'content'       => 'Thunderstruck v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Thunderstruck v90',
                    'description'   => 'Thunderstruck v90',
                    'content'       => 'Thunderstruck v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_TigerMoon',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tiger Moon',
                    'description'   => 'Tiger Moon',
                    'content'       => 'Tiger Moon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tiger Moon',
                    'description'   => 'Tiger Moon',
                    'content'       => 'Tiger Moon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tiger Moon',
                    'description'   => 'Tiger Moon',
                    'content'       => 'Tiger Moon'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_TombRaiderV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tomb Raider v90',
                    'description'   => 'Tomb Raider v90',
                    'content'       => 'Tomb Raider v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tomb Raider v90',
                    'description'   => 'Tomb Raider v90',
                    'content'       => 'Tomb Raider v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tomb Raider v90',
                    'description'   => 'Tomb Raider v90',
                    'content'       => 'Tomb Raider v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_TootinCarMan',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tootin Car Man',
                    'description'   => 'Tootin Car Man',
                    'content'       => 'Tootin Car Man'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tootin Car Man',
                    'description'   => 'Tootin Car Man',
                    'content'       => 'Tootin Car Man'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tootin Car Man',
                    'description'   => 'Tootin Car Man',
                    'content'       => 'Tootin Car Man'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyTotemTreasureV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Totem Treasure',
                    'description'   => 'Totem Treasure',
                    'content'       => 'Totem Treasure'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Totem Treasure',
                    'description'   => 'Totem Treasure',
                    'content'       => 'Totem Treasure'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Totem Treasure',
                    'description'   => 'Totem Treasure',
                    'content'       => 'Totem Treasure'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_trickortreat',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Trick Or Treat',
                    'description'   => 'Trick Or Treat',
                    'content'       => 'Trick Or Treat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Trick Or Treat',
                    'description'   => 'Trick Or Treat',
                    'content'       => 'Trick Or Treat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Trick Or Treat',
                    'description'   => 'Trick Or Treat',
                    'content'       => 'Trick Or Treat'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_TripleMagic',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Triple Magic',
                    'description'   => 'Triple Magic',
                    'content'       => 'Triple Magic'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Triple Magic',
                    'description'   => 'Triple Magic',
                    'content'       => 'Triple Magic'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Triple Magic',
                    'description'   => 'Triple Magic',
                    'content'       => 'Triple Magic'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyTwister',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Twister',
                    'description'   => 'Twister',
                    'content'       => 'Twister'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Twister',
                    'description'   => 'Twister',
                    'content'       => 'Twister'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Twister',
                    'description'   => 'Twister',
                    'content'       => 'Twister'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_UntamedBengalTiger',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Bengal Tiger',
                    'description'   => 'Untamed - Bengal Tiger',
                    'content'       => 'Untamed - Bengal Tiger'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Bengal Tiger',
                    'description'   => 'Untamed - Bengal Tiger',
                    'content'       => 'Untamed - Bengal Tiger'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Bengal Tiger',
                    'description'   => 'Untamed - Bengal Tiger',
                    'content'       => 'Untamed - Bengal Tiger'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_UntamedBengalTigerV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Bengal Tiger v90',
                    'description'   => 'Untamed - Bengal Tiger v90',
                    'content'       => 'Untamed - Bengal Tiger v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Bengal Tiger v90',
                    'description'   => 'Untamed - Bengal Tiger v90',
                    'content'       => 'Untamed - Bengal Tiger v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Bengal Tiger v90',
                    'description'   => 'Untamed - Bengal Tiger v90',
                    'content'       => 'Untamed - Bengal Tiger v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_UntamedCrownedEagle',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Crowned Eagle',
                    'description'   => 'Untamed - Crowned Eagle',
                    'content'       => 'Untamed - Crowned Eagle'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Crowned Eagle',
                    'description'   => 'Untamed - Crowned Eagle',
                    'content'       => 'Untamed - Crowned Eagle'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Crowned Eagle',
                    'description'   => 'Untamed - Crowned Eagle',
                    'content'       => 'Untamed - Crowned Eagle'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_UntamedCrownedEagleV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Crowned Eagle v90',
                    'description'   => 'Untamed - Crowned Eagle v90',
                    'content'       => 'Untamed - Crowned Eagle v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Crowned Eagle v90',
                    'description'   => 'Untamed - Crowned Eagle v90',
                    'content'       => 'Untamed - Crowned Eagle v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Crowned Eagle v90',
                    'description'   => 'Untamed - Crowned Eagle v90',
                    'content'       => 'Untamed - Crowned Eagle v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_UntamedGiantPandav90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Giant Panda v90',
                    'description'   => 'Untamed - Giant Panda v90',
                    'content'       => 'Untamed - Giant Panda v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Giant Panda v90',
                    'description'   => 'Untamed - Giant Panda v90',
                    'content'       => 'Untamed - Giant Panda v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Giant Panda v90',
                    'description'   => 'Untamed - Giant Panda v90',
                    'content'       => 'Untamed - Giant Panda v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_untamedwolfpack',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Wolf Pack',
                    'description'   => 'Untamed - Wolf Pack',
                    'content'       => 'Untamed - Wolf Pack'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Wolf Pack',
                    'description'   => 'Untamed - Wolf Pack',
                    'content'       => 'Untamed - Wolf Pack'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Wolf Pack',
                    'description'   => 'Untamed - Wolf Pack',
                    'content'       => 'Untamed - Wolf Pack'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_UntamedWolfPackV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Untamed - Wolf Pack v90',
                    'description'   => 'Untamed - Wolf Pack v90',
                    'content'       => 'Untamed - Wolf Pack v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Untamed - Wolf Pack v90',
                    'description'   => 'Untamed - Wolf Pack v90',
                    'content'       => 'Untamed - Wolf Pack v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Untamed - Wolf Pack v90',
                    'description'   => 'Untamed - Wolf Pack v90',
                    'content'       => 'Untamed - Wolf Pack v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_VinylCountDownV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Vinyl Countdown v90',
                    'description'   => 'Vinyl Countdown v90',
                    'content'       => 'Vinyl Countdown v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Vinyl Countdown v90',
                    'description'   => 'Vinyl Countdown v90',
                    'content'       => 'Vinyl Countdown v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Vinyl Countdown v90',
                    'description'   => 'Vinyl Countdown v90',
                    'content'       => 'Vinyl Countdown v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyWasabiSan',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wasabi-San',
                    'description'   => 'Wasabi-San',
                    'content'       => 'Wasabi-San'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wasabi-San',
                    'description'   => 'Wasabi-San',
                    'content'       => 'Wasabi-San'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wasabi-San',
                    'description'   => 'Wasabi-San',
                    'content'       => 'Wasabi-San'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_westernfrontier',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Western Frontier',
                    'description'   => 'Western Frontier',
                    'content'       => 'Western Frontier'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Western Frontier',
                    'description'   => 'Western Frontier',
                    'content'       => 'Western Frontier'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Western Frontier',
                    'description'   => 'Western Frontier',
                    'content'       => 'Western Frontier'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_WhatAHootV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'What A Hoot v90',
                    'description'   => 'What A Hoot v90',
                    'content'       => 'What A Hoot v90'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'What A Hoot v90',
                    'description'   => 'What A Hoot v90',
                    'content'       => 'What A Hoot v90'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'What A Hoot v90',
                    'description'   => 'What A Hoot v90',
                    'content'       => 'What A Hoot v90'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_WhatonEarth',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'What on Earth',
                    'description'   => 'What on Earth',
                    'content'       => 'What on Earth'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'What on Earth',
                    'description'   => 'What on Earth',
                    'content'       => 'What on Earth'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'What on Earth',
                    'description'   => 'What on Earth',
                    'content'       => 'What on Earth'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_WheelOfWealthSEV90',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wheel Of Wealth',
                    'description'   => 'Wheel Of Wealth',
                    'content'       => 'Wheel Of Wealth'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wheel Of Wealth',
                    'description'   => 'Wheel Of Wealth',
                    'content'       => 'Wheel Of Wealth'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wheel Of Wealth',
                    'description'   => 'Wheel Of Wealth',
                    'content'       => 'Wheel Of Wealth'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_WildCatch',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wild Catch',
                    'description'   => 'Wild Catch',
                    'content'       => 'Wild Catch'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wild Catch',
                    'description'   => 'Wild Catch',
                    'content'       => 'Wild Catch'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wild Catch',
                    'description'   => 'Wild Catch',
                    'content'       => 'Wild Catch'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_wwizards',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Winning Wizards',
                    'description'   => 'Winning Wizards',
                    'content'       => 'Winning Wizards'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Winning Wizards',
                    'description'   => 'Winning Wizards',
                    'content'       => 'Winning Wizards'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Winning Wizards',
                    'description'   => 'Winning Wizards',
                    'content'       => 'Winning Wizards'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyWitchesWealth',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Witches Wealth',
                    'description'   => 'Witches Wealth',
                    'content'       => 'Witches Wealth'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Witches Wealth',
                    'description'   => 'Witches Wealth',
                    'content'       => 'Witches Wealth'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Witches Wealth',
                    'description'   => 'Witches Wealth',
                    'content'       => 'Witches Wealth'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_RubyWorldCupMania',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'World Cup Mania',
                    'description'   => 'World Cup Mania',
                    'content'       => 'World Cup Mania'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'World Cup Mania',
                    'description'   => 'World Cup Mania',
                    'content'       => 'World Cup Mania'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'World Cup Mania',
                    'description'   => 'World Cup Mania',
                    'content'       => 'World Cup Mania'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'MGS_Slot',
            'code'          => 'SMG_zebra',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Zany Zebra',
                    'description'   => 'Zany Zebra',
                    'content'       => 'Zany Zebra'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Zany Zebra',
                    'description'   => 'Zany Zebra',
                    'content'       => 'Zany Zebra'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Zany Zebra',
                    'description'   => 'Zany Zebra',
                    'content'       => 'Zany Zebra'
                ],
            ],
            'devices'       => [1],
        ],

    ];


    foreach ($games as $game) {
        Game::query()->create($game);
    }

    #Changing config
    $configs = [
        [
            'code'          => 'mgs_last_bet_id',
            'name'          => 'MGS mgs_last_bet_id',
            'remark'        => 'MGS mgs_last_bet_id',
            'is_front_show' => false,
            'type'          => 'string',
            'value'         => null,
        ],
    ];
    ChangingConfig::insert($configs);

    #Game Platform Games End


    }
}
