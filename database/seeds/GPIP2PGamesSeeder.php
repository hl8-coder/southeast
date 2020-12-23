<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatformProduct;
use App\Models\Game;

class GPIP2PGamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * Superbull = html5Niuniu
     * Domino QQ = html5DominoQQ
     * Texas Hold'em Poker = html5HoldemPoker
     * Tien Len = html5TienLen
     * Pok Deng = html5PokDeng
     * Bai Cao = html5BaiCao
     * Gao Gae = html5GaoGae
     * Indian Rummy = html5IndianRummy
     *
     * @return void
     */
    public function run()
    {
        $games=  [
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_P2P',
                'code'          => 'html5HoldemPoker',
                'type'          => GamePlatformProduct::TYPE_P2P,
                'currencies'    => ['USD', 'VND', 'THB'],
                'languages'     => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => "Texas Hold'em Poker",
                        'description'   => "Texas Hold'em Poker",
                        'content'       => "Texas Hold'em Poker"
                    ],
                    [
                        'language'      => 'th',
                        'name'          => "Texas Hold'em Poker",
                        'description'   => "Texas Hold'em Poker",
                        'content'       => "Texas Hold'em Poker"
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => "Texas Hold'em Poker",
                        'description'   => "Texas Hold'em Poker",
                        'content'       => "Texas Hold'em Poker"
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_P2P',
                'code'          => 'html5TienLen',
                'type'          => GamePlatformProduct::TYPE_P2P,
                'currencies'    => ['USD', 'VND'],
                'languages'     => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => "Tien Len",
                        'description'   => "Tien Len",
                        'content'       => "Tien Len"
                    ],
                    [
                        'language'      => 'th',
                        'name'          => "Tien Len",
                        'description'   => "Tien Len",
                        'content'       => "Tien Len"
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => "Tien Len",
                        'description'   => "Tien Len",
                        'content'       => "Tien Len"
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_P2P',
                'code'          => 'html5BaiCao',
                'type'          => GamePlatformProduct::TYPE_P2P,
                'currencies'    => ['USD', 'VND'],
                'languages'     => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => "Bai Cao",
                        'description'   => "Bai Cao",
                        'content'       => "Bai Cao"
                    ],
                    [
                        'language'      => 'th',
                        'name'          => "Bai Cao",
                        'description'   => "Bai Cao",
                        'content'       => "Bai Cao"
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => "Bai Cao",
                        'description'   => "Bai Cao",
                        'content'       => "Bai Cao"
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_P2P',
                'code'          => 'html5PokDeng',
                'type'          => GamePlatformProduct::TYPE_P2P,
                'currencies'    => ['USD','THB'],
                'languages'     => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => "Pok Deng",
                        'description'   => "Pok Deng",
                        'content'       => "Pok Deng"
                    ],
                    [
                        'language'      => 'th',
                        'name'          => "Pok Deng",
                        'description'   => "Pok Deng",
                        'content'       => "Pok Deng"
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => "Pok Deng",
                        'description'   => "Pok Deng",
                        'content'       => "Pok Deng"
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_P2P',
                'code'          => 'html5GaoGae',
                'type'          => GamePlatformProduct::TYPE_P2P,
                'currencies'    => ['USD','THB'],
                'languages'     => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => "Gao Gae",
                        'description'   => "Gao Gae",
                        'content'       => "Gao Gae"
                    ],
                    [
                        'language'      => 'th',
                        'name'          => "Gao Gae",
                        'description'   => "Gao Gae",
                        'content'       => "Gao Gae"
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => "Gao Gae",
                        'description'   => "Gao Gae",
                        'content'       => "Gao Gae"
                    ],
                ],
                'devices'       => [1, 2],
            ],
        ];


        foreach ($games as $game) {
            Game::query()->create($game);
        }
    }
}
