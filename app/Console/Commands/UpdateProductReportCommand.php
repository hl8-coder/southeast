<?php

namespace App\Console\Commands;

use App\Models\GameBetDetail;
use App\Models\GamePlatformProduct;
use App\Models\User;
use App\Models\UserProductDailyReport;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class UpdateProductReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:update-product-daily-report {--date=}';

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
            $this->info("请输入date！");
            return;
        }

        $startDateString = Carbon::parse($date)->startOfDay()->toDateTimeString();
        $endDateString   = Carbon::parse($date)->endOfDay()->toDateTimeString();

        $productReports = UserProductDailyReport::query()
            ->where('date', $date)
            ->get();

        $sums = GameBetDetail::query()->where('payout_at', '>=', $startDateString)
            ->where('payout_at', '<=', $endDateString)
            ->where('platform_status', GameBetDetail::PLATFORM_STATUS_BET_SUCCESS)
            ->groupBy(['user_id', 'product_code', DB::raw('DATE_FORMAT(payout_at, "%Y-%m-%d")')])
            ->get([
                'user_id',
                'product_code',
                DB::raw('DATE_FORMAT(payout_at, "%Y-%m-%d") as date'),
                DB::raw('SUM(user_stake) as stake'),
                DB::raw('SUM(user_profit) as profit'),
                DB::raw('SUM(available_rebate_bet) as calculate_rebate_bet'),
            ]);

        $this->info('总共 ' . count($sums) . '条');

        foreach ($sums as $k => $sum) {
            $report = $productReports->where('user_id', $sum->user_id)
                        ->where('product_code', $sum->product_code)
                        ->where('date', $sum->date)
                        ->first();

            if (!$report) {
                $user = User::find($sum->user_id);
                $product = GamePlatformProduct::query()->where('code', $sum->product_code)->first();
                $report = UserProductDailyReport::query()->create([
                    'user_id'       => $sum->user_id,
                    'user_name'     => $user->name,
                    'platform_code' => $product->platform_code,
                    'product_code'  => $sum->product_code,
                    'date'          => $sum->date,
                ]);
            }

            $stake = !empty($sum->stake) ? $sum->stake : 0;
            $profit = !empty($sum->profit) ? $sum->profit : 0;
            $rebet = !empty($sum->calculate_rebate_bet) ? $sum->calculate_rebate_bet : 0;

            $report->update([
                'stake'                 => $stake,
                'profit'                => $profit,
                'effective_bet'         => $stake,
                'effective_profit'      => $profit,
                'calculate_rebate_bet'  => $rebet,
            ]);

            $this->info('已跑完 ' . ($k + 1) . ' 条');
        }
    }
}
