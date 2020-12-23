<?php

namespace App\Console\Commands;

use App\Models\GamePlatform;
use App\Services\GamePlatformService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class UpdateGameListCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:update-game-list';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update Game List';

    protected $service;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct(GamePlatformService $service)
    {
        parent::__construct();

        $this->service = $service;
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $platforms = GamePlatform::getAll()->where('is_update_list', true);

        $now = now();
        foreach ($platforms as $platform) {
            if (!$platform->last_updated_at || $now->diffInDays($platform->last_updated_at) >= $platform->update_interval) {
                try {
                    $this->service->gameList(null, $platform);
                    $platform->updateLastUpdateAt();
                } catch (\Exception $e) {
                    Log::stack(['update_game_list'])->info($e->getMessage());
                }
            }
        }
    }
}
