<?php

namespace App\Console\Commands;

use App\Repositories\GamePlatformTransferDetailRepository;
use Illuminate\Console\Command;

class CheckWaitTransferDetailCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:check-wait-transfer-detail';

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
        # 获取等待的第三方状态的转账记录
        $waitingDetails = GamePlatformTransferDetailRepository::getWaitingDetails();

        foreach ($waitingDetails as $detail) {
            GamePlatformTransferDetailRepository::addCheckJob($detail);
        }
    }
}
