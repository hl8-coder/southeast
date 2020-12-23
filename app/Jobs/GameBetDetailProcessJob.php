<?php

namespace App\Jobs;

use App\Models\GameBetDetail;
use App\Models\UserBetCountLog;
use App\Services\GameBetDetailService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GameBetDetailProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $detail;
    protected $data = [];

    /**
     * Create a new job instance.
     */
    public function __construct(GameBetDetail $detail)
    {
        $this->detail = $detail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->detail->isProcess()) {

            if ($this->detail->isClose()) {
                $this->detail->success();
                return;
            }

            try {
                DB::transaction(function () {
                    (new GameBetDetailService())->process($this->detail, $this->data);
                    // 统计
                    $this->report();
                    $this->detail->success();
                });
            } catch (\Exception $e) {
                $this->detail->fail(
                    str_limit($e->getMessage(), 1024, '...')
                );
                return;
            }
        }
    }

    public function report()
    {
        if ($this->detail->game->isCalculateCashBack()) {
            $this->data['calculate_cash_back_profit'] = $this->detail->available_profit;
        }
        $date = $this->detail->payout_at;
        if (!is_object($date) || '-0001-11-30 00:00:00' == $date->toDateTimeString()) {
            $date = $this->detail->bet_at;
        }
        UserBetCountLog::report(
            UserBetCountLog::PREFIX_BET . $this->detail->id,
            $this->detail->user_id,
            $this->detail->product_code,
            $date,
            $this->data
        );
    }
}
