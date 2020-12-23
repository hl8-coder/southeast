<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;

class UpdateGameImgsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:update-game-imgs {--product_code=} {--suffix=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {

        $productCode    = $this->option('product_code');
        $suffix         = $this->option('suffix');

        $games = Game::query()->where('product_code', $productCode)->get();


        foreach ($games as $game) {

            $languages = $game->languages;
            foreach ($languages as $k => &$language) {
                foreach (Game::$imgFields as $field) {

                    if (strpos($field, 'mobile') !== false) {
                        $language[$field] = 'uploads/games/mobile/' . strtolower($game->platform_code) . '/' . $game->code . '.' . $suffix;
                    } else {
                        $language[$field] = 'uploads/games/web/' . strtolower($game->platform_code) . '/' . $game->code . '.' . $suffix;
                    }
                }
            }
            $game->languages = $languages;
            $game->save();

            $this->info('游戏id：' . $game->id . '更新成功');
        }
    }
}
