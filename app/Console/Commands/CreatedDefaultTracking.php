<?php

namespace App\Console\Commands;

use App\Models\TrackingStatistic;
use App\Models\User;
use Illuminate\Console\Command;

class CreatedDefaultTracking extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'created-default-tracking-name';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '创建默认的追踪Tracking';

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
        $affiliates = User::query()->where('is_agent', true)->get();

        foreach ($affiliates as $affiliate) {
            $code = $affiliate->affiliate_code;
            if (!$code) {
                $code = $affiliate->affiliate->code;
            }
            $tracking = TrackingStatistic::query()->where('tracking_name', $code)->first();
            if (!is_object($tracking)) {
                $track                = new TrackingStatistic();
                $track->tracking_name = $code;
                $track->user_id       = $affiliate->id;
                $track->user_name     = $affiliate->name;
                $track->save();
            }
        }
    }
}
