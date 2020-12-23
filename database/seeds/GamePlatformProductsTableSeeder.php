<?php

use App\Models\GamePlatformProduct;
use App\Models\GamePlatform;
use Illuminate\Database\Seeder;

class GamePlatformProductsTableSeeder extends Seeder
{

    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {
        GamePlatformProduct::query()->truncate();

        $data = [];

        # SA start
        $platform = GamePlatform::query()->where('code', 'SA')->first();

        # 真人
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'SA_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'SA_Live',
                    'description' => 'SA_Live',
                    'content'     => 'SA_Live',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'SA_Live',
                    'description' => 'SA_Live',
                    'content'     => 'SA_Live',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'SA_Live',
                    'description' => 'SA_Live',
                    'content'     => 'SA_Live',
                ],
            ],
            'currencies'    => ['VND', 'THB'],
            'devices'       => [1, 2],
        ];
        # SA end

        # SP start
        $platform = GamePlatform::query()->where('code', 'SP')->first();

        # 老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'SP_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'SP_Slot',
                    'description' => 'SP_Slot',
                    'content'     => 'SP_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'SP_Slot',
                    'description' => 'SP_Slot',
                    'content'     => 'SP_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'SP_Slot',
                    'description' => 'SP_Slot',
                    'content'     => 'SP_Slot',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 捕鱼
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'SP_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB',],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'SP_Fish',
                    'description' => 'SP_Fish',
                    'content'     => 'SP_Fish',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'SP_Fish',
                    'description' => 'SP_Fish',
                    'content'     => 'SP_Fish',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'SP_Fish',
                    'description' => 'SP_Fish',
                    'content'     => 'SP_Fish',
                ],
            ],
            'devices'       => [1, 2],
        ];
        # SP end

        # RTG start
        $platform = GamePlatform::query()->where('code', 'RTG')->first();

        # 老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'RTG_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'RTG_Slot',
                    'description' => 'RTG_Slot',
                    'content'     => 'RTG_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'RTG_Slot',
                    'description' => 'RTG_Slot',
                    'content'     => 'RTG_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'RTG_Slot',
                    'description' => 'RTG_Slot',
                    'content'     => 'RTG_Slot',
                ],
            ],
            'currencies'    => ['VND', 'THB'],
            'devices'       => [1, 2],
        ];

        # 捕鱼
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'RTG_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'RTG_Fish',
                    'description' => 'RTG_Fish',
                    'content'     => 'RTG_Fish',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'RTG_Fish',
                    'description' => 'RTG_Fish',
                    'content'     => 'RTG_Fish',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'RTG_Fish',
                    'description' => 'RTG_Fish',
                    'content'     => 'RTG_Fish',
                ],
            ],
            'devices'       => [1, 2],
        ];
        # RTG end

        # EBET start
        $platform = GamePlatform::query()->where('code', 'EBET')->first();

        # 真人
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'EBET_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'EBET_Live',
                    'description' => 'EBET_Live',
                    'content'     => 'EBET_Live',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'EBET_Live',
                    'description' => 'EBET_Live',
                    'content'     => 'EBET_Live',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'EBET_Live',
                    'description' => 'EBET_Live',
                    'content'     => 'EBET_Live',
                ],
            ],
            'devices'       => [1, 2],
        ];
        # EBET end

