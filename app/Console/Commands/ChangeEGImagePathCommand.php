<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class ChangeEGImagePathCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:change-eg-games-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '这个指令只能在eg平台上执行';

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
        echo "\n";
        $this->info('该指令可以反复执行，不会造成数据重复修改，已经修改的不会再次改变！');
        $answer = $this->confirm('这个是不是EG平台？', false);
        if ($answer){
            Game::chunk(10, function ($games) {
                foreach ($games as $game) {
                    $game->small_img_path  = str_replace('uploads/games', 'uploads/eg_games', $game->small_img_path);
                    $game->web_img_path    = str_replace('uploads/games', 'uploads/eg_games', $game->web_img_path);
                    $game->mobile_img_path = str_replace('uploads/games', 'uploads/eg_games', $game->mobile_img_path);
                    $result = $game->save();
                }
            });
            $this->info('数据更新成功，请快速检查是否已经生效，如果没有，请触发缓存更新！');
        }else{
            $this->info('更新终止！数据未发生改变！');
        }
    }
}
