<?php

namespace App\Console\Commands;

use App\Events\KpiReportUpdateEvent;
use App\Models\Currency;
use App\Models\KpiReport;
use App\Services\KpiReportService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateKpiReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:calculate-kpi-report {--type=all} {--date=today}';
    # demo:
    # php artisan southeast:calculate-kpi-report
    # php artisan southeast:calculate-kpi-report --type=hello
    # php artisan southeast:calculate-kpi-report --type=hello --date=2019-12-25

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '计算 kpi 数据报表，type 为计算数据类型（默认为全部，具体类型请查看 kpi report model 常量），date 为需要计算的日期（默认为今天，需要传入日期）';

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
        try {
            $type = strtolower($this->option('type'));
            if ($type != 'all') {
                $reportType = KpiReport::$typeList[$type];
            } elseif ($type == 'all') {
                $reportType = 'all';
            }

            $date       = $this->option('date') == 'today' ? now()->toDateString() : $this->option('date');
            $dateString = Carbon::parse($date)->toDateString();
        } catch (\Exception $exception) {
            $message = 'command : date=>' . $this->option('date') .
                '; type=>' . $this->option('type') . '; errors:' . $exception->getMessage();
            Log::channel('kpi')->error($message);
            $this->error($message);
            return;
        }


        Currency::query()->where('status', true)->chunk(1, function($currencies) use($reportType, $dateString){
            $kpiService = new KpiReportService();
            $currencyObj= $currencies->first();
            if (empty($currencyObj)){
                return false;
            }
            $currency = $currencyObj->code;
            if ($reportType != 'all') {
                $kpiService->updateColumn($reportType, $currency, $dateString);
            } else {
                foreach (KpiReport::$typeList as $type) {
                    $kpiService->updateColumn($type, $currency, $dateString);
                }
            }
        });

    }
}