//        # SmartSoft start
//        $platform = GamePlatform::findByCode('SmartSoft');
//
//        # 老虎机
//        $data[] = [
//            'platform_code'  => $platform->code,
//            'code'           => 'SmartSoft_Slot',
//            'type'           => GamePlatformProduct::TYPE_SLOT,
//            'currencies'     => '[{"currency":"VND","name":"SmartSoft_Slot","description":"SmartSoft_Slot", "content":"SmartSoft_Slot"},{"currency":"THB","name":"SmartSoft_Slot","description":"SmartSoft_Slot", "content":"SmartSoft_Slot"}]',
//            'devices'        => '["1","2"]',
//        ];
//
//        # 捕鱼
//        $data[] = [
//            'platform_code'  => $platform->code,
//            'code'           => 'SmartSoft_Fish',
//            'type'           => GamePlatformProduct::TYPE_FISH,
//            'currencies'     => '[{"currency":"VND","name":"SmartSoft_Fish","description":"SmartSoft_Fish", "content":"SmartSoft_Fish"},{"currency":"THB","name":"SmartSoft_Fish","description":"SmartSoft_Fish", "content":"SmartSoft_Fish"}]',
//            'devices'        => '["1","2"]',
//        ];
        # SmartSoft end

        # ISB start
        $platform = GamePlatform::query()->where('code', 'ISB')->first();

        # 老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'ISB_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'ISB_Slot',
                    'description' => 'ISB_Slot',
                    'content'     => 'ISB_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'ISB_Slot',
                    'description' => 'ISB_Slot',
                    'content'     => 'ISB_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'ISB_Slot',
                    'description' => 'ISB_Slot',
                    'content'     => 'ISB_Slot',
                ],
            ],
            'devices'       => [1, 2],
        ];
        # ISB end

        # N2 start
        $platform = GamePlatform::query()->where('code', 'N2')->first();

        # 真人
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'N2_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'N2_Live',
                    'description' => 'N2_Live',
                    'content'     => 'N2_Live',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'N2_Live',
                    'description' => 'N2_Live',
                    'content'     => 'N2_Live',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'N2_Live',
                    'description' => 'N2_Live',
                    'content'     => 'N2_Live',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'N2_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'N2_Slot',
                    'description' => 'N2_Slot',
                    'content'     => 'N2_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'N2_Slot',
                    'description' => 'N2_Slot',
                    'content'     => 'N2_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'N2_Slot',
                    'description' => 'N2_Slot',
                    'content'     => 'N2_Slot',
                ],
            ],
            'devices'       => [1, 2],
        ];
        # N2 end

        # IBC start
        $platform = GamePlatform::query()->where('code', 'IBC')->first();

        # 体育
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'IBC_Sport',
            'type'          => GamePlatformProduct::TYPE_SPORT,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'IBC_Sport',
                    'description' => 'IBC_Sport',
                    'content'     => 'IBC_Sport',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'IBC_Sport',
                    'description' => 'IBC_Sport',
                    'content'     => 'IBC_Sport',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'IBC_Sport',
                    'description' => 'IBC_Sport',
                    'content'     => 'IBC_Sport',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # Lottery
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'IBC_Lottery',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'IBC_Lottery',
                    'description' => 'IBC_Lottery',
                    'content'     => 'IBC_Lottery',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'IBC_Lottery',
                    'description' => 'IBC_Lottery',
                    'content'     => 'IBC_Lottery',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'IBC_Lottery',
                    'description' => 'IBC_Lottery',
                    'content'     => 'IBC_Lottery',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # E-Sports
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'IBC_ESport',
            'type'          => GamePlatformProduct::TYPE_ESPORT,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'IBC_ESport',
                    'description' => 'IBC_ESport',
                    'content'     => 'IBC_ESport',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'IBC_ESport',
                    'description' => 'IBC_ESport',
                    'content'     => 'IBC_ESport',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'IBC_ESport',
                    'description' => 'IBC_ESport',
                    'content'     => 'IBC_ESport',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # Virtual Sports
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'IBC_Virtual',
            'type'          => GamePlatformProduct::TYPE_VIRTUAL,
            'currencies'    => ['VND', 'THB'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'IBC_Virtual_Sports',
                    'description' => 'IBC_Virtual_Sports',
                    'content'     => 'IBC_Virtual_Sports',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'IBC_Virtual_Sports',
                    'description' => 'IBC_Virtual_Sports',
                    'content'     => 'IBC_Virtual_Sports',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'IBC_Virtual_Sports',
                    'description' => 'IBC_Virtual_Sports',
                    'content'     => 'IBC_Virtual_Sports',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # IBC end

        # S128 start
        $platform = GamePlatform::query()->where('code', 'S128')->first();

        # Games
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'S128_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'Cock_Fight',
                    'description' => 'Cock_Fight',
                    'content'     => 'Cock_Fight',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'Cock_Fight',
                    'description' => 'Cock_Fight',
                    'content'     => 'Cock_Fight',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'Cock_Fight',
                    'description' => 'Cock_Fight',
                    'content'     => 'Cock_Fight',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # S128 end

        # GPI start
        $platform = GamePlatform::query()->where('code', 'GPI')->first();

        # 真人
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_Live',
                    'description' => 'GPI_Live',
                    'content'     => 'GPI_Live',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_Live',
                    'description' => 'GPI_Live',
                    'content'     => 'GPI_Live',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_Live',
                    'description' => 'GPI_Live',
                    'content'     => 'GPI_Live',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_Slot',
                    'description' => 'GPI_Slot',
                    'content'     => 'GPI_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_Slot',
                    'description' => 'GPI_Slot',
                    'content'     => 'GPI_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_Slot',
                    'description' => 'GPI_Slot',
                    'content'     => 'GPI_Slot',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # lottery
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_Lottery',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_Lottery',
                    'description' => 'GPI_Lottery',
                    'content'     => 'GPI_Lottery',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_Lottery',
                    'description' => 'GPI_Lottery',
                    'content'     => 'GPI_Lottery',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_Lottery',
                    'description' => 'GPI_Lottery',
                    'content'     => 'GPI_Lottery',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_SODE',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_SODE',
                    'description' => 'GPI_SODE',
                    'content'     => 'GPI_SODE',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_SODE',
                    'description' => 'GPI_SODE',
                    'content'     => 'GPI_SODE',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_SODE',
                    'description' => 'GPI_SODE',
                    'content'     => 'GPI_SODE',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_THLT',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_THLT',
                    'description' => 'GPI_THLT',
                    'content'     => 'GPI_THLT',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_THLT',
                    'description' => 'GPI_THLT',
                    'content'     => 'GPI_THLT',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_THLT',
                    'description' => 'GPI_THLT',
                    'content'     => 'GPI_THLT',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # games
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_Fish',
                    'description' => 'GPI_Fish',
                    'content'     => 'GPI_Fish',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_Fish',
                    'description' => 'GPI_Fish',
                    'content'     => 'GPI_Fish',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_Fish',
                    'description' => 'GPI_Fish',
                    'content'     => 'GPI_Fish',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # p2p
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GPI_P2P',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GPI_P2P',
                    'description' => 'GPI_P2P',
                    'content'     => 'GPI_P2P',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GPI_P2P',
                    'description' => 'GPI_P2P',
                    'content'     => 'GPI_P2P',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GPI_P2P',
                    'description' => 'GPI_P2P',
                    'content'     => 'GPI_P2P',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # GPI end

        # GG start
        $platform = GamePlatform::query()->where('code', 'GG')->first();

        # Slot
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GG_Slot',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GG_Slot',
                    'description' => 'GG_Slot',
                    'content'     => 'GG_Slot',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GG_Slot',
                    'description' => 'GG_Slot',
                    'content'     => 'GG_Slot',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GG_Slot',
                    'description' => 'GG_Slot',
                    'content'     => 'GG_Slot',
                ],
            ],
            'devices'       => [1, 2],
        ];
        # Games
        $data[] = [
            'platform_code' => $platform->code,
            'code'          => 'GG_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB'],
            'languages'    => [
                [
                    'language'    => 'en-US',
                    'name'        => 'GG_Fish',
                    'description' => 'GG_Fish',
                    'content'     => 'GG_Fish',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'GG_Fish',
                    'description' => 'GG_Fish',
                    'content'     => 'GG_Fish',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'GG_Fish',
                    'description' => 'GG_Fish',
                    'content'     => 'GG_Fish',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # GG end

        foreach ($data as $item) {
            if ($product = GamePlatformProduct::query()->where('code', $item['code'])->first()) {
                $product->update($item);
            } else {
                GamePlatformProduct::query()->create($item);
            }
        }
    }
}
