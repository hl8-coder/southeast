<?php

namespace App\Console\Commands;

use App\Models\GamePlatformPullReportSchedule;
use Illuminate\Console\Command;

class RePullGPIFailScheduleCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:re-pull-gpi-fail-schedule';

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
        GamePlatformPullReportSchedule::query()->where('platform_code', 'GPI')
            ->where('status', GamePlatformPullReportSchedule::STATUS_FAIL)
            ->where('times', '<', 3)
            ->update([
                'status' => GamePlatformPullReportSchedule::STATUS_CREATED,
            ]);
    }
}
