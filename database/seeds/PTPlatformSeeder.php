<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;
use App\Models\ChangingConfig;

class PTPlatformSeeder extends Seeder
{

    const CODE = 'PT';
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
                    'interval'              => 5, # 间隔时间
                    'delay'                 => 10, # 延迟时间
                    'offset'                => 5, # 时间跨度
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
                'interval'              => 5, # 间隔时间
                'delay'                 => 10, # 延迟时间
                'offset'                => 5, # 时间跨度
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
            'code'          => 'PT_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'PT Slot',
                    'description' => 'PT Slot',
                    'content'     => 'PT Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'PT Slot',
                    'description' => 'PT Slot',
                    'content'     => 'PT Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'PT Slot',
                    'description' => 'PT Slot',
                    'content'     => 'PT Slot',
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
        'product_code'  => 'PT_Slot',
        'code'          => 'pop_sw_8tr1qu_skw',
        'type'          => GamePlatformProduct::TYPE_SLOT,
        'currencies'    => ['USD', 'VND', 'THB'],
        'languages'    => [
            [
                'language'      => 'vi-VN',
                'name'          => '8 Treasures 1 Queen',
                'description'   => '8 Treasures 1 Queen',
                'content'       => '8 Treasures 1 Queen'
            ],
            [
                'language'      => 'th',
                'name'          => '8 Treasures 1 Queen',
                'description'   => '8 Treasures 1 Queen',
                'content'       => '8 Treasures 1 Queen'
            ],
            [
                'language'      => 'en-US',
                'name'          => '8 Treasures 1 Queen',
                'description'   => '8 Treasures 1 Queen',
                'content'       => '8 Treasures 1 Queen'
            ],
        ],
        'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hb',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'A Night Out',
                    'description'   => 'A Night Out',
                    'content'       => 'A Night Out'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'A Night Out',
                    'description'   => 'A Night Out',
                    'content'       => 'A Night Out'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'A Night Out',
                    'description'   => 'A Night Out',
                    'content'       => 'A Night Out'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashadv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Adventures in Wonderland',
                    'description'   => 'Adventures in Wonderland',
                    'content'       => 'Adventures in Wonderland'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Adventures in Wonderland',
                    'description'   => 'Adventures in Wonderland',
                    'content'       => 'Adventures in Wonderland'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Adventures in Wonderland',
                    'description'   => 'Adventures in Wonderland',
                    'content'       => 'Adventures in Wonderland'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ftsis',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age of the Gods : Fate Sisters',
                    'description'   => 'Age of the Gods : Fate Sisters',
                    'content'       => 'Age of the Gods : Fate Sisters'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age of the Gods : Fate Sisters',
                    'description'   => 'Age of the Gods : Fate Sisters',
                    'content'       => 'Age of the Gods : Fate Sisters'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age of the Gods : Fate Sisters',
                    'description'   => 'Age of the Gods : Fate Sisters',
                    'content'       => 'Age of the Gods : Fate Sisters'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'athn',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age of the Gods : Goddess of Wisdom',
                    'description'   => 'Age of the Gods : Goddess of Wisdom',
                    'content'       => 'Age of the Gods : Goddess of Wisdom'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age of the Gods : Goddess of Wisdom',
                    'description'   => 'Age of the Gods : Goddess of Wisdom',
                    'content'       => 'Age of the Gods : Goddess of Wisdom'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age of the Gods : Goddess of Wisdom',
                    'description'   => 'Age of the Gods : Goddess of Wisdom',
                    'content'       => 'Age of the Gods : Goddess of Wisdom'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'furf',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age of the Gods: Furious Four',
                    'description'   => 'Age of the Gods: Furious Four',
                    'content'       => 'Age of the Gods: Furious Four'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age of the Gods: Furious Four',
                    'description'   => 'Age of the Gods: Furious Four',
                    'content'       => 'Age of the Gods: Furious Four'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age of the Gods: Furious Four',
                    'description'   => 'Age of the Gods: Furious Four',
                    'content'       => 'Age of the Gods: Furious Four'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'zeus',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age of the Gods: King of Olympus',
                    'description'   => 'Age of the Gods: King of Olympus',
                    'content'       => 'Age of the Gods: King of Olympus'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age of the Gods: King of Olympus',
                    'description'   => 'Age of the Gods: King of Olympus',
                    'content'       => 'Age of the Gods: King of Olympus'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age of the Gods: King of Olympus',
                    'description'   => 'Age of the Gods: King of Olympus',
                    'content'       => 'Age of the Gods: King of Olympus'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hrcls',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age of the Gods: Prince of Olympus',
                    'description'   => 'Age of the Gods: Prince of Olympus',
                    'content'       => 'Age of the Gods: Prince of Olympus'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age of the Gods: Prince of Olympus',
                    'description'   => 'Age of the Gods: Prince of Olympus',
                    'content'       => 'Age of the Gods: Prince of Olympus'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age of the Gods: Prince of Olympus',
                    'description'   => 'Age of the Gods: Prince of Olympus',
                    'content'       => 'Age of the Gods: Prince of Olympus'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashamw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Amazon Wild',
                    'description'   => 'Amazon Wild',
                    'content'       => 'Amazon Wild'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Amazon Wild',
                    'description'   => 'Amazon Wild',
                    'content'       => 'Amazon Wild'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Amazon Wild',
                    'description'   => 'Amazon Wild',
                    'content'       => 'Amazon Wild'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'arc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Archer',
                    'description'   => 'Archer',
                    'content'       => 'Archer'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Archer',
                    'description'   => 'Archer',
                    'content'       => 'Archer'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Archer',
                    'description'   => 'Archer',
                    'content'       => 'Archer'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'art',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Arctic Treasure',
                    'description'   => 'Arctic Treasure',
                    'content'       => 'Arctic Treasure'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Arctic Treasure',
                    'description'   => 'Arctic Treasure',
                    'content'       => 'Arctic Treasure'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Arctic Treasure',
                    'description'   => 'Arctic Treasure',
                    'content'       => 'Arctic Treasure'
                ],
            ],
            'devices'       => [2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_swaf_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Asian Fantasy',
                    'description'   => 'Asian Fantasy',
                    'content'       => 'Asian Fantasy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Asian Fantasy',
                    'description'   => 'Asian Fantasy',
                    'content'       => 'Asian Fantasy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Asian Fantasy',
                    'description'   => 'Asian Fantasy',
                    'content'       => 'Asian Fantasy'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsatq',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Atlantis Queen',
                    'description'   => 'Atlantis Queen',
                    'content'       => 'Atlantis Queen'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Atlantis Queen',
                    'description'   => 'Atlantis Queen',
                    'content'       => 'Atlantis Queen'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Atlantis Queen',
                    'description'   => 'Atlantis Queen',
                    'content'       => 'Atlantis Queen'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'bs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bai Shi',
                    'description'   => 'Bai Shi',
                    'content'       => 'Bai Shi'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bai Shi',
                    'description'   => 'Bai Shi',
                    'content'       => 'Bai Shi'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bai Shi',
                    'description'   => 'Bai Shi',
                    'content'       => 'Bai Shi'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'bl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Beach Life',
                    'description'   => 'Beach Life',
                    'content'       => 'Beach Life'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Beach Life',
                    'description'   => 'Beach Life',
                    'content'       => 'Beach Life'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Beach Life',
                    'description'   => 'Beach Life',
                    'content'       => 'Beach Life'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'bt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bermuda Triangle',
                    'description'   => 'Bermuda Triangle',
                    'content'       => 'Bermuda Triangle'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bermuda Triangle',
                    'description'   => 'Bermuda Triangle',
                    'content'       => 'Bermuda Triangle'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bermuda Triangle',
                    'description'   => 'Bermuda Triangle',
                    'content'       => 'Bermuda Triangle'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'bob',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bonus Bears',
                    'description'   => 'Bonus Bears',
                    'content'       => 'Bonus Bears'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bonus Bears',
                    'description'   => 'Bonus Bears',
                    'content'       => 'Bonus Bears'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bonus Bears',
                    'description'   => 'Bonus Bears',
                    'content'       => 'Bonus Bears'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashbob',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bounty of the Beanstalk',
                    'description'   => 'Bounty of the Beanstalk',
                    'content'       => 'Bounty of the Beanstalk'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bounty of the Beanstalk',
                    'description'   => 'Bounty of the Beanstalk',
                    'content'       => 'Bounty of the Beanstalk'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bounty of the Beanstalk',
                    'description'   => 'Bounty of the Beanstalk',
                    'content'       => 'Bounty of the Beanstalk'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'bfb',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Buffalo Blitz',
                    'description'   => 'Buffalo Blitz',
                    'content'       => 'Buffalo Blitz'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Buffalo Blitz',
                    'description'   => 'Buffalo Blitz',
                    'content'       => 'Buffalo Blitz'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Buffalo Blitz',
                    'description'   => 'Buffalo Blitz',
                    'content'       => 'Buffalo Blitz'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ct',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Captain\'s Treasure',
                    'description'   => 'Captain\'s Treasure',
                    'content'       => 'Captain\'s Treasure'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Captain\'s Treasure',
                    'description'   => 'Captain\'s Treasure',
                    'content'       => 'Captain\'s Treasure'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Captain\'s Treasure',
                    'description'   => 'Captain\'s Treasure',
                    'content'       => 'Captain\'s Treasure'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ctp2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Captain\'s Treasure Pro',
                    'description'   => 'Captain\'s Treasure Pro',
                    'content'       => 'Captain\'s Treasure Pro'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Captain\'s Treasure Pro',
                    'description'   => 'Captain\'s Treasure Pro',
                    'content'       => 'Captain\'s Treasure Pro'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Captain\'s Treasure Pro',
                    'description'   => 'Captain\'s Treasure Pro',
                    'content'       => 'Captain\'s Treasure Pro'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'cashfi',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cash Fish',
                    'description'   => 'Cash Fish',
                    'content'       => 'Cash Fish'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cash Fish',
                    'description'   => 'Cash Fish',
                    'content'       => 'Cash Fish'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cash Fish',
                    'description'   => 'Cash Fish',
                    'content'       => 'Cash Fish'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ctiv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cat In Vegas',
                    'description'   => 'Cat In Vegas',
                    'content'       => 'Cat In Vegas'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cat In Vegas',
                    'description'   => 'Cat In Vegas',
                    'content'       => 'Cat In Vegas'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cat In Vegas',
                    'description'   => 'Cat In Vegas',
                    'content'       => 'Cat In Vegas'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'catqc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cat Queen',
                    'description'   => 'Cat Queen',
                    'content'       => 'Cat Queen'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cat Queen',
                    'description'   => 'Cat Queen',
                    'content'       => 'Cat Queen'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cat Queen',
                    'description'   => 'Cat Queen',
                    'content'       => 'Cat Queen'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'chao',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Chaoji 888',
                    'description'   => 'Chaoji 888',
                    'content'       => 'Chaoji 888'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Chaoji 888',
                    'description'   => 'Chaoji 888',
                    'content'       => 'Chaoji 888'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Chaoji 888',
                    'description'   => 'Chaoji 888',
                    'content'       => 'Chaoji 888'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'chl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cherry Love',
                    'description'   => 'Cherry Love',
                    'content'       => 'Cherry Love'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cherry Love',
                    'description'   => 'Cherry Love',
                    'content'       => 'Cherry Love'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cherry Love',
                    'description'   => 'Cherry Love',
                    'content'       => 'Cherry Love'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashcpl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Chests of Plenty',
                    'description'   => 'Chests of Plenty',
                    'content'       => 'Chests of Plenty'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Chests of Plenty',
                    'description'   => 'Chests of Plenty',
                    'content'       => 'Chests of Plenty'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Chests of Plenty',
                    'description'   => 'Chests of Plenty',
                    'content'       => 'Chests of Plenty'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'cm',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Chinese Kitchen',
                    'description'   => 'Chinese Kitchen',
                    'content'       => 'Chinese Kitchen'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Chinese Kitchen',
                    'description'   => 'Chinese Kitchen',
                    'content'       => 'Chinese Kitchen'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Chinese Kitchen',
                    'description'   => 'Chinese Kitchen',
                    'content'       => 'Chinese Kitchen'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'scs',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Classic Slots Scratch',
                    'description'   => 'Classic Slots Scratch',
                    'content'       => 'Classic Slots Scratch'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Classic Slots Scratch',
                    'description'   => 'Classic Slots Scratch',
                    'content'       => 'Classic Slots Scratch'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Classic Slots Scratch',
                    'description'   => 'Classic Slots Scratch',
                    'content'       => 'Classic Slots Scratch'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtscnb',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cops N\' Bandits',
                    'description'   => 'Cops N\' Bandits',
                    'content'       => 'Cops N\' Bandits'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cops N\' Bandits',
                    'description'   => 'Cops N\' Bandits',
                    'content'       => 'Cops N\' Bandits'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cops N\' Bandits',
                    'description'   => 'Cops N\' Bandits',
                    'content'       => 'Cops N\' Bandits'
                ],
            ],
            'devices'       => [2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtscbl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Cowboys & Aliens',
                    'description'   => 'Cowboys & Aliens',
                    'content'       => 'Cowboys & Aliens'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Cowboys & Aliens',
                    'description'   => 'Cowboys & Aliens',
                    'content'       => 'Cowboys & Aliens'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Cowboys & Aliens',
                    'description'   => 'Cowboys & Aliens',
                    'content'       => 'Cowboys & Aliens'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'c7',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Crazy 7',
                    'description'   => 'Crazy 7',
                    'content'       => 'Crazy 7'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Crazy 7',
                    'description'   => 'Crazy 7',
                    'content'       => 'Crazy 7'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Crazy 7',
                    'description'   => 'Crazy 7',
                    'content'       => 'Crazy 7'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsdrdv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Daring Dave & the Eye of Ra',
                    'description'   => 'Daring Dave & the Eye of Ra',
                    'content'       => 'Daring Dave & the Eye of Ra'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Daring Dave & the Eye of Ra',
                    'description'   => 'Daring Dave & the Eye of Ra',
                    'content'       => 'Daring Dave & the Eye of Ra'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Daring Dave & the Eye of Ra',
                    'description'   => 'Daring Dave & the Eye of Ra',
                    'content'       => 'Daring Dave & the Eye of Ra'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'dt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Desert Treasure',
                    'description'   => 'Desert Treasure',
                    'content'       => 'Desert Treasure'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Desert Treasure',
                    'description'   => 'Desert Treasure',
                    'content'       => 'Desert Treasure'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Desert Treasure',
                    'description'   => 'Desert Treasure',
                    'content'       => 'Desert Treasure'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'dt2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Desert Treasure II',
                    'description'   => 'Desert Treasure II',
                    'content'       => 'Desert Treasure II'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Desert Treasure II',
                    'description'   => 'Desert Treasure II',
                    'content'       => 'Desert Treasure II'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Desert Treasure II',
                    'description'   => 'Desert Treasure II',
                    'content'       => 'Desert Treasure II'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'dnr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dolphin Reef',
                    'description'   => 'Dolphin Reef',
                    'content'       => 'Dolphin Reef'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dolphin Reef',
                    'description'   => 'Dolphin Reef',
                    'content'       => 'Dolphin Reef'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dolphin Reef',
                    'description'   => 'Dolphin Reef',
                    'content'       => 'Dolphin Reef'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'dlm',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dr Love More',
                    'description'   => 'Dr Love More',
                    'content'       => 'Dr Love More'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dr Love More',
                    'description'   => 'Dr Love More',
                    'content'       => 'Dr Love More'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dr Love More',
                    'description'   => 'Dr Love More',
                    'content'       => 'Dr Love More'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsdgk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Kingdom',
                    'description'   => 'Dragon Kingdom',
                    'content'       => 'Dragon Kingdom'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Kingdom',
                    'description'   => 'Dragon Kingdom',
                    'content'       => 'Dragon Kingdom'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Kingdom',
                    'description'   => 'Dragon Kingdom',
                    'content'       => 'Dragon Kingdom'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'eas',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Easter Surprise',
                    'description'   => 'Easter Surprise',
                    'content'       => 'Easter Surprise'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Easter Surprise',
                    'description'   => 'Easter Surprise',
                    'content'       => 'Easter Surprise'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Easter Surprise',
                    'description'   => 'Easter Surprise',
                    'content'       => 'Easter Surprise'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'egspin',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Egypt Spin',
                    'description'   => 'Egypt Spin',
                    'content'       => 'Egypt Spin'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Egypt Spin',
                    'description'   => 'Egypt Spin',
                    'content'       => 'Egypt Spin'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Egypt Spin',
                    'description'   => 'Egypt Spin',
                    'content'       => 'Egypt Spin'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'esmk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Esmeralda',
                    'description'   => 'Esmeralda',
                    'content'       => 'Esmeralda'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Esmeralda',
                    'description'   => 'Esmeralda',
                    'content'       => 'Esmeralda'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Esmeralda',
                    'description'   => 'Esmeralda',
                    'content'       => 'Esmeralda'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashfta',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fairest of Them All',
                    'description'   => 'Fairest of Them All',
                    'content'       => 'Fairest of Them All'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fairest of Them All',
                    'description'   => 'Fairest of Them All',
                    'content'       => 'Fairest of Them All'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fairest of Them All',
                    'description'   => 'Fairest of Them All',
                    'content'       => 'Fairest of Them All'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fcgz',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fei Cui Gong Zhu',
                    'description'   => 'Fei Cui Gong Zhu',
                    'content'       => 'Fei Cui Gong Zhu'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fei Cui Gong Zhu',
                    'description'   => 'Fei Cui Gong Zhu',
                    'content'       => 'Fei Cui Gong Zhu'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fei Cui Gong Zhu',
                    'description'   => 'Fei Cui Gong Zhu',
                    'content'       => 'Fei Cui Gong Zhu'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsflzt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fei Long Zai Tian',
                    'description'   => 'Fei Long Zai Tian',
                    'content'       => 'Fei Long Zai Tian'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fei Long Zai Tian',
                    'description'   => 'Fei Long Zai Tian',
                    'content'       => 'Fei Long Zai Tian'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fei Long Zai Tian',
                    'description'   => 'Fei Long Zai Tian',
                    'content'       => 'Fei Long Zai Tian'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fkmj',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Feng Kuang Ma Jiang',
                    'description'   => 'Feng Kuang Ma Jiang',
                    'content'       => 'Feng Kuang Ma Jiang'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Feng Kuang Ma Jiang',
                    'description'   => 'Feng Kuang Ma Jiang',
                    'content'       => 'Feng Kuang Ma Jiang'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Feng Kuang Ma Jiang',
                    'description'   => 'Feng Kuang Ma Jiang',
                    'content'       => 'Feng Kuang Ma Jiang'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ftg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Five Tiger Generals',
                    'description'   => 'Five Tiger Generals',
                    'content'       => 'Five Tiger Generals'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Five Tiger Generals',
                    'description'   => 'Five Tiger Generals',
                    'content'       => 'Five Tiger Generals'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Five Tiger Generals',
                    'description'   => 'Five Tiger Generals',
                    'content'       => 'Five Tiger Generals'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsfc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Football Carnival',
                    'description'   => 'Football Carnival',
                    'content'       => 'Football Carnival'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Football Carnival',
                    'description'   => 'Football Carnival',
                    'content'       => 'Football Carnival'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Football Carnival',
                    'description'   => 'Football Carnival',
                    'content'       => 'Football Carnival'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fbr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Football Rules',
                    'description'   => 'Football Rules',
                    'content'       => 'Football Rules'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Football Rules',
                    'description'   => 'Football Rules',
                    'content'       => 'Football Rules'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Football Rules',
                    'description'   => 'Football Rules',
                    'content'       => 'Football Rules'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fow',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Forest of Wonder',
                    'description'   => 'Forest of Wonder',
                    'content'       => 'Forest of Wonder'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Forest of Wonder',
                    'description'   => 'Forest of Wonder',
                    'content'       => 'Forest of Wonder'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Forest of Wonder',
                    'description'   => 'Forest of Wonder',
                    'content'       => 'Forest of Wonder'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'frtf',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortunate Five',
                    'description'   => 'Fortunate Five',
                    'content'       => 'Fortunate Five'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortunate Five',
                    'description'   => 'Fortunate Five',
                    'content'       => 'Fortunate Five'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortunate Five',
                    'description'   => 'Fortunate Five',
                    'content'       => 'Fortunate Five'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fday',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortune Day',
                    'description'   => 'Fortune Day',
                    'content'       => 'Fortune Day'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortune Day',
                    'description'   => 'Fortune Day',
                    'content'       => 'Fortune Day'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortune Day',
                    'description'   => 'Fortune Day',
                    'content'       => 'Fortune Day'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_swfl_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortune Lions',
                    'description'   => 'Fortune Lions',
                    'content'       => 'Fortune Lions'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortune Lions',
                    'description'   => 'Fortune Lions',
                    'content'       => 'Fortune Lions'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortune Lions',
                    'description'   => 'Fortune Lions',
                    'content'       => 'Fortune Lions'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fxf',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fortunes of The Fox',
                    'description'   => 'Fortunes of The Fox',
                    'content'       => 'Fortunes of The Fox'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fortunes of The Fox',
                    'description'   => 'Fortunes of The Fox',
                    'content'       => 'Fortunes of The Fox'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fortunes of The Fox',
                    'description'   => 'Fortunes of The Fox',
                    'content'       => 'Fortunes of The Fox'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'foy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fountain of Youth',
                    'description'   => 'Fountain of Youth',
                    'content'       => 'Fountain of Youth'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fountain of Youth',
                    'description'   => 'Fountain of Youth',
                    'content'       => 'Fountain of Youth'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fountain of Youth',
                    'description'   => 'Fountain of Youth',
                    'content'       => 'Fountain of Youth'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fdt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Frankie Dettori\'s Magic Seven',
                    'description'   => 'Frankie Dettori\'s Magic Seven',
                    'content'       => 'Frankie Dettori\'s Magic Seven'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Frankie Dettori\'s Magic Seven',
                    'description'   => 'Frankie Dettori\'s Magic Seven',
                    'content'       => 'Frankie Dettori\'s Magic Seven'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Frankie Dettori\'s Magic Seven',
                    'description'   => 'Frankie Dettori\'s Magic Seven',
                    'content'       => 'Frankie Dettori\'s Magic Seven'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fdtjg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Frankie Dettori\'s Magic Seven Jackpot',
                    'description'   => 'Frankie Dettori\'s Magic Seven Jackpot',
                    'content'       => 'Frankie Dettori\'s Magic Seven Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Frankie Dettori\'s Magic Seven Jackpot',
                    'description'   => 'Frankie Dettori\'s Magic Seven Jackpot',
                    'content'       => 'Frankie Dettori\'s Magic Seven Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Frankie Dettori\'s Magic Seven Jackpot',
                    'description'   => 'Frankie Dettori\'s Magic Seven Jackpot',
                    'content'       => 'Frankie Dettori\'s Magic Seven Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fmn',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fruit Mania',
                    'description'   => 'Fruit Mania',
                    'content'       => 'Fruit Mania'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fruit Mania',
                    'description'   => 'Fruit Mania',
                    'content'       => 'Fruit Mania'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fruit Mania',
                    'description'   => 'Fruit Mania',
                    'content'       => 'Fruit Mania'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashfmf',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Full Moon Fortunes',
                    'description'   => 'Full Moon Fortunes',
                    'content'       => 'Full Moon Fortunes'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Full Moon Fortunes',
                    'description'   => 'Full Moon Fortunes',
                    'content'       => 'Full Moon Fortunes'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Full Moon Fortunes',
                    'description'   => 'Full Moon Fortunes',
                    'content'       => 'Full Moon Fortunes'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fff',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Funky Fruits Farm',
                    'description'   => 'Funky Fruits Farm',
                    'content'       => 'Funky Fruits Farm'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Funky Fruits Farm',
                    'description'   => 'Funky Fruits Farm',
                    'content'       => 'Funky Fruits Farm'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Funky Fruits Farm',
                    'description'   => 'Funky Fruits Farm',
                    'content'       => 'Funky Fruits Farm'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fnfrj',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Funky Fruits Jackpot Game',
                    'description'   => 'Funky Fruits Jackpot Game',
                    'content'       => 'Funky Fruits Jackpot Game'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Funky Fruits Jackpot Game',
                    'description'   => 'Funky Fruits Jackpot Game',
                    'content'       => 'Funky Fruits Jackpot Game'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Funky Fruits Jackpot Game',
                    'description'   => 'Funky Fruits Jackpot Game',
                    'content'       => 'Funky Fruits Jackpot Game'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fm',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Funky Monkey',
                    'description'   => 'Funky Monkey',
                    'content'       => 'Funky Monkey'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Funky Monkey',
                    'description'   => 'Funky Monkey',
                    'content'       => 'Funky Monkey'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Funky Monkey',
                    'description'   => 'Funky Monkey',
                    'content'       => 'Funky Monkey'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ges',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Geisha Story',
                    'description'   => 'Geisha Story',
                    'content'       => 'Geisha Story'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Geisha Story',
                    'description'   => 'Geisha Story',
                    'content'       => 'Geisha Story'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Geisha Story',
                    'description'   => 'Geisha Story',
                    'content'       => 'Geisha Story'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gesjp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Geisha Story Jackpot',
                    'description'   => 'Geisha Story Jackpot',
                    'content'       => 'Geisha Story Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Geisha Story Jackpot',
                    'description'   => 'Geisha Story Jackpot',
                    'content'       => 'Geisha Story Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Geisha Story Jackpot',
                    'description'   => 'Geisha Story Jackpot',
                    'content'       => 'Geisha Story Jackpot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gemq',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gem Queen',
                    'description'   => 'Gem Queen',
                    'content'       => 'Gem Queen'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gem Queen',
                    'description'   => 'Gem Queen',
                    'content'       => 'Gem Queen'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gem Queen',
                    'description'   => 'Gem Queen',
                    'content'       => 'Gem Queen'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'glr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gladiator',
                    'description'   => 'Gladiator',
                    'content'       => 'Gladiator'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gladiator',
                    'description'   => 'Gladiator',
                    'content'       => 'Gladiator'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gladiator',
                    'description'   => 'Gladiator',
                    'content'       => 'Gladiator'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'glrj',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gladiator Jackpot',
                    'description'   => 'Gladiator Jackpot',
                    'content'       => 'Gladiator Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gladiator Jackpot',
                    'description'   => 'Gladiator Jackpot',
                    'content'       => 'Gladiator Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gladiator Jackpot',
                    'description'   => 'Gladiator Jackpot',
                    'content'       => 'Gladiator Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'grel',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gold Rally',
                    'description'   => 'Gold Rally',
                    'content'       => 'Gold Rally'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gold Rally',
                    'description'   => 'Gold Rally',
                    'content'       => 'Gold Rally'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gold Rally',
                    'description'   => 'Gold Rally',
                    'content'       => 'Gold Rally'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'glg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Golden Games',
                    'description'   => 'Golden Games',
                    'content'       => 'Golden Games'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Golden Games',
                    'description'   => 'Golden Games',
                    'content'       => 'Golden Games'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Golden Games',
                    'description'   => 'Golden Games',
                    'content'       => 'Golden Games'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gos',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Golden Tour',
                    'description'   => 'Golden Tour',
                    'content'       => 'Golden Tour'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Golden Tour',
                    'description'   => 'Golden Tour',
                    'content'       => 'Golden Tour'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Golden Tour',
                    'description'   => 'Golden Tour',
                    'content'       => 'Golden Tour'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'bib',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Great Blue',
                    'description'   => 'Great Blue',
                    'content'       => 'Great Blue'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Great Blue',
                    'description'   => 'Great Blue',
                    'content'       => 'Great Blue'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Great Blue',
                    'description'   => 'Great Blue',
                    'content'       => 'Great Blue'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gro',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Greatest Odyssey',
                    'description'   => 'Greatest Odyssey',
                    'content'       => 'Greatest Odyssey'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Greatest Odyssey',
                    'description'   => 'Greatest Odyssey',
                    'content'       => 'Greatest Odyssey'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Greatest Odyssey',
                    'description'   => 'Greatest Odyssey',
                    'content'       => 'Greatest Odyssey'
                ],
            ],
            'devices'       => [2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hlf',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Halloween Fortune',
                    'description'   => 'Halloween Fortune',
                    'content'       => 'Halloween Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Halloween Fortune',
                    'description'   => 'Halloween Fortune',
                    'content'       => 'Halloween Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Halloween Fortune',
                    'description'   => 'Halloween Fortune',
                    'content'       => 'Halloween Fortune'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hlf2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Halloween Fortune II',
                    'description'   => 'Halloween Fortune II',
                    'content'       => 'Halloween Fortune II'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Halloween Fortune II',
                    'description'   => 'Halloween Fortune II',
                    'content'       => 'Halloween Fortune II'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Halloween Fortune II',
                    'description'   => 'Halloween Fortune II',
                    'content'       => 'Halloween Fortune II'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_haocs_sky',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Haoshi Cheng Shuang',
                    'description'   => 'Haoshi Cheng Shuang',
                    'content'       => 'Haoshi Cheng Shuang'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Haoshi Cheng Shuang',
                    'description'   => 'Haoshi Cheng Shuang',
                    'content'       => 'Haoshi Cheng Shuang'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Haoshi Cheng Shuang',
                    'description'   => 'Haoshi Cheng Shuang',
                    'content'       => 'Haoshi Cheng Shuang'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hh',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Haunted House',
                    'description'   => 'Haunted House',
                    'content'       => 'Haunted House'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Haunted House',
                    'description'   => 'Haunted House',
                    'content'       => 'Haunted House'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Haunted House',
                    'description'   => 'Haunted House',
                    'content'       => 'Haunted House'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashhotj',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Heart of The Jungle',
                    'description'   => 'Heart of The Jungle',
                    'content'       => 'Heart of The Jungle'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Heart of The Jungle',
                    'description'   => 'Heart of The Jungle',
                    'content'       => 'Heart of The Jungle'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Heart of The Jungle',
                    'description'   => 'Heart of The Jungle',
                    'content'       => 'Heart of The Jungle'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'heavru',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Heavenly Ruler',
                    'description'   => 'Heavenly Ruler',
                    'content'       => 'Heavenly Ruler'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Heavenly Ruler',
                    'description'   => 'Heavenly Ruler',
                    'content'       => 'Heavenly Ruler'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Heavenly Ruler',
                    'description'   => 'Heavenly Ruler',
                    'content'       => 'Heavenly Ruler'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Highway Kings',
                    'description'   => 'Highway Kings',
                    'content'       => 'Highway Kings'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Highway Kings',
                    'description'   => 'Highway Kings',
                    'content'       => 'Highway Kings'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Highway Kings',
                    'description'   => 'Highway Kings',
                    'content'       => 'Highway Kings'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtshwkp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Highway Kings Pro',
                    'description'   => 'Highway Kings Pro',
                    'content'       => 'Highway Kings Pro'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Highway Kings Pro',
                    'description'   => 'Highway Kings Pro',
                    'content'       => 'Highway Kings Pro'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Highway Kings Pro',
                    'description'   => 'Highway Kings Pro',
                    'content'       => 'Highway Kings Pro'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gts50',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hot Gems',
                    'description'   => 'Hot Gems',
                    'content'       => 'Hot Gems'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hot Gems',
                    'description'   => 'Hot Gems',
                    'content'       => 'Hot Gems'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hot Gems',
                    'description'   => 'Hot Gems',
                    'content'       => 'Hot Gems'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'hotktv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Hot KTV',
                    'description'   => 'Hot KTV',
                    'content'       => 'Hot KTV'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Hot KTV',
                    'description'   => 'Hot KTV',
                    'content'       => 'Hot KTV'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Hot KTV',
                    'description'   => 'Hot KTV',
                    'content'       => 'Hot KTV'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsir',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ice Run',
                    'description'   => 'Ice Run',
                    'content'       => 'Ice Run'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ice Run',
                    'description'   => 'Ice Run',
                    'content'       => 'Ice Run'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ice Run',
                    'description'   => 'Ice Run',
                    'content'       => 'Ice Run'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'aztec',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Inca Jackpot',
                    'description'   => 'Inca Jackpot',
                    'content'       => 'Inca Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Inca Jackpot',
                    'description'   => 'Inca Jackpot',
                    'content'       => 'Inca Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Inca Jackpot',
                    'description'   => 'Inca Jackpot',
                    'content'       => 'Inca Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'irl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Irish Luck',
                    'description'   => 'Irish Luck',
                    'content'       => 'Irish Luck'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Irish Luck',
                    'description'   => 'Irish Luck',
                    'content'       => 'Irish Luck'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Irish Luck',
                    'description'   => 'Irish Luck',
                    'content'       => 'Irish Luck'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'jpgt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jackpot Giant',
                    'description'   => 'Jackpot Giant',
                    'content'       => 'Jackpot Giant'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jackpot Giant',
                    'description'   => 'Jackpot Giant',
                    'content'       => 'Jackpot Giant'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jackpot Giant',
                    'description'   => 'Jackpot Giant',
                    'content'       => 'Jackpot Giant'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsje',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jade Emperor',
                    'description'   => 'Jade Emperor',
                    'content'       => 'Jade Emperor'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jade Emperor',
                    'description'   => 'Jade Emperor',
                    'content'       => 'Jade Emperor'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jade Emperor',
                    'description'   => 'Jade Emperor',
                    'content'       => 'Jade Emperor'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsjxb',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ji Xiang 8',
                    'description'   => 'Ji Xiang 8',
                    'content'       => 'Ji Xiang 8'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ji Xiang 8',
                    'description'   => 'Ji Xiang 8',
                    'content'       => 'Ji Xiang 8'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ji Xiang 8',
                    'description'   => 'Ji Xiang 8',
                    'content'       => 'Ji Xiang 8'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'jqw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jin Qian Wa',
                    'description'   => 'Jin Qian Wa',
                    'content'       => 'Jin Qian Wa'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jin Qian Wa',
                    'description'   => 'Jin Qian Wa',
                    'content'       => 'Jin Qian Wa'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jin Qian Wa',
                    'description'   => 'Jin Qian Wa',
                    'content'       => 'Jin Qian Wa'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'kkg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Kong The Eighth Wonder Of The World',
                    'description'   => 'Kong The Eighth Wonder Of The World',
                    'content'       => 'Kong The Eighth Wonder Of The World'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Kong The Eighth Wonder Of The World',
                    'description'   => 'Kong The Eighth Wonder Of The World',
                    'content'       => 'Kong The Eighth Wonder Of The World'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Kong The Eighth Wonder Of The World',
                    'description'   => 'Kong The Eighth Wonder Of The World',
                    'content'       => 'Kong The Eighth Wonder Of The World'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'lndg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Land of Gold',
                    'description'   => 'Land of Gold',
                    'content'       => 'Land of Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Land of Gold',
                    'description'   => 'Land of Gold',
                    'content'       => 'Land of Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Land of Gold',
                    'description'   => 'Land of Gold',
                    'content'       => 'Land of Gold'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ght_a',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lie Yan Zuan Shi',
                    'description'   => 'Lie Yan Zuan Shi',
                    'content'       => 'Lie Yan Zuan Shi'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lie Yan Zuan Shi',
                    'description'   => 'Lie Yan Zuan Shi',
                    'content'       => 'Lie Yan Zuan Shi'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lie Yan Zuan Shi',
                    'description'   => 'Lie Yan Zuan Shi',
                    'content'       => 'Lie Yan Zuan Shi'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'kfp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Liu Fu Shou',
                    'description'   => 'Liu Fu Shou',
                    'content'       => 'Liu Fu Shou'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Liu Fu Shou',
                    'description'   => 'Liu Fu Shou',
                    'content'       => 'Liu Fu Shou'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Liu Fu Shou',
                    'description'   => 'Liu Fu Shou',
                    'content'       => 'Liu Fu Shou'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'longlong',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Long Long Long',
                    'description'   => 'Long Long Long',
                    'content'       => 'Long Long Long'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Long Long Long',
                    'description'   => 'Long Long Long',
                    'content'       => 'Long Long Long'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Long Long Long',
                    'description'   => 'Long Long Long',
                    'content'       => 'Long Long Long'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'lm',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lotto Madness',
                    'description'   => 'Lotto Madness',
                    'content'       => 'Lotto Madness'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lotto Madness',
                    'description'   => 'Lotto Madness',
                    'content'       => 'Lotto Madness'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lotto Madness',
                    'description'   => 'Lotto Madness',
                    'content'       => 'Lotto Madness'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gts51',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Panda',
                    'description'   => 'Lucky Panda',
                    'content'       => 'Lucky Panda'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Panda',
                    'description'   => 'Lucky Panda',
                    'content'       => 'Lucky Panda'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Panda',
                    'description'   => 'Lucky Panda',
                    'content'       => 'Lucky Panda'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ms',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Magic Slots',
                    'description'   => 'Magic Slots',
                    'content'       => 'Magic Slots'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Magic Slots',
                    'description'   => 'Magic Slots',
                    'content'       => 'Magic Slots'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Magic Slots',
                    'description'   => 'Magic Slots',
                    'content'       => 'Magic Slots'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'mgstk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Magical Stacks',
                    'description'   => 'Magical Stacks',
                    'content'       => 'Magical Stacks'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Magical Stacks',
                    'description'   => 'Magical Stacks',
                    'content'       => 'Magical Stacks'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Magical Stacks',
                    'description'   => 'Magical Stacks',
                    'content'       => 'Magical Stacks'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsmrln',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Marilyn Monroe',
                    'description'   => 'Marilyn Monroe',
                    'content'       => 'Marilyn Monroe'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Marilyn Monroe',
                    'description'   => 'Marilyn Monroe',
                    'content'       => 'Marilyn Monroe'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Marilyn Monroe',
                    'description'   => 'Marilyn Monroe',
                    'content'       => 'Marilyn Monroe'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'mfrt',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Miss Fortune',
                    'description'   => 'Miss Fortune',
                    'content'       => 'Miss Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Miss Fortune',
                    'description'   => 'Miss Fortune',
                    'content'       => 'Miss Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Miss Fortune',
                    'description'   => 'Miss Fortune',
                    'content'       => 'Miss Fortune'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'mcb',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mr. Cashback',
                    'description'   => 'Mr. Cashback',
                    'content'       => 'Mr. Cashback'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mr. Cashback',
                    'description'   => 'Mr. Cashback',
                    'content'       => 'Mr. Cashback'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mr. Cashback',
                    'description'   => 'Mr. Cashback',
                    'content'       => 'Mr. Cashback'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'nk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Neptunes Kingdom',
                    'description'   => 'Neptunes Kingdom',
                    'content'       => 'Neptunes Kingdom'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Neptunes Kingdom',
                    'description'   => 'Neptunes Kingdom',
                    'content'       => 'Neptunes Kingdom'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Neptunes Kingdom',
                    'description'   => 'Neptunes Kingdom',
                    'content'       => 'Neptunes Kingdom'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'nian',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Nian Nian You Yu',
                    'description'   => 'Nian Nian You Yu',
                    'content'       => 'Nian Nian You Yu'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Nian Nian You Yu',
                    'description'   => 'Nian Nian You Yu',
                    'content'       => 'Nian Nian You Yu'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Nian Nian You Yu',
                    'description'   => 'Nian Nian You Yu',
                    'content'       => 'Nian Nian You Yu'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pmn',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Panther Moon',
                    'description'   => 'Panther Moon',
                    'content'       => 'Panther Moon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Panther Moon',
                    'description'   => 'Panther Moon',
                    'content'       => 'Panther Moon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Panther Moon',
                    'description'   => 'Panther Moon',
                    'content'       => 'Panther Moon'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Party Line',
                    'description'   => 'Party Line',
                    'content'       => 'Party Line'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Party Line',
                    'description'   => 'Party Line',
                    'content'       => 'Party Line'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Party Line',
                    'description'   => 'Party Line',
                    'content'       => 'Party Line'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pgv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Penguin Vacation',
                    'description'   => 'Penguin Vacation',
                    'content'       => 'Penguin Vacation'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Penguin Vacation',
                    'description'   => 'Penguin Vacation',
                    'content'       => 'Penguin Vacation'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Penguin Vacation',
                    'description'   => 'Penguin Vacation',
                    'content'       => 'Penguin Vacation'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pst',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pharaoh\'s Secrets',
                    'description'   => 'Pharaoh\'s Secrets',
                    'content'       => 'Pharaoh\'s Secrets'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pharaoh\'s Secrets',
                    'description'   => 'Pharaoh\'s Secrets',
                    'content'       => 'Pharaoh\'s Secrets'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pharaoh\'s Secrets',
                    'description'   => 'Pharaoh\'s Secrets',
                    'content'       => 'Pharaoh\'s Secrets'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'paw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Piggies and the Wolf',
                    'description'   => 'Piggies and the Wolf',
                    'content'       => 'Piggies and the Wolf'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Piggies and the Wolf',
                    'description'   => 'Piggies and the Wolf',
                    'content'       => 'Piggies and the Wolf'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Piggies and the Wolf',
                    'description'   => 'Piggies and the Wolf',
                    'content'       => 'Piggies and the Wolf'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtspor',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Plenty O\'Fortune',
                    'description'   => 'Plenty O\'Fortune',
                    'content'       => 'Plenty O\'Fortune'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Plenty O\'Fortune',
                    'description'   => 'Plenty O\'Fortune',
                    'content'       => 'Plenty O\'Fortune'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Plenty O\'Fortune',
                    'description'   => 'Plenty O\'Fortune',
                    'content'       => 'Plenty O\'Fortune'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'phot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Purple Hot',
                    'description'   => 'Purple Hot',
                    'content'       => 'Purple Hot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Purple Hot',
                    'description'   => 'Purple Hot',
                    'content'       => 'Purple Hot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Purple Hot',
                    'description'   => 'Purple Hot',
                    'content'       => 'Purple Hot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'qop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Queen of Pyramids',
                    'description'   => 'Queen of Pyramids',
                    'content'       => 'Queen of Pyramids'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Queen of Pyramids',
                    'description'   => 'Queen of Pyramids',
                    'content'       => 'Queen of Pyramids'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Queen of Pyramids',
                    'description'   => 'Queen of Pyramids',
                    'content'       => 'Queen of Pyramids'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'qnw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Queen Of Wands',
                    'description'   => 'Queen Of Wands',
                    'content'       => 'Queen Of Wands'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Queen Of Wands',
                    'description'   => 'Queen Of Wands',
                    'content'       => 'Queen Of Wands'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Queen Of Wands',
                    'description'   => 'Queen Of Wands',
                    'content'       => 'Queen Of Wands'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ririjc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ri Ri Jin Cai',
                    'description'   => 'Ri Ri Jin Cai',
                    'content'       => 'Ri Ri Jin Cai'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ri Ri Jin Cai',
                    'description'   => 'Ri Ri Jin Cai',
                    'content'       => 'Ri Ri Jin Cai'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ri Ri Jin Cai',
                    'description'   => 'Ri Ri Jin Cai',
                    'content'       => 'Ri Ri Jin Cai'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ririshc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ri Ri Sheng Cai',
                    'description'   => 'Ri Ri Sheng Cai',
                    'content'       => 'Ri Ri Sheng Cai'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ri Ri Sheng Cai',
                    'description'   => 'Ri Ri Sheng Cai',
                    'content'       => 'Ri Ri Sheng Cai'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ri Ri Sheng Cai',
                    'description'   => 'Ri Ri Sheng Cai',
                    'content'       => 'Ri Ri Sheng Cai'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'rky',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rocky',
                    'description'   => 'Rocky',
                    'content'       => 'Rocky'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rocky',
                    'description'   => 'Rocky',
                    'content'       => 'Rocky'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rocky',
                    'description'   => 'Rocky',
                    'content'       => 'Rocky'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'rng2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rome and Glory',
                    'description'   => 'Rome and Glory',
                    'content'       => 'Rome and Glory'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rome and Glory',
                    'description'   => 'Rome and Glory',
                    'content'       => 'Rome and Glory'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rome and Glory',
                    'description'   => 'Rome and Glory',
                    'content'       => 'Rome and Glory'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sfh',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Safari Heat',
                    'description'   => 'Safari Heat',
                    'content'       => 'Safari Heat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Safari Heat',
                    'description'   => 'Safari Heat',
                    'content'       => 'Safari Heat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Safari Heat',
                    'description'   => 'Safari Heat',
                    'content'       => 'Safari Heat'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtssmbr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Samba Brazil',
                    'description'   => 'Samba Brazil',
                    'content'       => 'Samba Brazil'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Samba Brazil',
                    'description'   => 'Samba Brazil',
                    'content'       => 'Samba Brazil'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Samba Brazil',
                    'description'   => 'Samba Brazil',
                    'content'       => 'Samba Brazil'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ssp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Santa Surprise',
                    'description'   => 'Santa Surprise',
                    'content'       => 'Santa Surprise'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Santa Surprise',
                    'description'   => 'Santa Surprise',
                    'content'       => 'Santa Surprise'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Santa Surprise',
                    'description'   => 'Santa Surprise',
                    'content'       => 'Santa Surprise'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'savcas',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Savannah Cash',
                    'description'   => 'Savannah Cash',
                    'content'       => 'Savannah Cash'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Savannah Cash',
                    'description'   => 'Savannah Cash',
                    'content'       => 'Savannah Cash'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Savannah Cash',
                    'description'   => 'Savannah Cash',
                    'content'       => 'Savannah Cash'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'samz',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Secrets of the Amazon',
                    'description'   => 'Secrets of the Amazon',
                    'content'       => 'Secrets of the Amazon'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Secrets of the Amazon',
                    'description'   => 'Secrets of the Amazon',
                    'content'       => 'Secrets of the Amazon'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Secrets of the Amazon',
                    'description'   => 'Secrets of the Amazon',
                    'content'       => 'Secrets of the Amazon'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'shmst',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sherlock Mystery',
                    'description'   => 'Sherlock Mystery',
                    'content'       => 'Sherlock Mystery'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sherlock Mystery',
                    'description'   => 'Sherlock Mystery',
                    'content'       => 'Sherlock Mystery'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sherlock Mystery',
                    'description'   => 'Sherlock Mystery',
                    'content'       => 'Sherlock Mystery'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_swsl_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Si Ling',
                    'description'   => 'Si Ling',
                    'content'       => 'Si Ling'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Si Ling',
                    'description'   => 'Si Ling',
                    'content'       => 'Si Ling'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Si Ling',
                    'description'   => 'Si Ling',
                    'content'       => 'Si Ling'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sx',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Si Xiang',
                    'description'   => 'Si Xiang',
                    'content'       => 'Si Xiang'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Si Xiang',
                    'description'   => 'Si Xiang',
                    'content'       => 'Si Xiang'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Si Xiang',
                    'description'   => 'Si Xiang',
                    'content'       => 'Si Xiang'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sis',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Silent Samurai',
                    'description'   => 'Silent Samurai',
                    'content'       => 'Silent Samurai'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Silent Samurai',
                    'description'   => 'Silent Samurai',
                    'content'       => 'Silent Samurai'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Silent Samurai',
                    'description'   => 'Silent Samurai',
                    'content'       => 'Silent Samurai'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sisjp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Silent Samurai Jackpot',
                    'description'   => 'Silent Samurai Jackpot',
                    'content'       => 'Silent Samurai Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Silent Samurai Jackpot',
                    'description'   => 'Silent Samurai Jackpot',
                    'content'       => 'Silent Samurai Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Silent Samurai Jackpot',
                    'description'   => 'Silent Samurai Jackpot',
                    'content'       => 'Silent Samurai Jackpot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sib',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Silver Bullet',
                    'description'   => 'Silver Bullet',
                    'content'       => 'Silver Bullet'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Silver Bullet',
                    'description'   => 'Silver Bullet',
                    'content'       => 'Silver Bullet'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Silver Bullet',
                    'description'   => 'Silver Bullet',
                    'content'       => 'Silver Bullet'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashsbd',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sinbad\'s Golden Voyage',
                    'description'   => 'Sinbad\'s Golden Voyage',
                    'content'       => 'Sinbad\'s Golden Voyage'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sinbad\'s Golden Voyage',
                    'description'   => 'Sinbad\'s Golden Voyage',
                    'content'       => 'Sinbad\'s Golden Voyage'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sinbad\'s Golden Voyage',
                    'description'   => 'Sinbad\'s Golden Voyage',
                    'content'       => 'Sinbad\'s Golden Voyage'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'skp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Skazka Pro',
                    'description'   => 'Skazka Pro',
                    'content'       => 'Skazka Pro'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Skazka Pro',
                    'description'   => 'Skazka Pro',
                    'content'       => 'Skazka Pro'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Skazka Pro',
                    'description'   => 'Skazka Pro',
                    'content'       => 'Skazka Pro'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'spr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sparta',
                    'description'   => 'Sparta',
                    'content'       => 'Sparta'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sparta',
                    'description'   => 'Sparta',
                    'content'       => 'Sparta'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sparta',
                    'description'   => 'Sparta',
                    'content'       => 'Sparta'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'spud',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Spud O´Reilly´s Crops of Gold',
                    'description'   => 'Spud O´Reilly´s Crops of Gold',
                    'content'       => 'Spud O´Reilly´s Crops of Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Spud O´Reilly´s Crops of Gold',
                    'description'   => 'Spud O´Reilly´s Crops of Gold',
                    'content'       => 'Spud O´Reilly´s Crops of Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Spud O´Reilly´s Crops of Gold',
                    'description'   => 'Spud O´Reilly´s Crops of Gold',
                    'content'       => 'Spud O´Reilly´s Crops of Gold'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sol',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Streak of Luck',
                    'description'   => 'Streak of Luck',
                    'content'       => 'Streak of Luck'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Streak of Luck',
                    'description'   => 'Streak of Luck',
                    'content'       => 'Streak of Luck'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Streak of Luck',
                    'description'   => 'Streak of Luck',
                    'content'       => 'Streak of Luck'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'sugla',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sugar Land',
                    'description'   => 'Sugar Land',
                    'content'       => 'Sugar Land'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sugar Land',
                    'description'   => 'Sugar Land',
                    'content'       => 'Sugar Land'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sugar Land',
                    'description'   => 'Sugar Land',
                    'content'       => 'Sugar Land'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsswk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sun Wukong',
                    'description'   => 'Sun Wukong',
                    'content'       => 'Sun Wukong'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sun Wukong',
                    'description'   => 'Sun Wukong',
                    'content'       => 'Sun Wukong'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sun Wukong',
                    'description'   => 'Sun Wukong',
                    'content'       => 'Sun Wukong'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'slion',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Super Lion',
                    'description'   => 'Super Lion',
                    'content'       => 'Super Lion'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Super Lion',
                    'description'   => 'Super Lion',
                    'content'       => 'Super Lion'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Super Lion',
                    'description'   => 'Super Lion',
                    'content'       => 'Super Lion'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'cnpr',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sweet Party',
                    'description'   => 'Sweet Party',
                    'content'       => 'Sweet Party'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sweet Party',
                    'description'   => 'Sweet Party',
                    'content'       => 'Sweet Party'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sweet Party',
                    'description'   => 'Sweet Party',
                    'content'       => 'Sweet Party'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'tpd2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Thai Paradise',
                    'description'   => 'Thai Paradise',
                    'content'       => 'Thai Paradise'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Thai Paradise',
                    'description'   => 'Thai Paradise',
                    'content'       => 'Thai Paradise'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Thai Paradise',
                    'description'   => 'Thai Paradise',
                    'content'       => 'Thai Paradise'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'thtk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Thai Temple',
                    'description'   => 'Thai Temple',
                    'content'       => 'Thai Temple'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Thai Temple',
                    'description'   => 'Thai Temple',
                    'content'       => 'Thai Temple'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Thai Temple',
                    'description'   => 'Thai Temple',
                    'content'       => 'Thai Temple'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'dcv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Discovery',
                    'description'   => 'The Discovery',
                    'content'       => 'The Discovery'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Discovery',
                    'description'   => 'The Discovery',
                    'content'       => 'The Discovery'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Discovery',
                    'description'   => 'The Discovery',
                    'content'       => 'The Discovery'
                ],
            ],
            'devices'       => [2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashglss',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Glass Slipper',
                    'description'   => 'The Glass Slipper',
                    'content'       => 'The Glass Slipper'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Glass Slipper',
                    'description'   => 'The Glass Slipper',
                    'content'       => 'The Glass Slipper'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Glass Slipper',
                    'description'   => 'The Glass Slipper',
                    'content'       => 'The Glass Slipper'
                ],
            ],
            'devices'       => [2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsgme',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Great Ming Empire',
                    'description'   => 'The Great Ming Empire',
                    'content'       => 'The Great Ming Empire'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Great Ming Empire',
                    'description'   => 'The Great Ming Empire',
                    'content'       => 'The Great Ming Empire'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Great Ming Empire',
                    'description'   => 'The Great Ming Empire',
                    'content'       => 'The Great Ming Empire'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtsjzc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Jazz Club',
                    'description'   => 'The Jazz Club',
                    'content'       => 'The Jazz Club'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Jazz Club',
                    'description'   => 'The Jazz Club',
                    'content'       => 'The Jazz Club'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Jazz Club',
                    'description'   => 'The Jazz Club',
                    'content'       => 'The Jazz Club'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'mmy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Mummy',
                    'description'   => 'The Mummy',
                    'content'       => 'The Mummy'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Mummy',
                    'description'   => 'The Mummy',
                    'content'       => 'The Mummy'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Mummy',
                    'description'   => 'The Mummy',
                    'content'       => 'The Mummy'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'donq',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Riches of Don Quixote',
                    'description'   => 'The Riches of Don Quixote',
                    'content'       => 'The Riches of Don Quixote'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Riches of Don Quixote',
                    'description'   => 'The Riches of Don Quixote',
                    'content'       => 'The Riches of Don Quixote'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Riches of Don Quixote',
                    'description'   => 'The Riches of Don Quixote',
                    'content'       => 'The Riches of Don Quixote'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'tmqd',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Three Musketeers and The Queen\'s Diamond',
                    'description'   => 'The Three Musketeers and The Queen\'s Diamond',
                    'content'       => 'The Three Musketeers and The Queen\'s Diamond'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Three Musketeers and The Queen\'s Diamond',
                    'description'   => 'The Three Musketeers and The Queen\'s Diamond',
                    'content'       => 'The Three Musketeers and The Queen\'s Diamond'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Three Musketeers and The Queen\'s Diamond',
                    'description'   => 'The Three Musketeers and The Queen\'s Diamond',
                    'content'       => 'The Three Musketeers and The Queen\'s Diamond'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'titimama',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tiao Tiao Mao Mao',
                    'description'   => 'Tiao Tiao Mao Mao',
                    'content'       => 'Tiao Tiao Mao Mao'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tiao Tiao Mao Mao',
                    'description'   => 'Tiao Tiao Mao Mao',
                    'content'       => 'Tiao Tiao Mao Mao'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tiao Tiao Mao Mao',
                    'description'   => 'Tiao Tiao Mao Mao',
                    'content'       => 'Tiao Tiao Mao Mao'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashtmd',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Time for a Deal',
                    'description'   => 'Time for a Deal',
                    'content'       => 'Time for a Deal'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Time for a Deal',
                    'description'   => 'Time for a Deal',
                    'content'       => 'Time for a Deal'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Time for a Deal',
                    'description'   => 'Time for a Deal',
                    'content'       => 'Time for a Deal'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'topg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Top Gun',
                    'description'   => 'Top Gun',
                    'content'       => 'Top Gun'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Top Gun',
                    'description'   => 'Top Gun',
                    'content'       => 'Top Gun'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Top Gun',
                    'description'   => 'Top Gun',
                    'content'       => 'Top Gun'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ttc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Top Trumps Celebs',
                    'description'   => 'Top Trumps Celebs',
                    'content'       => 'Top Trumps Celebs'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Top Trumps Celebs',
                    'description'   => 'Top Trumps Celebs',
                    'content'       => 'Top Trumps Celebs'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Top Trumps Celebs',
                    'description'   => 'Top Trumps Celebs',
                    'content'       => 'Top Trumps Celebs'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ta',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tres Amigos',
                    'description'   => 'Tres Amigos',
                    'content'       => 'Tres Amigos'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tres Amigos',
                    'description'   => 'Tres Amigos',
                    'content'       => 'Tres Amigos'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tres Amigos',
                    'description'   => 'Tres Amigos',
                    'content'       => 'Tres Amigos'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'trpmnk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Triple Monkey',
                    'description'   => 'Triple Monkey',
                    'content'       => 'Triple Monkey'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Triple Monkey',
                    'description'   => 'Triple Monkey',
                    'content'       => 'Triple Monkey'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Triple Monkey',
                    'description'   => 'Triple Monkey',
                    'content'       => 'Triple Monkey'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'trl',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'True Love',
                    'description'   => 'True Love',
                    'content'       => 'True Love'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'True Love',
                    'description'   => 'True Love',
                    'content'       => 'True Love'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'True Love',
                    'description'   => 'True Love',
                    'content'       => 'True Love'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ub',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ugga Bugga',
                    'description'   => 'Ugga Bugga',
                    'content'       => 'Ugga Bugga'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ugga Bugga',
                    'description'   => 'Ugga Bugga',
                    'content'       => 'Ugga Bugga'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ugga Bugga',
                    'description'   => 'Ugga Bugga',
                    'content'       => 'Ugga Bugga'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'er',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Vacation Station',
                    'description'   => 'Vacation Station',
                    'content'       => 'Vacation Station'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Vacation Station',
                    'description'   => 'Vacation Station',
                    'content'       => 'Vacation Station'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Vacation Station',
                    'description'   => 'Vacation Station',
                    'content'       => 'Vacation Station'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'vcstd',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Vacation Station Deluxe',
                    'description'   => 'Vacation Station Deluxe',
                    'content'       => 'Vacation Station Deluxe'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Vacation Station Deluxe',
                    'description'   => 'Vacation Station Deluxe',
                    'content'       => 'Vacation Station Deluxe'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Vacation Station Deluxe',
                    'description'   => 'Vacation Station Deluxe',
                    'content'       => 'Vacation Station Deluxe'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gts52',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Vikingmania',
                    'description'   => 'Vikingmania',
                    'content'       => 'Vikingmania'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Vikingmania',
                    'description'   => 'Vikingmania',
                    'content'       => 'Vikingmania'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Vikingmania',
                    'description'   => 'Vikingmania',
                    'content'       => 'Vikingmania'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'warg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Warriors Gold',
                    'description'   => 'Warriors Gold',
                    'content'       => 'Warriors Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Warriors Gold',
                    'description'   => 'Warriors Gold',
                    'content'       => 'Warriors Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Warriors Gold',
                    'description'   => 'Warriors Gold',
                    'content'       => 'Warriors Gold'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'whk',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'White King',
                    'description'   => 'White King',
                    'content'       => 'White King'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'White King',
                    'description'   => 'White King',
                    'content'       => 'White King'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'White King',
                    'description'   => 'White King',
                    'content'       => 'White King'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtswg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wild Gambler',
                    'description'   => 'Wild Gambler',
                    'content'       => 'Wild Gambler'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wild Gambler',
                    'description'   => 'Wild Gambler',
                    'content'       => 'Wild Gambler'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wild Gambler',
                    'description'   => 'Wild Gambler',
                    'content'       => 'Wild Gambler'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashwgaa',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wild Gambler 2: Arctic Adventure',
                    'description'   => 'Wild Gambler 2: Arctic Adventure',
                    'content'       => 'Wild Gambler 2: Arctic Adventure'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wild Gambler 2: Arctic Adventure',
                    'description'   => 'Wild Gambler 2: Arctic Adventure',
                    'content'       => 'Wild Gambler 2: Arctic Adventure'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wild Gambler 2: Arctic Adventure',
                    'description'   => 'Wild Gambler 2: Arctic Adventure',
                    'content'       => 'Wild Gambler 2: Arctic Adventure'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'wis',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wild Spirit',
                    'description'   => 'Wild Spirit',
                    'content'       => 'Wild Spirit'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wild Spirit',
                    'description'   => 'Wild Spirit',
                    'content'       => 'Wild Spirit'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wild Spirit',
                    'description'   => 'Wild Spirit',
                    'content'       => 'Wild Spirit'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gtswng',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wings Of Gold',
                    'description'   => 'Wings Of Gold',
                    'content'       => 'Wings Of Gold'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wings Of Gold',
                    'description'   => 'Wings Of Gold',
                    'content'       => 'Wings Of Gold'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wings Of Gold',
                    'description'   => 'Wings Of Gold',
                    'content'       => 'Wings Of Gold'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'wlg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wu Long',
                    'description'   => 'Wu Long',
                    'content'       => 'Wu Long'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wu Long',
                    'description'   => 'Wu Long',
                    'content'       => 'Wu Long'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wu Long',
                    'description'   => 'Wu Long',
                    'content'       => 'Wu Long'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'wlgjp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wu Long Jackpot',
                    'description'   => 'Wu Long Jackpot',
                    'content'       => 'Wu Long Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wu Long Jackpot',
                    'description'   => 'Wu Long Jackpot',
                    'content'       => 'Wu Long Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wu Long Jackpot',
                    'description'   => 'Wu Long Jackpot',
                    'content'       => 'Wu Long Jackpot'
                ],
            ],
            'devices'       => [1],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'wlcsh',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Wu Lu Cai Shen',
                    'description'   => 'Wu Lu Cai Shen',
                    'content'       => 'Wu Lu Cai Shen'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Wu Lu Cai Shen',
                    'description'   => 'Wu Lu Cai Shen',
                    'content'       => 'Wu Lu Cai Shen'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Wu Lu Cai Shen',
                    'description'   => 'Wu Lu Cai Shen',
                    'content'       => 'Wu Lu Cai Shen'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'zcjb',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Zhao Cai Jin Bao',
                    'description'   => 'Zhao Cai Jin Bao',
                    'content'       => 'Zhao Cai Jin Bao'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Zhao Cai Jin Bao',
                    'description'   => 'Zhao Cai Jin Bao',
                    'content'       => 'Zhao Cai Jin Bao'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Zhao Cai Jin Bao',
                    'description'   => 'Zhao Cai Jin Bao',
                    'content'       => 'Zhao Cai Jin Bao'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'zcjbjp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Zhao Cai Jin Bao Jackpot',
                    'description'   => 'Zhao Cai Jin Bao Jackpot',
                    'content'       => 'Zhao Cai Jin Bao Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Zhao Cai Jin Bao Jackpot',
                    'description'   => 'Zhao Cai Jin Bao Jackpot',
                    'content'       => 'Zhao Cai Jin Bao Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Zhao Cai Jin Bao Jackpot',
                    'description'   => 'Zhao Cai Jin Bao Jackpot',
                    'content'       => 'Zhao Cai Jin Bao Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'zctz',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Zhao Cai Tong Zi',
                    'description'   => 'Zhao Cai Tong Zi',
                    'content'       => 'Zhao Cai Tong Zi'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Zhao Cai Tong Zi',
                    'description'   => 'Zhao Cai Tong Zi',
                    'content'       => 'Zhao Cai Tong Zi'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Zhao Cai Tong Zi',
                    'description'   => 'Zhao Cai Tong Zi',
                    'content'       => 'Zhao Cai Tong Zi'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'aeolus',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Age of the Gods: God of Storms',
                    'description'   => 'Age of the Gods: God of Storms',
                    'content'       => 'Age of the Gods: God of Storms'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Age of the Gods: God of Storms',
                    'description'   => 'Age of the Gods: God of Storms',
                    'content'       => 'Age of the Gods: God of Storms'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Age of the Gods: God of Storms',
                    'description'   => 'Age of the Gods: God of Storms',
                    'content'       => 'Age of the Gods: God of Storms'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'legwld',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Legacy Of The Wild',
                    'description'   => 'Legacy Of The Wild',
                    'content'       => 'Legacy Of The Wild'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Legacy Of The Wild',
                    'description'   => 'Legacy Of The Wild',
                    'content'       => 'Legacy Of The Wild'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Legacy Of The Wild',
                    'description'   => 'Legacy Of The Wild',
                    'content'       => 'Legacy Of The Wild'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashicv',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ice Cave',
                    'description'   => 'Ice Cave',
                    'content'       => 'Ice Cave'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ice Cave',
                    'description'   => 'Ice Cave',
                    'content'       => 'Ice Cave'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ice Cave',
                    'description'   => 'Ice Cave',
                    'content'       => 'Ice Cave'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ashhof',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Heart of the Frontier',
                    'description'   => 'Heart of the Frontier',
                    'content'       => 'Heart of the Frontier'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Heart of the Frontier',
                    'description'   => 'Heart of the Frontier',
                    'content'       => 'Heart of the Frontier'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Heart of the Frontier',
                    'description'   => 'Heart of the Frontier',
                    'content'       => 'Heart of the Frontier'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'fmjp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Funky Monkey Jackpot',
                    'description'   => 'Funky Monkey Jackpot',
                    'content'       => 'Funky Monkey Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Funky Monkey Jackpot',
                    'description'   => 'Funky Monkey Jackpot',
                    'content'       => 'Funky Monkey Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Funky Monkey Jackpot',
                    'description'   => 'Funky Monkey Jackpot',
                    'content'       => 'Funky Monkey Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'grbjp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Great Blue Jackpot',
                    'description'   => 'Great Blue Jackpot',
                    'content'       => 'Great Blue Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Great Blue Jackpot',
                    'description'   => 'Great Blue Jackpot',
                    'content'       => 'Great Blue Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Great Blue Jackpot',
                    'description'   => 'Great Blue Jackpot',
                    'content'       => 'Great Blue Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'yclong',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Yun Cong Long',
                    'description'   => 'Yun Cong Long',
                    'content'       => 'Yun Cong Long'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Yun Cong Long',
                    'description'   => 'Yun Cong Long',
                    'content'       => 'Yun Cong Long'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Yun Cong Long',
                    'description'   => 'Yun Cong Long',
                    'content'       => 'Yun Cong Long'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'xufe',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Xuan Pu Lian Huan',
                    'description'   => 'Xuan Pu Lian Huan',
                    'content'       => 'Xuan Pu Lian Huan'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Xuan Pu Lian Huan',
                    'description'   => 'Xuan Pu Lian Huan',
                    'content'       => 'Xuan Pu Lian Huan'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Xuan Pu Lian Huan',
                    'description'   => 'Xuan Pu Lian Huan',
                    'content'       => 'Xuan Pu Lian Huan'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'popc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gaelic Luck',
                    'description'   => 'Gaelic Luck',
                    'content'       => 'Gaelic Luck'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gaelic Luck',
                    'description'   => 'Gaelic Luck',
                    'content'       => 'Gaelic Luck'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gaelic Luck',
                    'description'   => 'Gaelic Luck',
                    'content'       => 'Gaelic Luck'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pisa',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pixel Samurai',
                    'description'   => 'Pixel Samurai',
                    'content'       => 'Pixel Samurai'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pixel Samurai',
                    'description'   => 'Pixel Samurai',
                    'content'       => 'Pixel Samurai'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pixel Samurai',
                    'description'   => 'Pixel Samurai',
                    'content'       => 'Pixel Samurai'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'phtd',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pharaoh’s Treasure Deluxe',
                    'description'   => 'Pharaoh’s Treasure Deluxe',
                    'content'       => 'Pharaoh’s Treasure Deluxe'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pharaoh’s Treasure Deluxe',
                    'description'   => 'Pharaoh’s Treasure Deluxe',
                    'content'       => 'Pharaoh’s Treasure Deluxe'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pharaoh’s Treasure Deluxe',
                    'description'   => 'Pharaoh’s Treasure Deluxe',
                    'content'       => 'Pharaoh’s Treasure Deluxe'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'jnglg',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jungle Giants',
                    'description'   => 'Jungle Giants',
                    'content'       => 'Jungle Giants'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jungle Giants',
                    'description'   => 'Jungle Giants',
                    'content'       => 'Jungle Giants'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jungle Giants',
                    'description'   => 'Jungle Giants',
                    'content'       => 'Jungle Giants'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'epa',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Epic Ape',
                    'description'   => 'Epic Ape',
                    'content'       => 'Epic Ape'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Epic Ape',
                    'description'   => 'Epic Ape',
                    'content'       => 'Epic Ape'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Epic Ape',
                    'description'   => 'Epic Ape',
                    'content'       => 'Epic Ape'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'drgch',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Champions',
                    'description'   => 'Dragon Champions',
                    'content'       => 'Dragon Champions'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Champions',
                    'description'   => 'Dragon Champions',
                    'content'       => 'Dragon Champions'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Champions',
                    'description'   => 'Dragon Champions',
                    'content'       => 'Dragon Champions'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_fiesta_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fiesta de la Memoria',
                    'description'   => 'Fiesta de la Memoria',
                    'content'       => 'Fiesta de la Memoria'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fiesta de la Memoria',
                    'description'   => 'Fiesta de la Memoria',
                    'content'       => 'Fiesta de la Memoria'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fiesta de la Memoria',
                    'description'   => 'Fiesta de la Memoria',
                    'content'       => 'Fiesta de la Memoria'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_rothr_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Raiders of the Hidden Realm',
                    'description'   => 'Raiders of the Hidden Realm',
                    'content'       => 'Raiders of the Hidden Realm'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Raiders of the Hidden Realm',
                    'description'   => 'Raiders of the Hidden Realm',
                    'content'       => 'Raiders of the Hidden Realm'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Raiders of the Hidden Realm',
                    'description'   => 'Raiders of the Hidden Realm',
                    'content'       => 'Raiders of the Hidden Realm'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_satsumo_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Satsumo\'s Revenge',
                    'description'   => 'Satsumo\'s Revenge',
                    'content'       => 'Satsumo\'s Revenge'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Satsumo\'s Revenge',
                    'description'   => 'Satsumo\'s Revenge',
                    'content'       => 'Satsumo\'s Revenge'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Satsumo\'s Revenge',
                    'description'   => 'Satsumo\'s Revenge',
                    'content'       => 'Satsumo\'s Revenge'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_yxlb_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ying Xiong Lu Bu',
                    'description'   => 'Ying Xiong Lu Bu',
                    'content'       => 'Ying Xiong Lu Bu'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ying Xiong Lu Bu',
                    'description'   => 'Ying Xiong Lu Bu',
                    'content'       => 'Ying Xiong Lu Bu'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ying Xiong Lu Bu',
                    'description'   => 'Ying Xiong Lu Bu',
                    'content'       => 'Ying Xiong Lu Bu'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_dr_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Riches',
                    'description'   => 'Dragon Riches',
                    'content'       => 'Dragon Riches'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Riches',
                    'description'   => 'Dragon Riches',
                    'content'       => 'Dragon Riches'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Riches',
                    'description'   => 'Dragon Riches',
                    'content'       => 'Dragon Riches'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'wotp',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ways of the Phoenix',
                    'description'   => 'Ways of the Phoenix',
                    'content'       => 'Ways of the Phoenix'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ways of the Phoenix',
                    'description'   => 'Ways of the Phoenix',
                    'content'       => 'Ways of the Phoenix'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ways of the Phoenix',
                    'description'   => 'Ways of the Phoenix',
                    'content'       => 'Ways of the Phoenix'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ccccny',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Coin Coin Coin CNY',
                    'description'   => 'Coin Coin Coin CNY',
                    'content'       => 'Coin Coin Coin CNY'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Coin Coin Coin CNY',
                    'description'   => 'Coin Coin Coin CNY',
                    'content'       => 'Coin Coin Coin CNY'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Coin Coin Coin CNY',
                    'description'   => 'Coin Coin Coin CNY',
                    'content'       => 'Coin Coin Coin CNY'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'tigc',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tiger Claw',
                    'description'   => 'Tiger Claw',
                    'content'       => 'Tiger Claw'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tiger Claw',
                    'description'   => 'Tiger Claw',
                    'content'       => 'Tiger Claw'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tiger Claw',
                    'description'   => 'Tiger Claw',
                    'content'       => 'Tiger Claw'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'anwild',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Anaconda Wild',
                    'description'   => 'Anaconda Wild',
                    'content'       => 'Anaconda Wild'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Anaconda Wild',
                    'description'   => 'Anaconda Wild',
                    'content'       => 'Anaconda Wild'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Anaconda Wild',
                    'description'   => 'Anaconda Wild',
                    'content'       => 'Anaconda Wild'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_panthpays_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Panther Pays',
                    'description'   => 'Panther Pays',
                    'content'       => 'Panther Pays'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Panther Pays',
                    'description'   => 'Panther Pays',
                    'content'       => 'Panther Pays'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Panther Pays',
                    'description'   => 'Panther Pays',
                    'content'       => 'Panther Pays'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_elady_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Eternal Lady',
                    'description'   => 'Eternal Lady',
                    'content'       => 'Eternal Lady'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Eternal Lady',
                    'description'   => 'Eternal Lady',
                    'content'       => 'Eternal Lady'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Eternal Lady',
                    'description'   => 'Eternal Lady',
                    'content'       => 'Eternal Lady'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_xjinfu_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jinfu Xingyun',
                    'description'   => 'Jinfu Xingyun',
                    'content'       => 'Jinfu Xingyun'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jinfu Xingyun',
                    'description'   => 'Jinfu Xingyun',
                    'content'       => 'Jinfu Xingyun'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jinfu Xingyun',
                    'description'   => 'Jinfu Xingyun',
                    'content'       => 'Jinfu Xingyun'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_squeen_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sky Queen',
                    'description'   => 'Sky Queen',
                    'content'       => 'Sky Queen'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sky Queen',
                    'description'   => 'Sky Queen',
                    'content'       => 'Sky Queen'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sky Queen',
                    'description'   => 'Sky Queen',
                    'content'       => 'Sky Queen'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_tigertdp_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tiger Turtle Dragon Phoenix',
                    'description'   => 'Tiger Turtle Dragon Phoenix',
                    'content'       => 'Tiger Turtle Dragon Phoenix'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tiger Turtle Dragon Phoenix',
                    'description'   => 'Tiger Turtle Dragon Phoenix',
                    'content'       => 'Tiger Turtle Dragon Phoenix'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tiger Turtle Dragon Phoenix',
                    'description'   => 'Tiger Turtle Dragon Phoenix',
                    'content'       => 'Tiger Turtle Dragon Phoenix'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_fatchoy_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fat Choy Choy Sun',
                    'description'   => 'Fat Choy Choy Sun',
                    'content'       => 'Fat Choy Choy Sun'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fat Choy Choy Sun',
                    'description'   => 'Fat Choy Choy Sun',
                    'content'       => 'Fat Choy Choy Sun'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fat Choy Choy Sun',
                    'description'   => 'Fat Choy Choy Sun',
                    'content'       => 'Fat Choy Choy Sun'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_jqw_ab_jp_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jin Qian Wa Jackpot',
                    'description'   => 'Jin Qian Wa Jackpot',
                    'content'       => 'Jin Qian Wa Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jin Qian Wa Jackpot',
                    'description'   => 'Jin Qian Wa Jackpot',
                    'content'       => 'Jin Qian Wa Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jin Qian Wa Jackpot',
                    'description'   => 'Jin Qian Wa Jackpot',
                    'content'       => 'Jin Qian Wa Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_rm_ab_jp_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Respin Mania Wu Shi Jackpot',
                    'description'   => 'Respin Mania Wu Shi Jackpot',
                    'content'       => 'Respin Mania Wu Shi Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Respin Mania Wu Shi Jackpot',
                    'description'   => 'Respin Mania Wu Shi Jackpot',
                    'content'       => 'Respin Mania Wu Shi Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Respin Mania Wu Shi Jackpot',
                    'description'   => 'Respin Mania Wu Shi Jackpot',
                    'content'       => 'Respin Mania Wu Shi Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_shctz_ab_jp_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Zhao Cai Tong Zi Jackpot',
                    'description'   => 'Zhao Cai Tong Zi Jackpot',
                    'content'       => 'Zhao Cai Tong Zi Jackpot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Zhao Cai Tong Zi Jackpot',
                    'description'   => 'Zhao Cai Tong Zi Jackpot',
                    'content'       => 'Zhao Cai Tong Zi Jackpot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Zhao Cai Tong Zi Jackpot',
                    'description'   => 'Zhao Cai Tong Zi Jackpot',
                    'content'       => 'Zhao Cai Tong Zi Jackpot'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_dbond_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Bond',
                    'description'   => 'Dragon Bond',
                    'content'       => 'Dragon Bond'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Bond',
                    'description'   => 'Dragon Bond',
                    'content'       => 'Dragon Bond'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Bond',
                    'description'   => 'Dragon Bond',
                    'content'       => 'Dragon Bond'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_jflong_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Jinfu Long',
                    'description'   => 'Jinfu Long',
                    'content'       => 'Jinfu Long'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Jinfu Long',
                    'description'   => 'Jinfu Long',
                    'content'       => 'Jinfu Long'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Jinfu Long',
                    'description'   => 'Jinfu Long',
                    'content'       => 'Jinfu Long'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_scqueen_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pharaoh’s Daughter',
                    'description'   => 'Pharaoh’s Daughter',
                    'content'       => 'Pharaoh’s Daughter'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pharaoh’s Daughter',
                    'description'   => 'Pharaoh’s Daughter',
                    'content'       => 'Pharaoh’s Daughter'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pharaoh’s Daughter',
                    'description'   => 'Pharaoh’s Daughter',
                    'content'       => 'Pharaoh’s Daughter'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_sjungle_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Savage Jungle',
                    'description'   => 'Savage Jungle',
                    'content'       => 'Savage Jungle'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Savage Jungle',
                    'description'   => 'Savage Jungle',
                    'content'       => 'Savage Jungle'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Savage Jungle',
                    'description'   => 'Savage Jungle',
                    'content'       => 'Savage Jungle'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'ljxy',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Long Jia Xiang Yun',
                    'description'   => 'Long Jia Xiang Yun',
                    'content'       => 'Long Jia Xiang Yun'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Long Jia Xiang Yun',
                    'description'   => 'Long Jia Xiang Yun',
                    'content'       => 'Long Jia Xiang Yun'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Long Jia Xiang Yun',
                    'description'   => 'Long Jia Xiang Yun',
                    'content'       => 'Long Jia Xiang Yun'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_ar_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Aztec Reel',
                    'description'   => 'Aztec Reel',
                    'content'       => 'Aztec Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Aztec Reel',
                    'description'   => 'Aztec Reel',
                    'content'       => 'Aztec Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Aztec Reel',
                    'description'   => 'Aztec Reel',
                    'content'       => 'Aztec Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_fr_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fire Reel',
                    'description'   => 'Fire Reel',
                    'content'       => 'Fire Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fire Reel',
                    'description'   => 'Fire Reel',
                    'content'       => 'Fire Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fire Reel',
                    'description'   => 'Fire Reel',
                    'content'       => 'Fire Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_mr_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Metal Reel',
                    'description'   => 'Metal Reel',
                    'content'       => 'Metal Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Metal Reel',
                    'description'   => 'Metal Reel',
                    'content'       => 'Metal Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Metal Reel',
                    'description'   => 'Metal Reel',
                    'content'       => 'Metal Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_rr_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Riverboat Reel',
                    'description'   => 'Riverboat Reel',
                    'content'       => 'Riverboat Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Riverboat Reel',
                    'description'   => 'Riverboat Reel',
                    'content'       => 'Riverboat Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Riverboat Reel',
                    'description'   => 'Riverboat Reel',
                    'content'       => 'Riverboat Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'pop_sw_wrl_skw',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Water Reel',
                    'description'   => 'Water Reel',
                    'content'       => 'Water Reel'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Water Reel',
                    'description'   => 'Water Reel',
                    'content'       => 'Water Reel'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Water Reel',
                    'description'   => 'Water Reel',
                    'content'       => 'Water Reel'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_bwizard_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Blue Wizard',
                    'description'   => 'Blue Wizard',
                    'content'       => 'Blue Wizard'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Blue Wizard',
                    'description'   => 'Blue Wizard',
                    'content'       => 'Blue Wizard'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Blue Wizard',
                    'description'   => 'Blue Wizard',
                    'content'       => 'Blue Wizard'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_tsgift_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tsai Shens Gift',
                    'description'   => 'Tsai Shens Gift',
                    'content'       => 'Tsai Shens Gift'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tsai Shens Gift',
                    'description'   => 'Tsai Shens Gift',
                    'content'       => 'Tsai Shens Gift'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tsai Shens Gift',
                    'description'   => 'Tsai Shens Gift',
                    'content'       => 'Tsai Shens Gift'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_tstacks_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tiger Stacks',
                    'description'   => 'Tiger Stacks',
                    'content'       => 'Tiger Stacks'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tiger Stacks',
                    'description'   => 'Tiger Stacks',
                    'content'       => 'Tiger Stacks'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tiger Stacks',
                    'description'   => 'Tiger Stacks',
                    'content'       => 'Tiger Stacks'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'gpas_nsshen_pop',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Ni Shu Shen Me',
                    'description'   => 'Ni Shu Shen Me',
                    'content'       => 'Ni Shu Shen Me'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Ni Shu Shen Me',
                    'description'   => 'Ni Shu Shen Me',
                    'content'       => 'Ni Shu Shen Me'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Ni Shu Shen Me',
                    'description'   => 'Ni Shu Shen Me',
                    'content'       => 'Ni Shu Shen Me'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'whk2',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'White King 2',
                    'description'   => 'White King 2',
                    'content'       => 'White King 2'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'White King 2',
                    'description'   => 'White King 2',
                    'content'       => 'White King 2'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'White King 2',
                    'description'   => 'White King 2',
                    'content'       => 'White King 2'
                ],
            ],
            'devices'       => [1, 2],
        ],
        [
            'platform_code' => self::CODE,
            'product_code'  => 'PT_Slot',
            'code'          => 'mnkmn',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['USD', 'VND', 'THB'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Monkey Mania',
                    'description'   => 'Monkey Mania',
                    'content'       => 'Monkey Mania'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Monkey Mania',
                    'description'   => 'Monkey Mania',
                    'content'       => 'Monkey Mania'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Monkey Mania',
                    'description'   => 'Monkey Mania',
                    'content'       => 'Monkey Mania'
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
