<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Models\UserBetCountLog;
use App\Services\ReportService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class SumUserBetCountLogCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:sum-user-bet-count-log';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'southeast:sum-user-bet-count-log';

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
        $logs = UserBetCountLog::query()->where('status', UserBetCountLog::STATUS_CREATED)
                ->orderBy('created_at')
                ->limit(10000)
                ->get();

        $data = [];
        $userIds = [];
        $logIds = [];
        foreach ($logs as $log) {
            $userIds[] = $log->user_id;
            $logIds[]  = $log->id;
            $date = $log->date->toDateString();
            foreach ($log->toArray() as $k => $v) {
                if (in_array($k, ['id', 'unique_id', 'user_id', 'product_code', 'date', 'status', 'created_at', 'updated_at'])) {
                    continue;
                }
                if (!isset($data[$log->user_id][$log->product_code][$date][$k])) {
                    $data[$log->user_id][$log->product_code][$date][$k] = $v;
                } else {
                    $data[$log->user_id][$log->product_code][$date][$k] += $v;
                }
            }
        }

        UserBetCountLog::processing($logIds);

        $userIds = array_unique($userIds);
        $users = User::query()->whereIn('id', $userIds)->get();

        $service = new ReportService();
        try {
            DB::transaction(function() use ($service, $data, $users, $logIds) {
                foreach ($data as $userId => $products) {
                    $user = $users->where('id', $userId)->first();
                    foreach ($products as $productCode => $dates) {
                        foreach ($dates as $date => $v) {
                            $service->productReport($user, $productCode, $v, $date);
                        }
                    }
                }

                UserBetCountLog::success($logIds);
            });
        } catch (\Exception $e) {
            Log::error($e->getMessage());
            UserBetCountLog::fail($logIds);
        }

    }
}
