<?php

use Illuminate\Database\Seeder;
use App\Models\Game;

class MGITournamentGameSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $game = [
            'platform_code' => 'MGS',
            'product_code'  => 'MGS_Slot',
            'type'          => \App\Models\GamePlatformProduct::TYPE_SLOT,
            'currencies'    => ['VND', 'THB', 'USD'],
            'code'          => 'tournament',
            'languages'    => [
                [
                    'language'      => 'vi-VN',
                    'name'          => 'Tournament',
                    'description'   => 'Tournament',
                    'content'       => 'Tournament'
                ],
                [
                    'language'      => 'th',
                    'name'          => 'Tournament',
                    'description'   => 'Tournament',
                    'content'       => 'Tournament'
                ],
                [
                    'language'      => 'en-US',
                    'name'          => 'Tournament',
                    'description'   => 'Tournament',
                    'content'       => 'Tournament'
                ],
            ],
            'devices'       => [1, 2],
            'status'        => 0,
        ];

        Game::query()->create($game);

        $this->info('Tournament游戏id:' . Game::query()->where('code', 'tournament')->first()->id);
    }
}
