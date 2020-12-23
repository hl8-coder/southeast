<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;

class RollbackChangeEGImagePathCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:rollback-change-eg-games-image';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '回滚 southeast:change-eg-games-image 指令造成的数据变更，恢复到原来的样子!';

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
        $this->info('该指令可以可以混滚图片修改命令 southeast:change-eg-games-image 造成的数据变更，在数据异常的情况下执行该指令可以回滚数据！');
        $answer = $this->confirm('这个是不是EG平台？', false);
        if ($answer){
            Game::chunk(10, function ($games) {
                foreach ($games as $game) {
                    $game->small_img_path  = str_replace('uploads/eg_games', 'uploads/games', $game->small_img_path);
                    $game->web_img_path    = str_replace('uploads/eg_games', 'uploads/games', $game->web_img_path);
                    $game->mobile_img_path = str_replace('uploads/eg_games', 'uploads/games', $game->mobile_img_path);
                    $result = $game->save();
                }
            });
            $this->info('数据回滚成功，请快速检查是否已经生效，如果没有，请触发缓存更新！');
        }else{
            $this->info('回滚终止！数据保持当前状态！');
        }
    }
}
