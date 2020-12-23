<?php

namespace App\Console\Commands;

use App\Models\GamePlatform;
use App\Models\GamePlatformPullReportSchedule;
use App\Services\GamePlatformService;
use Illuminate\Console\Command;

class PullGamePlatformReportsCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:pull-game-platform-reports';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Pull game platform reports';

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
        foreach (GamePlatform::getAll()->where('status', true) as $platform) {
            # 获取时刻表
            foreach ($platform->getNoProcessSchedules($platform->limit) as $schedule) {
                $this->pull($platform, $schedule);

            }
        }
    }

    public function pull(GamePlatform $platform, GamePlatformPullReportSchedule $schedule)
    {
        try {
            $schedule->start();
            $data =  app(GamePlatformService::class)->pull(null, $platform, ['schedule' => $schedule]);
            $schedule->success($data['origin_total'], $data['transfer_total']);
        } catch (\Exception $e) {
            $schedule->fail(
                str_limit($e->getMessage(), '1024', '...')
            );
        }
    }
}
