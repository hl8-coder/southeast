<?php

namespace App\Console\Commands;

use App\Jobs\GameBetDetailProcessJob;
use App\Models\GameBetDetail;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddGameBetDetailsToProcessCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:add-game-bet-details-to-process';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

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
        # 获取未处理交易记录
        $details = GameBetDetail::query()->where('status', GameBetDetail::STATUS_CREATED)
            ->orderBy('payout_at')
            ->limit(2000)
            ->get();

        foreach ($details as $detail) {
            $detail->start();
            dispatch(new GameBetDetailProcessJob($detail))->onQueue('process_' . $detail->user_id % GameBetDetail::JOB_NUM);
        }
        Log::stack(['add_game_bet_detail_to_process'])->info('共 ' . count($details) . ' 条交易记录被添加到投注详情处理队列。');
    }
}
