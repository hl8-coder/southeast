<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatformProduct;

class GPIFishGamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $games = [
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_Fish',
                'type'          => GamePlatformProduct::TYPE_FISH,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'thaihilo',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Thai Hi Lo',
                        'description'   => 'Thai Hi Lo',
                        'content'       => 'Thai Hi Lo'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Thai Hi Lo',
                        'description'   => 'Thai Hi Lo',
                        'content'       => 'Thai Hi Lo'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Thai Hi Lo',
                        'description'   => 'Thai Hi Lo',
                        'content'       => 'Thai Hi Lo'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_Fish',
                'type'          => GamePlatformProduct::TYPE_FISH,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'fishprawncrab',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fish Prawn Crab',
                        'description'   => 'Fish Prawn Crab',
                        'content'       => 'Fish Prawn Crab'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fish Prawn Crab',
                        'description'   => 'Fish Prawn Crab',
                        'content'       => 'Fish Prawn Crab'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fish Prawn Crab',
                        'description'   => 'Fish Prawn Crab',
                        'content'       => 'Fish Prawn Crab'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_Fish',
                'type'          => GamePlatformProduct::TYPE_FISH,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'fishprawncrabgame',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Fish Prawn Crab - Scratch',
                        'description'   => 'Fish Prawn Crab - Scratch',
                        'content'       => 'Fish Prawn Crab - Scratch'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Fish Prawn Crab - Scratch',
                        'description'   => 'Fish Prawn Crab - Scratch',
                        'content'       => 'Fish Prawn Crab - Scratch'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Fish Prawn Crab - Scratch',
                        'description'   => 'Fish Prawn Crab - Scratch',
                        'content'       => 'Fish Prawn Crab - Scratch'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_Fish',
                'type'          => GamePlatformProduct::TYPE_FISH,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'moneyblast',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Money Blast',
                        'description'   => 'Money Blast',
                        'content'       => 'Money Blast'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Money Blast',
                        'description'   => 'Money Blast',
                        'content'       => 'Money Blast'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Money Blast',
                        'description'   => 'Money Blast',
                        'content'       => 'Money Blast'
                    ],
                ],
                'devices'       => [1, 2],
            ],
            [
                'platform_code' => 'GPI',
                'product_code'  => 'GPI_Fish',
                'type'          => GamePlatformProduct::TYPE_FISH,
                'currencies'    => ['VND', 'THB', 'USD'],
                'code'          => 'super98baccarat',
                'languages'    => [
                    [
                        'language'      => 'vi-VN',
                        'name'          => 'Super98 Baccarat',
                        'description'   => 'Super98 Baccarat',
                        'content'       => 'Super98 Baccarat'
                    ],
                    [
                        'language'      => 'th',
                        'name'          => 'Super98 Baccarat',
                        'description'   => 'Super98 Baccarat',
                        'content'       => 'Super98 Baccarat'
                    ],
                    [
                        'language'      => 'en-US',
                        'name'          => 'Super98 Baccarat',
                        'description'   => 'Super98 Baccarat',
                        'content'       => 'Super98 Baccarat'
                    ],
                ],
                'devices'       => [1, 2],
            ],
        ];


        foreach ($games as $game) {
            \App\Models\Game::query()->create($game);
        }

    }
}
