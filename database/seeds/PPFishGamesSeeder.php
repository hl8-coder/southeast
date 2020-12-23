<?php

use Illuminate\Database\Seeder;
use App\Models\GamePlatformProduct;

class PPFishGamesSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $product = [
            'platform_code'  => 'PP',
            'code'           => 'PP_Fish',
            'type'           => GamePlatformProduct::TYPE_FISH,
            'currencies'     => ['VND', 'THB', 'USD'],
            'languages'     => [
                [
                    'language'    => 'en-US',
                    'name'        => 'PP_Fish',
                    'description' => 'PP_Fish',
                    'content'     => 'PP_Fish',
                ],
                [
                    'language'    => 'vi-VN',
                    'name'        => 'PP_Fish',
                    'description' => 'PP_Fish',
                    'content'     => 'PP_Fish',
                ],
                [
                    'language'    => 'th',
                    'name'        => 'PP_Fish',
                    'description' => 'PP_Fish',
                    'content'     => 'PP_Fish',
                ],
            ],
            'is_can_try'    => 0,
            'devices'       => [1, 2],
        ];

        GamePlatformProduct::query()->create($product);

        $game = [
            'platform_code' => 'PP',
            'product_code'  => 'PP_Fish',
            'type'          => GamePlatformProduct::TYPE_FISH,
            'currencies'    => ['VND', 'THB', 'USD'],
            'code'          => 'pp3fish',
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Fishing Game',
                    'description'   => 'Fishing Game',
                    'content'       => 'Fishing Game'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Fishing Game',
                    'description'   => 'Fishing Game',
                    'content'       => 'Fishing Game'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Fishing Game',
                    'description'   => 'Fishing Game',
                    'content'       => 'Fishing Game'
                ],
            ],
            'devices'       => [1, 2],
        ];
        \App\Models\Game::query()->create($game);

    }
}
