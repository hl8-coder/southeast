<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\Game;

class GamesManualTableSeeder extends Seeder
{
    /**
     * Auto generated seed file
     *
     * @return void
     */
    public function run()
    {

        $data = [];
        # SA start
        $platform = GamePlatform::findByCode('SA');

        # 真人
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LIVE);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'SA_LIVE',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'SA_Live',
                    'description'   => 'SA_Live',
                    'content'       => 'SA_Live'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'SA_Live',
                    'description'   => 'SA_Live',
                    'content'       => 'SA_Live'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'SA_Live',
                    'description'   => 'SA_Live',
                    'content'       => 'SA_Live'
                ],
            ],
            'devices'       => [1, 2],
        ];
        # SA end

        # SP start
        $platform = GamePlatform::findByCode('SP');

        # 捕鱼
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_FISH);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'EG-FISHING-001',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'SP Fish',
                    'description'   => 'SP Fish',
                    'content'       => 'SP Fish'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'SP Fish',
                    'description'   => 'SP Fish',
                    'content'       => 'SP Fish'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'SP Fish',
                    'description'   => 'SP Fish',
                    'content'       => 'SP Fish'
                ],
            ],
            'devices'       => [1, 2],
        ];
        # SP end

        # RTG start
        $platform = GamePlatform::findByCode('RTG');

        # 捕鱼
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_FISH);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '2228225',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Banana Jones',
                    'description'   => 'Banana Jones',
                    'content'       => 'Banana Jones'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Banana Jones',
                    'description'   => 'Banana Jones',
                    'content'       => 'Banana Jones'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Banana Jones',
                    'description'   => 'Banana Jones',
                    'content'       => 'Banana Jones'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '2162689',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fish Catch',
                    'description'   => 'Fish Catch',
                    'content'       => 'Fish Catch'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fish Catch',
                    'description'   => 'Fish Catch',
                    'content'       => 'Fish Catch'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fish Catch',
                    'description'   => 'Fish Catch',
                    'content'       => 'Fish Catch'
                ],
            ],
            'devices'       => [1, 2],
        ];
        # RTG end

        # EBET start
        $platform = GamePlatform::findByCode('EBET');

        # 真人
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LIVE);

        # 大厅
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'EBET_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat',
                    'description'   => 'Baccarat',
                    'content'       => 'Baccarat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat',
                    'description'   => 'Baccarat',
                    'content'       => 'Baccarat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat',
                    'description'   => 'Baccarat',
                    'content'       => 'Baccarat'
                ],
            ],
            'devices'       => [1, 2],
        ];

        #百家乐
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat',
                    'description'   => 'Baccarat',
                    'content'       => 'Baccarat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat',
                    'description'   => 'Baccarat',
                    'content'       => 'Baccarat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat',
                    'description'   => 'Baccarat',
                    'content'       => 'Baccarat'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 龙虎
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '2',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'     => ['VND', 'THB', 'USD'],
            'languages'     => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Tiger',
                    'description'   => 'Dragon Tiger',
                    'content'       => 'Dragon Tiger'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Tiger',
                    'description'   => 'Dragon Tiger',
                    'content'       => 'Dragon Tiger'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Tiger',
                    'description'   => 'Dragon Tiger',
                    'content'       => 'Dragon Tiger'
                ],
            ],
            'devices'        => [1, 2],
        ];

        # 骰宝
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '3',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'     => ['VND', 'THB', 'USD'],
            'languages'     => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sic Bo',
                    'description'   => 'Sic Bo',
                    'content'       => 'Sic Bo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sic Bo',
                    'description'   => 'Sic Bo',
                    'content'       => 'Sic Bo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sic Bo',
                    'description'   => 'Sic Bo',
                    'content'       => 'Sic Bo'
                ],
            ],
            'devices'        => [1, 2],
        ];

        # 轮盘
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '4',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
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
        ];

        # 老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '5',
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Slot',
                    'description'   => 'Slot',
                    'content'       => 'Slot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Slot',
                    'description'   => 'Slot',
                    'content'       => 'Slot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Slot',
                    'description'   => 'Slot',
                    'content'       => 'Slot'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 试玩老虎机
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '6',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Trial slot',
                    'description'   => 'Trial slot',
                    'content'       => 'Trial slot'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Trial slot',
                    'description'   => 'Trial slot',
                    'content'       => 'Trial slot'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Trial slot',
                    'description'   => 'Trial slot',
                    'content'       => 'Trial slot'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 区块链百家乐
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '7',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Block Chain Baccarat',
                    'description'   => 'Block Chain Baccarat',
                    'content'       => 'Block Chain Baccarat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Block Chain Baccarat',
                    'description'   => 'Block Chain Baccarat',
                    'content'       => 'Block Chain Baccarat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Block Chain Baccarat',
                    'description'   => 'Block Chain Baccarat',
                    'content'       => 'Block Chain Baccarat'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 牛牛
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '8',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'NiuNiu',
                    'description'   => 'NiuNiu',
                    'content'       => 'NiuNiu'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'NiuNiu',
                    'description'   => 'NiuNiu',
                    'content'       => 'NiuNiu'
                ],
            ],
            'devices'       => [1, 2],
        ];
        # EBET end

        # N2 start
        $platform = GamePlatform::findByCode('N2');

        # 真人
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LIVE);

        # 大厅
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'N2_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat Commission',
                    'description'   => 'Baccarat Commission',
                    'content'       => 'Baccarat Commission'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat Commission',
                    'description'   => 'Baccarat Commission',
                    'content'       => 'Baccarat Commission'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat Commission',
                    'description'   => 'Baccarat Commission',
                    'content'       => 'Baccarat Commission'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 百家乐
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '90091',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat Commission',
                    'description'   => 'Baccarat Commission',
                    'content'       => 'Baccarat Commission'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat Commission',
                    'description'   => 'Baccarat Commission',
                    'content'       => 'Baccarat Commission'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat Commission',
                    'description'   => 'Baccarat Commission',
                    'content'       => 'Baccarat Commission'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 免佣百家乐
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '90092',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Baccarat Non-Commission',
                    'description'   => 'Baccarat Non-Commission',
                    'content'       => 'Baccarat Non-Commission'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Baccarat Non-Commission',
                    'description'   => 'Baccarat Non-Commission',
                    'content'       => 'Baccarat Non-Commission'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Baccarat Non-Commission',
                    'description'   => 'Baccarat Non-Commission',
                    'content'       => 'Baccarat Non-Commission'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 轮盘
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '50002',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
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
        ];

        # 骰宝
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '60001',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sicbo',
                    'description'   => 'Sicbo',
                    'content'       => 'Sicbo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sicbo',
                    'description'   => 'Sicbo',
                    'content'       => 'Sicbo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sicbo',
                    'description'   => 'Sicbo',
                    'content'       => 'Sicbo'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 老虎机
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_SLOT);

        # 电子轮盘
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '51002',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Roulette',
                    'description'   => 'RNG Roulette',
                    'content'       => 'RNG Roulette'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Roulette',
                    'description'   => 'RNG Roulette',
                    'content'       => 'RNG Roulette'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Roulette',
                    'description'   => 'RNG Roulette',
                    'content'       => 'RNG Roulette'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子轮盘手动
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '52002',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Roulette PT',
                    'description'   => 'RNG Roulette PT',
                    'content'       => 'RNG Roulette PT'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Roulette PT',
                    'description'   => 'RNG Roulette PT',
                    'content'       => 'RNG Roulette PT'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Roulette PT',
                    'description'   => 'RNG Roulette PT',
                    'content'       => 'RNG Roulette PT'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子骰宝
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '61001',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Sicbo',
                    'description'   => 'RNG Sicbo',
                    'content'       => 'RNG Sicbo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Sicbo',
                    'description'   => 'RNG Sicbo',
                    'content'       => 'RNG Sicbo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Sicbo',
                    'description'   => 'RNG Sicbo',
                    'content'       => 'RNG Sicbo'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子骰宝手动
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'code'          => '62001',
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Sicbo PT',
                    'description'   => 'RNG Sicbo PT',
                    'content'       => 'RNG Sicbo PT'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Sicbo PT',
                    'description'   => 'RNG Sicbo PT',
                    'content'       => 'RNG Sicbo PT'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Sicbo PT',
                    'description'   => 'RNG Sicbo PT',
                    'content'       => 'RNG Sicbo PT'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子百家乐
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '91091',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Baccarat Commission',
                    'description'   => 'RNG Baccarat Commission',
                    'content'       => 'RNG Baccarat Commission'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Baccarat Commission',
                    'description'   => 'RNG Baccarat Commission',
                    'content'       => 'RNG Baccarat Commission'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Baccarat Commission',
                    'description'   => 'RNG Baccarat Commission',
                    'content'       => 'RNG Baccarat Commission'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子免佣百家乐
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '91092',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Baccarat Non-Commission',
                    'description'   => 'RNG Baccarat Non-Commission',
                    'content'       => 'RNG Baccarat Non-Commission'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Baccarat Non-Commission',
                    'description'   => 'RNG Baccarat Non-Commission',
                    'content'       => 'RNG Baccarat Non-Commission'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Baccarat Non-Commission',
                    'description'   => 'RNG Baccarat Non-Commission',
                    'content'       => 'RNG Baccarat Non-Commission'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子传统 21 点手动
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '110001',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Traditional Blackjack PT',
                    'description'   => 'RNG Traditional Blackjack PT',
                    'content'       => 'RNG Traditional Blackjack PT'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Traditional Blackjack PT',
                    'description'   => 'RNG Traditional Blackjack PT',
                    'content'       => 'RNG Traditional Blackjack PT'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Traditional Blackjack PT',
                    'description'   => 'RNG Traditional Blackjack PT',
                    'content'       => 'RNG Traditional Blackjack PT'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子淘金 21 点手动
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '110002',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Free Bet Blackjack PT',
                    'description'   => 'RNG Free Bet Blackjack PT',
                    'content'       => 'RNG Free Bet Blackjack PT'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Free Bet Blackjack PT',
                    'description'   => 'RNG Free Bet Blackjack PT',
                    'content'       => 'RNG Free Bet Blackjack PT'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Free Bet Blackjack PT',
                    'description'   => 'RNG Free Bet Blackjack PT',
                    'content'       => 'RNG Free Bet Blackjack PT'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电子换牌 21 点手动
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '110003',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Blackjack Switch PT',
                    'description'   => 'RNG Blackjack Switch PT',
                    'content'       => 'RNG Blackjack Switch PT'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Blackjack Switch PT',
                    'description'   => 'RNG Blackjack Switch PT',
                    'content'       => 'RNG Blackjack Switch PT'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Blackjack Switch PT',
                    'description'   => 'RNG Blackjack Switch PT',
                    'content'       => 'RNG Blackjack Switch PT'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # N2 end

        # IBC start
        $platform = GamePlatform::findByCode('IBC');

        # 体育
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_SPORT);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'ibc',
            'type'          => GamePlatformProduct::TYPE_SPORT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'IBC Sport',
                    'description'   => 'IBC Sport',
                    'content'       => 'IBC Sport'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'IBC Sport',
                    'description'   => 'IBC Sport',
                    'content'       => 'IBC Sport'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'IBC Sport',
                    'description'   => 'IBC Sport',
                    'content'       => 'IBC Sport'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # lottery
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LOTTERY);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '161',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Number Game',
                    'description'   => 'Number Game',
                    'content'       => 'Number Game'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Number Game',
                    'description'   => 'Number Game',
                    'content'       => 'Number Game'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Number Game',
                    'description'   => 'Number Game',
                    'content'       => 'Number Game'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '164',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Happy 5',
                    'description'   => 'Happy 5',
                    'content'       => 'Happy 5'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Happy 5',
                    'description'   => 'Happy 5',
                    'content'       => 'Happy 5'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Happy 5',
                    'description'   => 'Happy 5',
                    'content'       => 'Happy 5'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '202',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'RNG Keno',
                    'description'   => 'RNG Keno',
                    'content'       => 'RNG Keno'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'RNG Keno',
                    'description'   => 'RNG Keno',
                    'content'       => 'RNG Keno'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'RNG Keno',
                    'description'   => 'RNG Keno',
                    'content'       => 'RNG Keno'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 电竞转体育
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LOTTERY);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '43',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'E Sports',
                    'description'   => 'E Sports',
                    'content'       => 'E Sports',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'E Sports',
                    'description'   => 'E Sports',
                    'content'       => 'E Sports',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'E Sports',
                    'description'   => 'E Sports',
                    'content'       => 'E Sports',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟体育转体育
        # 足球
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LOTTERY);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '180',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Soccer',
                    'description'   => 'Virtual Soccer',
                    'content'       => 'Virtual Soccer'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Soccer',
                    'description'   => 'Virtual Soccer',
                    'content'       => 'Virtual Soccer'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Soccer',
                    'description'   => 'Virtual Soccer',
                    'content'       => 'Virtual Soccer'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟赛马
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '181',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Horse Racing',
                    'description'   => 'Virtual Horse Racing',
                    'content'       => 'Virtual Horse Racing'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Horse Racing',
                    'description'   => 'Virtual Horse Racing',
                    'content'       => 'Virtual Horse Racing'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Horse Racing',
                    'description'   => 'Virtual Horse Racing',
                    'content'       => 'Virtual Horse Racing'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟赛狗
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '182',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Greyhound',
                    'description'   => 'Virtual Greyhound',
                    'content'       => 'Virtual Greyhound'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Greyhound',
                    'description'   => 'Virtual Greyhound',
                    'content'       => 'Virtual Greyhound'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Greyhound',
                    'description'   => 'Virtual Greyhound',
                    'content'       => 'Virtual Greyhound'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟沙地摩托车
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '183',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Speedway',
                    'description'   => 'Virtual Speedway',
                    'content'       => 'Virtual Speedway'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Speedway',
                    'description'   => 'Virtual Speedway',
                    'content'       => 'Virtual Speedway'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Speedway',
                    'description'   => 'Virtual Speedway',
                    'content'       => 'Virtual Speedway'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟赛车
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '184',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual F1',
                    'description'   => 'Virtual F1',
                    'content'       => 'Virtual F1'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual F1',
                    'description'   => 'Virtual F1',
                    'content'       => 'Virtual F1'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual F1',
                    'description'   => 'Virtual F1',
                    'content'       => 'Virtual F1'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟自行车
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '185',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Cycling',
                    'description'   => 'Virtual Cycling',
                    'content'       => 'Virtual Cycling'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Cycling',
                    'description'   => 'Virtual Cycling',
                    'content'       => 'Virtual Cycling'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Cycling',
                    'description'   => 'Virtual Cycling',
                    'content'       => 'Virtual Cycling'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟网球
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '186',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Tennis',
                    'description'   => 'Virtual Tennis',
                    'content'       => 'Virtual Tennis'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Tennis',
                    'description'   => 'Virtual Tennis',
                    'content'       => 'Virtual Tennis'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Tennis',
                    'description'   => 'Virtual Tennis',
                    'content'       => 'Virtual Tennis'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟足球联赛
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '190',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Soccer League',
                    'description'   => 'Virtual Soccer League',
                    'content'       => 'Virtual Soccer League'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Soccer League',
                    'description'   => 'Virtual Soccer League',
                    'content'       => 'Virtual Soccer League'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Soccer League',
                    'description'   => 'Virtual Soccer League',
                    'content'       => 'Virtual Soccer League'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟足球国家杯
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '191',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Soccer Nation',
                    'description'   => 'Virtual Soccer Nation',
                    'content'       => 'Virtual Soccer Nation'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Soccer Nation',
                    'description'   => 'Virtual Soccer Nation',
                    'content'       => 'Virtual Soccer Nation'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Soccer Nation',
                    'description'   => 'Virtual Soccer Nation',
                    'content'       => 'Virtual Soccer Nation'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟足球国家杯
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '192',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Soccer World Cup',
                    'description'   => 'Virtual Soccer World Cup',
                    'content'       => 'Virtual Soccer World Cup'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Soccer World Cup',
                    'description'   => 'Virtual Soccer World Cup',
                    'content'       => 'Virtual Soccer World Cup'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Soccer World Cup',
                    'description'   => 'Virtual Soccer World Cup',
                    'content'       => 'Virtual Soccer World Cup'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟篮球
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '193',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Basketball',
                    'description'   => 'Virtual Basketball',
                    'content'       => 'Virtual Basketball'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Basketball',
                    'description'   => 'Virtual Basketball',
                    'content'       => 'Virtual Basketball'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Basketball',
                    'description'   => 'Virtual Basketball',
                    'content'       => 'Virtual Basketball'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟足球亚洲杯
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '194',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Soccer Asian Cup',
                    'description'   => 'Virtual Soccer Asian Cup',
                    'content'       => 'Virtual Soccer Asian Cup'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Soccer Asian Cup',
                    'description'   => 'Virtual Soccer Asian Cup',
                    'content'       => 'Virtual Soccer Asian Cup'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Soccer Asian Cup',
                    'description'   => 'Virtual Soccer Asian Cup',
                    'content'       => 'Virtual Soccer Asian Cup'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟赛狗
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '195',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Dog',
                    'description'   => 'Virtual Dog',
                    'content'       => 'Virtual Dog'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Dog',
                    'description'   => 'Virtual Dog',
                    'content'       => 'Virtual Dog'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Dog',
                    'description'   => 'Virtual Dog',
                    'content'       => 'Virtual Dog'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 虚拟网球
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '196',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Tennis',
                    'description'   => 'Virtual Tennis',
                    'content'       => 'Virtual Tennis'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Tennis',
                    'description'   => 'Virtual Tennis',
                    'content'       => 'Virtual Tennis'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Tennis',
                    'description'   => 'Virtual Tennis',
                    'content'       => 'Virtual Tennis'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # Virtual Sports Parlay
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '199',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Sports Parlay',
                    'description'   => 'Virtual Sports Parlay',
                    'content'       => 'Virtual Sports Parlay'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Sports Parlay',
                    'description'   => 'Virtual Sports Parlay',
                    'content'       => 'Virtual Sports Parlay'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Sports Parlay',
                    'description'   => 'Virtual Sports Parlay',
                    'content'       => 'Virtual Sports Parlay'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # IBC end

        # S128 start
        $platform = GamePlatform::findByCode('S128');

        # Games
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_FISH);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 's128',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'cock fight',
                    'description'   => 'cock fight',
                    'content'       => 'cock fight'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'cock fight',
                    'description'   => 'cock fight',
                    'content'       => 'cock fight'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'cock fight',
                    'description'   => 'cock fight',
                    'content'       => 'cock fight'
                ],
            ],
            'devices'       => [1, 2],
        ];
        # S128 end

        # GPI start
        $platform = GamePlatform::findByCode('GPI');

        # 真人
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LIVE);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'GPI_Live',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'GPI Live',
                    'description'   => 'GPI Live',
                    'content'       => 'GPI Live',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'GPI Live',
                    'description'   => 'GPI Live',
                    'content'       => 'GPI Live',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'GPI Live',
                    'description'   => 'GPI Live',
                    'content'       => 'GPI Live',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Commision Baccarat 1',
                    'description'   => 'Commision Baccarat 1',
                    'content'       => 'Commision Baccarat 1',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Commision Baccarat 1',
                    'description'   => 'Commision Baccarat 1',
                    'content'       => 'Commision Baccarat 1',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Commision Baccarat 1',
                    'description'   => 'Commision Baccarat 1',
                    'content'       => 'Commision Baccarat 1',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '52',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Commision Baccarat 2',
                    'description'   => 'Commision Baccarat 2',
                    'content'       => 'Commision Baccarat 2',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Commision Baccarat 2',
                    'description'   => 'Commision Baccarat 2',
                    'content'       => 'Commision Baccarat 2',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Commision Baccarat 2',
                    'description'   => 'Commision Baccarat 2',
                    'content'       => 'Commision Baccarat 2',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '3',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Commision Baccarat 3',
                    'description'   => 'Commision Baccarat 3',
                    'content'       => 'Commision Baccarat 3',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Commision Baccarat 3',
                    'description'   => 'Commision Baccarat 3',
                    'content'       => 'Commision Baccarat 3',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Commision Baccarat 3',
                    'description'   => 'Commision Baccarat 3',
                    'content'       => 'Commision Baccarat 3',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '3',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Commision Baccarat 3',
                    'description'   => 'Commision Baccarat 3',
                    'content'       => 'Commision Baccarat 3',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Commision Baccarat 3',
                    'description'   => 'Commision Baccarat 3',
                    'content'       => 'Commision Baccarat 3',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Commision Baccarat 3',
                    'description'   => 'Commision Baccarat 3',
                    'content'       => 'Commision Baccarat 3',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '31',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'No Commision Baccarat 1',
                    'description'   => 'No Commision Baccarat 1',
                    'content'       => 'No Commision Baccarat 1',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'No Commision Baccarat 1',
                    'description'   => 'No Commision Baccarat 1',
                    'content'       => 'No Commision Baccarat 1',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'No Commision Baccarat 1',
                    'description'   => 'No Commision Baccarat 1',
                    'content'       => 'No Commision Baccarat 1',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '82',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'No Commision Baccarat 2',
                    'description'   => 'No Commision Baccarat 2',
                    'content'       => 'No Commision Baccarat 2',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'No Commision Baccarat 2',
                    'description'   => 'No Commision Baccarat 2',
                    'content'       => 'No Commision Baccarat 2',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'No Commision Baccarat 2',
                    'description'   => 'No Commision Baccarat 2',
                    'content'       => 'No Commision Baccarat 2',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '33',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'No Commision Baccarat 3',
                    'description'   => 'No Commision Baccarat 3',
                    'content'       => 'No Commision Baccarat 3',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'No Commision Baccarat 3',
                    'description'   => 'No Commision Baccarat 3',
                    'content'       => 'No Commision Baccarat 3',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'No Commision Baccarat 3',
                    'description'   => 'No Commision Baccarat 3',
                    'content'       => 'No Commision Baccarat 3',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '113',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Super 98 Baccarat',
                    'description'   => 'Super 98 Baccarat',
                    'content'       => 'Super 98 Baccarat',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Super 98 Baccarat',
                    'description'   => 'Super 98 Baccarat',
                    'content'       => 'Super 98 Baccarat',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Super 98 Baccarat',
                    'description'   => 'Super 98 Baccarat',
                    'content'       => 'Super 98 Baccarat',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '223',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fabulous 4 Baccarat',
                    'description'   => 'Fabulous 4 Baccarat',
                    'content'       => 'Fabulous 4 Baccarat',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fabulous 4 Baccarat',
                    'description'   => 'Fabulous 4 Baccarat',
                    'content'       => 'Fabulous 4 Baccarat',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fabulous 4 Baccarat',
                    'description'   => 'Fabulous 4 Baccarat',
                    'content'       => 'Fabulous 4 Baccarat',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '61',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'C Squeeze Baccarat',
                    'description'   => 'C Squeeze Baccarat',
                    'content'       => 'C Squeeze Baccarat',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'C Squeeze Baccarat',
                    'description'   => 'C Squeeze Baccarat',
                    'content'       => 'C Squeeze Baccarat',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'C Squeeze Baccarat',
                    'description'   => 'C Squeeze Baccarat',
                    'content'       => 'C Squeeze Baccarat',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '91',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'NC Squeeze Baccarat',
                    'description'   => 'NC Squeeze Baccarat',
                    'content'       => 'NC Squeeze Baccarat',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'NC Squeeze Baccarat',
                    'description'   => 'NC Squeeze Baccarat',
                    'content'       => 'NC Squeeze Baccarat',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'NC Squeeze Baccarat',
                    'description'   => 'NC Squeeze Baccarat',
                    'content'       => 'NC Squeeze Baccarat',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '4',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Tiger 1',
                    'description'   => 'Dragon Tiger 1',
                    'content'       => 'Dragon Tiger 1',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Tiger 1',
                    'description'   => 'Dragon Tiger 1',
                    'content'       => 'Dragon Tiger 1',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Tiger 1',
                    'description'   => 'Dragon Tiger 1',
                    'content'       => 'Dragon Tiger 1',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '14',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Dragon Tiger 2',
                    'description'   => 'Dragon Tiger 2',
                    'content'       => 'Dragon Tiger 2',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Dragon Tiger 2',
                    'description'   => 'Dragon Tiger 2',
                    'content'       => 'Dragon Tiger 2',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Dragon Tiger 2',
                    'description'   => 'Dragon Tiger 2',
                    'content'       => 'Dragon Tiger 2',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '5',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Sicbo 1',
                    'description'   => 'Sicbo 1',
                    'content'       => 'Sicbo 1',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Sicbo 1',
                    'description'   => 'Sicbo 1',
                    'content'       => 'Sicbo 1',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Sicbo 1',
                    'description'   => 'Sicbo 1',
                    'content'       => 'Sicbo 1',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '6',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Roulette 1',
                    'description'   => 'Roulette 1',
                    'content'       => 'Roulette 1',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Roulette 1',
                    'description'   => 'Roulette 1',
                    'content'       => 'Roulette 1',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Roulette 1',
                    'description'   => 'Roulette 1',
                    'content'       => 'Roulette 1',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '316',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Roulette 2',
                    'description'   => 'Roulette 2',
                    'content'       => 'Roulette 2',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Roulette 2',
                    'description'   => 'Roulette 2',
                    'content'       => 'Roulette 2',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Roulette 2',
                    'description'   => 'Roulette 2',
                    'content'       => 'Roulette 2',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '68',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Super 3 Pictures',
                    'description'   => 'Super 3 Pictures',
                    'content'       => 'Super 3 Pictures',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Super 3 Pictures',
                    'description'   => 'Super 3 Pictures',
                    'content'       => 'Super 3 Pictures',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Super 3 Pictures',
                    'description'   => 'Super 3 Pictures',
                    'content'       => 'Super 3 Pictures',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '59',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Super Color Sicbo',
                    'description'   => 'Super Color Sicbo',
                    'content'       => 'Super Color Sicbo',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Super Color Sicbo',
                    'description'   => 'Super Color Sicbo',
                    'content'       => 'Super Color Sicbo',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Super Color Sicbo',
                    'description'   => 'Super Color Sicbo',
                    'content'       => 'Super Color Sicbo',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '60',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Blackjack',
                    'description'   => 'Blackjack',
                    'content'       => 'Blackjack',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Blackjack',
                    'description'   => 'Blackjack',
                    'content'       => 'Blackjack',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Blackjack',
                    'description'   => 'Blackjack',
                    'content'       => 'Blackjack',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '72',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Super Fantan',
                    'description'   => 'Super Fantan',
                    'content'       => 'Super Fantan',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Super Fantan',
                    'description'   => 'Super Fantan',
                    'content'       => 'Super Fantan',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Super Fantan',
                    'description'   => 'Super Fantan',
                    'content'       => 'Super Fantan',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '67',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Super Hi-Lo',
                    'description'   => 'Super Hi-Lo',
                    'content'       => 'Super Hi-Lo',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Super Hi-Lo',
                    'description'   => 'Super Hi-Lo',
                    'content'       => 'Super Hi-Lo',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Super Hi-Lo',
                    'description'   => 'Super Hi-Lo',
                    'content'       => 'Super Hi-Lo',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '18',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky Baccarat',
                    'description'   => 'Lucky Baccarat',
                    'content'       => 'Lucky Baccarat',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky Baccarat',
                    'description'   => 'Lucky Baccarat',
                    'content'       => 'Lucky Baccarat',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky Baccarat',
                    'description'   => 'Lucky Baccarat',
                    'content'       => 'Lucky Baccarat',
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '25',
            'type'          => GamePlatformProduct::TYPE_LIVE,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fish Prawn Crab',
                    'description'   => 'Fish Prawn Crab',
                    'content'       => 'Fish Prawn Crab',
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fish Prawn Crab',
                    'description'   => 'Fish Prawn Crab',
                    'content'       => 'Fish Prawn Crab',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fish Prawn Crab',
                    'description'   => 'Fish Prawn Crab',
                    'content'       => 'Fish Prawn Crab',
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 老虎机
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_SLOT);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1001',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Commision Baccarat 1',
                    'description'   => 'Virtual Commision Baccarat 1',
                    'content'       => 'Virtual Commision Baccarat 1'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Commision Baccarat 1',
                    'description'   => 'Virtual Commision Baccarat 1',
                    'content'       => 'Virtual Commision Baccarat 1'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Commision Baccarat 1',
                    'description'   => 'Virtual Commision Baccarat 1',
                    'content'       => 'Virtual Commision Baccarat 1'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1031',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual No Commision Baccarat 1',
                    'description'   => 'Virtual No Commision Baccarat 1',
                    'content'       => 'Virtual No Commision Baccarat 1'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual No Commision Baccarat 1',
                    'description'   => 'Virtual No Commision Baccarat 1',
                    'content'       => 'Virtual No Commision Baccarat 1'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual No Commision Baccarat 1',
                    'description'   => 'Virtual No Commision Baccarat 1',
                    'content'       => 'Virtual No Commision Baccarat 1'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1061',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual C Squeeze Baccarat 1',
                    'description'   => 'Virtual C Squeeze Baccarat 1',
                    'content'       => 'Virtual C Squeeze Baccarat 1'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual C Squeeze Baccarat 1',
                    'description'   => 'Virtual C Squeeze Baccarat 1',
                    'content'       => 'Virtual C Squeeze Baccarat 1'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual C Squeeze Baccarat 1',
                    'description'   => 'Virtual C Squeeze Baccarat 1',
                    'content'       => 'Virtual C Squeeze Baccarat 1'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1091',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual NC Squeeze Baccarat 1',
                    'description'   => 'Virtual NC Squeeze Baccarat 1',
                    'content'       => 'Virtual NC Squeeze Baccarat 1'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual NC Squeeze Baccarat 1',
                    'description'   => 'Virtual NC Squeeze Baccarat 1',
                    'content'       => 'Virtual NC Squeeze Baccarat 1'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual NC Squeeze Baccarat 1',
                    'description'   => 'Virtual NC Squeeze Baccarat 1',
                    'content'       => 'Virtual NC Squeeze Baccarat 1'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1091',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual NC Squeeze Baccarat 1',
                    'description'   => 'Virtual NC Squeeze Baccarat 1',
                    'content'       => 'Virtual NC Squeeze Baccarat 1'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual NC Squeeze Baccarat 1',
                    'description'   => 'Virtual NC Squeeze Baccarat 1',
                    'content'       => 'Virtual NC Squeeze Baccarat 1'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual NC Squeeze Baccarat 1',
                    'description'   => 'Virtual NC Squeeze Baccarat 1',
                    'content'       => 'Virtual NC Squeeze Baccarat 1'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '1081',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Virtual Lucky Baccarat',
                    'description'   => 'Virtual Lucky Baccarat',
                    'content'       => 'Virtual Lucky Baccarat'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Virtual Lucky Baccarat',
                    'description'   => 'Virtual Lucky Baccarat',
                    'content'       => 'Virtual Lucky Baccarat'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Virtual Lucky Baccarat',
                    'description'   => 'Virtual Lucky Baccarat',
                    'content'       => 'Virtual Lucky Baccarat'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # lottery
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_LOTTERY);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'keno',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Keno',
                    'description'   => 'Keno',
                    'content'       => 'Keno'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Keno',
                    'description'   => 'Keno',
                    'content'       => 'Keno'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Keno',
                    'description'   => 'Keno',
                    'content'       => 'Keno'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'pk10',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'PK10',
                    'description'   => 'PK10',
                    'content'       => 'PK10'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'PK10',
                    'description'   => 'PK10',
                    'content'       => 'PK10'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'PK10',
                    'description'   => 'PK10',
                    'content'       => 'PK10'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'thailottery',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Thai Lottery',
                    'description'   => 'Thai Lottery',
                    'content'       => 'Thai Lottery'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Thai Lottery',
                    'description'   => 'Thai Lottery',
                    'content'       => 'Thai Lottery'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Thai Lottery',
                    'description'   => 'Thai Lottery',
                    'content'       => 'Thai Lottery'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'rockpaperscissors',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rock Paper Scissors',
                    'description'   => 'Rock Paper Scissors',
                    'content'       => 'Rock Paper Scissors'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rock Paper Scissors',
                    'description'   => 'Rock Paper Scissors',
                    'content'       => 'Rock Paper Scissors'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rock Paper Scissors',
                    'description'   => 'Rock Paper Scissors',
                    'content'       => 'Rock Paper Scissors'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'fast3',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fast3',
                    'description'   => 'Fast3',
                    'content'       => 'Fast3'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fast3',
                    'description'   => 'Fast3',
                    'content'       => 'Fast3'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fast3',
                    'description'   => 'Fast3',
                    'content'       => 'Fast3'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'sode',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'So De',
                    'description'   => 'So De',
                    'content'       => 'So De'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'So De',
                    'description'   => 'So De',
                    'content'       => 'So De'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'So De',
                    'description'   => 'So De',
                    'content'       => 'So De'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'taixiu',
            'type'          => GamePlatformProduct::TYPE_LOTTERY,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tai Xiu',
                    'description'   => 'Tai Xiu',
                    'content'       => 'Tai Xiu'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tai Xiu',
                    'description'   => 'Tai Xiu',
                    'content'       => 'Tai Xiu'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tai Xiu',
                    'description'   => 'Tai Xiu',
                    'content'       => 'Tai Xiu'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # games
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_FISH);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'ladder',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Ladder',
                    'description'   => 'The Ladder',
                    'content'       => 'The Ladder'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Ladder',
                    'description'   => 'The Ladder',
                    'content'       => 'The Ladder'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Ladder',
                    'description'   => 'The Ladder',
                    'content'       => 'The Ladder'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'rockpaperscissors',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Rock Paper Scissors',
                    'description'   => 'Rock Paper Scissors',
                    'content'       => 'Rock Paper Scissors'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Rock Paper Scissors',
                    'description'   => 'Rock Paper Scissors',
                    'content'       => 'Rock Paper Scissors'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Rock Paper Scissors',
                    'description'   => 'Rock Paper Scissors',
                    'content'       => 'Rock Paper Scissors'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'thor',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Thor',
                    'description'   => 'Thor',
                    'content'       => 'Thor'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Thor',
                    'description'   => 'Thor',
                    'content'       => 'Thor'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Thor',
                    'description'   => 'Thor',
                    'content'       => 'Thor'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # p2p
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_P2P);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'html5niuniu',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Superbull',
                    'description'   => 'Superbull',
                    'content'       => 'Superbull'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Superbull',
                    'description'   => 'Superbull',
                    'content'       => 'Superbull'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Superbull',
                    'description'   => 'Superbull',
                    'content'       => 'Superbull'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'html5dominoqq',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Domino QQ',
                    'description'   => 'Domino QQ',
                    'content'       => 'Domino QQ'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Domino QQ',
                    'description'   => 'Domino QQ',
                    'content'       => 'Domino QQ'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Domino QQ',
                    'description'   => 'Domino QQ',
                    'content'       => 'Domino QQ'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'html5holdempoker',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Texas Hold\'em Poker',
                    'description'   => 'Texas Hold\'em Poker',
                    'content'       => 'Texas Hold\'em Poker'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Texas Hold\'em Poker',
                    'description'   => 'Texas Hold\'em Poker',
                    'content'       => 'Texas Hold\'em Poker'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Texas Hold\'em Poker',
                    'description'   => 'Texas Hold\'em Poker',
                    'content'       => 'Texas Hold\'em Poker'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'html5thienlen',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tien Len',
                    'description'   => 'Tien Len',
                    'content'       => 'Tien Len'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tien Len',
                    'description'   => 'Tien Len',
                    'content'       => 'Tien Len',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tien Len',
                    'description'   => 'Tien Len',
                    'content'       => 'Tien Len'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'html5pokdeng',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Pok Deng',
                    'description'   => 'Pok Deng',
                    'content'       => 'Pok Deng'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Pok Deng',
                    'description'   => 'Pok Deng',
                    'content'       => 'Pok Deng',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Pok Deng',
                    'description'   => 'Pok Deng',
                    'content'       => 'Pok Deng'
                ],
            ],
            'devices'       => [1, 2],
        ];

        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => 'html5baicao',
            'type'          => GamePlatformProduct::TYPE_P2P,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Bai Cao',
                    'description'   => 'Bai Cao',
                    'content'       => 'Bai Cao'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Bai Cao',
                    'description'   => 'Bai Cao',
                    'content'       => 'Bai Cao',
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Bai Cao',
                    'description'   => 'Bai Cao',
                    'content'       => 'Bai Cao'
                ],
            ],
            'devices'       => [1, 2],
        ];
        # GPI end

        # GG start
        $platform = GamePlatform::findByCode('GG');

        // 0 Game Lobby
        // 110 Fishing 2
        // 102 Fruit
        // 103 Solo king
        // 104 Gold Shark
        // 105 Lucky five
        // 106 Big fish eat small fish
        // 107 PokerGo
        // 108 DiamondDeal
        // 109 The Deep Forest Dance
        // 401 World cup
        // 402 Mercedes-Benz BMW
        # 捕鱼
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_FISH);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '110',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fishing 2',
                    'description'   => 'Fishing 2',
                    'content'       => 'Fishing 2'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fishing 2',
                    'description'   => 'Fishing 2',
                    'content'       => 'Fishing 2'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fishing 2',
                    'description'   => 'Fishing 2',
                    'content'       => 'Fishing 2'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # 老虎机
        $product  = GamePlatformProduct::findProductByType($platform->code, GamePlatformProduct::TYPE_SLOT);
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '102',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fruit',
                    'description'   => 'Fruit',
                    'content'       => 'Fruit'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fruit',
                    'description'   => 'Fruit',
                    'content'       => 'Fruit'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fruit',
                    'description'   => 'Fruit',
                    'content'       => 'Fruit'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '103',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Solo king',
                    'description'   => 'Solo king',
                    'content'       => 'Solo king'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Solo king',
                    'description'   => 'Solo king',
                    'content'       => 'Solo king'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Solo king',
                    'description'   => 'Solo king',
                    'content'       => 'Solo king'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '104',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Gold Shark',
                    'description'   => 'Gold Shark',
                    'content'       => 'Gold Shark'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Gold Shark',
                    'description'   => 'Gold Shark',
                    'content'       => 'Gold Shark'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Gold Shark',
                    'description'   => 'Gold Shark',
                    'content'       => 'Gold Shark'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '105',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Lucky five',
                    'description'   => 'Lucky five',
                    'content'       => 'Lucky five'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Lucky five',
                    'description'   => 'Lucky five',
                    'content'       => 'Lucky five'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Lucky five',
                    'description'   => 'Lucky five',
                    'content'       => 'Lucky five'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '106',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Big fish eat small fish',
                    'description'   => 'Big fish eat small fish',
                    'content'       => 'Big fish eat small fish'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Big fish eat small fish',
                    'description'   => 'Big fish eat small fish',
                    'content'       => 'Big fish eat small fish'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Big fish eat small fish',
                    'description'   => 'Big fish eat small fish',
                    'content'       => 'Big fish eat small fish'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '107',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'PokerGo',
                    'description'   => 'PokerGo',
                    'content'       => 'PokerGo'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'PokerGo',
                    'description'   => 'PokerGo',
                    'content'       => 'PokerGo'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'PokerGo',
                    'description'   => 'PokerGo',
                    'content'       => 'PokerGo'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '108',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'DiamondDeal',
                    'description'   => 'DiamondDeal',
                    'content'       => 'DiamondDeal'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'DiamondDeal',
                    'description'   => 'DiamondDeal',
                    'content'       => 'DiamondDeal'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'DiamondDeal',
                    'description'   => 'DiamondDeal',
                    'content'       => 'DiamondDeal'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '109',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'The Deep Forest Dance',
                    'description'   => 'The Deep Forest Dance',
                    'content'       => 'The Deep Forest Dance'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'The Deep Forest Dance',
                    'description'   => 'The Deep Forest Dance',
                    'content'       => 'The Deep Forest Dance'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'The Deep Forest Dance',
                    'description'   => 'The Deep Forest Dance',
                    'content'       => 'The Deep Forest Dance'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '401',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'World cup',
                    'description'   => 'World cup',
                    'content'       => 'World cup'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'World cup',
                    'description'   => 'World cup',
                    'content'       => 'World cup'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'World cup',
                    'description'   => 'World cup',
                    'content'       => 'World cup'
                ],
            ],
            'devices'       => [1, 2],
        ];
        $data[] = [
            'platform_code' => $platform->code,
            'product_code'  => $product->code,
            'code'          => '402',
            'type'          => GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Mercedes-Benz BMW',
                    'description'   => 'Mercedes-Benz BMW',
                    'content'       => 'Mercedes-Benz BMW'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Mercedes-Benz BMW',
                    'description'   => 'Mercedes-Benz BMW',
                    'content'       => 'Mercedes-Benz BMW'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Mercedes-Benz BMW',
                    'description'   => 'Mercedes-Benz BMW',
                    'content'       => 'Mercedes-Benz BMW'
                ],
            ],
            'devices'       => [1, 2],
        ];

        # GG end

        foreach ($data as $item) {
            if ($product = Game::findByPlatformAndCode($item['platform_code'], $item['code'])) {
                $product->update($item);
            } else {
                Game::query()->create($item);
            }
        }
    }
}
