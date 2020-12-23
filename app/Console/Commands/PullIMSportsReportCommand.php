<?php

namespace App\Console\Commands;

use App\GamePlatforms\IMSPORTSPlatform;
use App\Models\GamePlatform;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PullIMSportsReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:pull-imsports-settled-status-report';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'southeast:pull-imsports-settled-status-report';

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
        $platform = GamePlatform::getAll()->where('code', 'IMSports')->where('status', true)->first();

        if ($platform) {

            $imsportsPlatform = new IMSPORTSPlatform([null, $platform]);

            $imsportsPlatform->pullOldSportsData();

            Log::stack(['imsports'])->info('pull  lottery success');
        }
    }
}
