<?php

namespace App\Console\Commands;

use App\Models\BatchTransferBackLog;
use App\Services\GamePlatformService;
use Illuminate\Console\Command;

class BatchTransferToAllUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:batch-transfer-to-all-user';

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
        $platformCode = 'SBO';

        # 获取所有转回成功的单
        $logs = BatchTransferBackLog::query()->where('platform_code', $platformCode)
                ->where('status', BatchTransferBackLog::STATUS_TRANSFER_IN_SUCCESSFUL)
                ->get();

        $service = new GamePlatformService();
        foreach ($logs as $log) {
            try {
                # 获取最新余额
                $platformUser = $log->platformUser;

                if (!empty((float)$log->amount)) {
                    # 发起转入转账
                    $service->transferIn($platformUser->platform, $platformUser->user, $log->amount, '', '', 'System');
                }

                # 修改转账记录
                $log->update([
                    'status' => BatchTransferBackLog::STATUS_TRANSFER_OUT_SUCCESSFUL
                ]);

                $this->info($platformUser->user_name . '转回' . $platformCode . '第三方成功');
            } catch (\Exception $e) {
                # 添加转账记录
                $log->update([
                    'status' => BatchTransferBackLog::STATUS_TRANSFER_OUT_FAIL
                ]);
                $this->warn($platformUser->user_name . '将' . $platformCode . '转回第三方失败， 失败原因：' . $e->getLine() . ' ' . $e->getMessage());
            }

        }

    }
}
