<?php

namespace App\Console\Commands;

use App\GamePlatforms\GPIPlatform;
use App\Models\GamePlatform;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PullGPILotteryReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:pull-gpi-lottery-report {--date=}';

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
        $date = $this->option('date');

        if (empty($date)) {
            $date = now()->toDateString();
        }

        $endAt   = $date . ' 23:00:00';
        $startAt = Carbon::parse($endAt)->copy()->subDays(2)->subHours(2)->toDateTimeString();
        $platform = GamePlatform::getAll()->where('code', 'GPI')->first();

        $pullCount = [
            'origin_total'    => 0,
            'transfer_total'  => 0,
        ];

        $gpiPlatform = new GPIPlatform([null, $platform]);

        $result = $gpiPlatform->singleProductPull($startAt, $endAt, 'lottery');
        $totalPage = $result['@attributes']['total_page'];
        $pageNum = $result['@attributes']['page_num'];
        if (1 == $totalPage) {
            $pullCount = $gpiPlatform->insertBetDetails($result, $pullCount);
        } else {

            if (1 == $pageNum) {
                $pullCount = $gpiPlatform->insertBetDetails($result, $pullCount);
            }

            for ($i=2; $i <= $totalPage; $i++) {
                try {
                    $result = $gpiPlatform->singleProductPull($startAt, $endAt, 'lottery', $i);
                    $pullCount = $gpiPlatform->insertBetDetails($result, $pullCount);
                } catch (\Exception $e) {
                    Log::stack(['gpi'])->info('pull lottery error:' . $e->getMessage());
                    continue;
                }
            }
        }
        Log::stack(['gpi'])->info('pull lottery success:' . json_encode($pullCount));
    }
}
