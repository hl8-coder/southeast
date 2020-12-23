<?php

namespace App\Console\Commands;

use App\Models\Game;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Log;

class ChangeISBGamePlatformImageFormatCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:change-isb-game-platform-image-format';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '这里涉及修改 ISB 平台游戏图片格式，由于比较多，使用命令来批量修改';

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
        $isbGames = Game::query()->where('platform_code', 'ISB')->get();
        foreach ($isbGames as $game){
            // mobile
            if (strstr($game->mobile_img_path, 'uploads/games/mobile/isb') != false){
                $newPath = str_replace('.png', '.jpg', $game->mobile_img_path);
                if (File::exists(public_path($newPath))){
                    // id, code, old_path, new_path, result
                    $info = 'id:' . $game->id . ', code:' . $game->code . ', old_mobile_path:' . $game->mobile_img_path . ', new_mobile_path:' . $newPath;
                    Log::channel('command_mark_log')->info($info);
                    $game->mobile_img_path = $newPath;
                }
            }
            // web
            // if (strstr($game->web_img_path, 'uploads/games/web/isb') != false){
            //     $newWebPath = str_replace('.png', '.jpg', $game->web_img_path);
            //     if (File::exists(public_path($newWebPath))){
            //         // id, code, old_path, new_path, result
            //         $info = 'id:' . $game->id . ', code:' . $game->code . ', old_web_path:' . $game->web_img_path . ', new_web_path:' . $newWebPath;
            //         Log::channel('command_mark_log')->info($info);
            //         // $game->mobile_img_path = $newWebPath;
            //     }
            // }
            $result = $game->save();
            Log::channel('command_mark_log')->info('result:' . $result);
        }
    }
}
