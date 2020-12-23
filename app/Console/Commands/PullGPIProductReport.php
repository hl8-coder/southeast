<?php

namespace App\Console\Commands;

use App\GamePlatforms\GPIPlatform;
use App\Models\GamePlatform;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class PullGPIProductReport extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:pull-gpi-product-report {--product=} {--start_at=} {--end_at=} {--page_size=}';

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
        $product = $this->option('product');
        $startAt = $this->option('start_at');
        $endAt = $this->option('end_at');
        $pageSize = $this->option('page_size');
        if (empty($product) || empty($startAt) || empty($endAt) || empty($pageSize)) {
            $this->error('请输入正确参数');
            return;
        }

        $platform = GamePlatform::getAll()->where('code', 'GPI')->first();

        $startCarbonAt = Carbon::parse($startAt);
        $endCarbonAt = Carbon::parse($endAt)->toDateTimeString();
        $tmpAt = Carbon::parse($startAt)->addHour();
        $gpiPlatform = new GPIPlatform([null, $platform]);

        while ($tmpAt <= $endCarbonAt) {
            $this->single($gpiPlatform, $product, $pageSize, $startCarbonAt, $tmpAt);
            $startCarbonAt->addHour();
            $tmpAt->addHour();
        }

    }

    public function single(GPIPlatform $gpiPlatform, $product, $pageSize, $startCarbonAt, $tmpAt) {
        $pullCount = [
            'origin_total'    => 0,
            'transfer_total'  => 0,
        ];

        $startAt = $startCarbonAt->toDateTimeString();
        $endAt = $tmpAt->toDateTimeString();

        try {
            $result = $gpiPlatform->singleProductPull($startAt, $endAt, $product, 1, $pageSize);
        } catch (\Exception $e) {
            $this->error('pull ' . $product . ' 时间：' . $startAt . '~' . $endAt . ' error:' . $e->getMessage());
            Log::stack(['gpi'])->error('pull ' . $product . ' 时间：' . $startAt . '~' . $endAt . ' error:' . $e->getMessage());
            throw $e;
        }

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
                    $result = $gpiPlatform->singleProductPull($startAt, $endAt, $product, $i, $pageSize);
                    $pullCount = $gpiPlatform->insertBetDetails($result, $pullCount);
                } catch (\Exception $e) {
                    $this->error('pull ' . $product . ' 时间：' . $startAt . '~' . $endAt . ' error:' . $e->getMessage());
                    Log::stack(['gpi'])->error('pull ' . $product . ' 时间：' . $startAt . '~' . $endAt . ' error:' . $e->getMessage());
                    throw $e;
                }
            }
        }
        $this->info('pull ' . $product  . ' 时间：' . $startAt . '~' . $endAt . ' success:' . json_encode($pullCount));
        Log::stack(['gpi'])->info('pull ' . $product  . ' 时间：' . $startAt . '~' . $endAt . ' success:' . json_encode($pullCount));
    }
}
