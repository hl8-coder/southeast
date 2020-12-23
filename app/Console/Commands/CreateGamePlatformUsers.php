<?php

namespace App\Console\Commands;

use App\Models\GamePlatform;
use App\Repositories\GamePlatformUserRepository;
use Illuminate\Console\Command;

class CreateGamePlatformUsers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:create-game-platform-users {platform_code}';

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
        $platformCode = $this->argument('platform_code');

        if ($platform = GamePlatform::findByCodeFromCache($platformCode)) {
            GamePlatformUserRepository::allUserRegisterPlatform($platform);
        }
    }
}
