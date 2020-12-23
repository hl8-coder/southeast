<?php

namespace App\Console\Commands;

use App\Models\BatchTransferBackLog;
use App\Models\GamePlatformUser;
use App\Services\GamePlatformService;
use Illuminate\Console\Command;

class BatchTransferBackAllUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:batch-transfer-back-all-user';

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

        # 所有已经注册第三方会员的第三方钱包转到主钱包
        $platformUsers = GamePlatformUser::query()->where('platform_code', $platformCode)
                        ->whereNotNull('platform_user_id')
                        ->whereNotNull('platform_created_at')
                        ->get();

        $service = new GamePlatformService();
        foreach ($platformUsers as $platformUser) {
            try {
                # 获取最新余额
                $platformUser = $service->balance($platformUser->user, $platformUser->platform);

                if (!empty((float)$platformUser->balance)) {
                    # 发起转账
                    $service->transferOut($platformUser->platform, $platformUser->user, $platformUser->balance, '', 'System', false, false);
                }

                # 添加转账记录
                BatchTransferBackLog::add(
                    $platformUser->platform_code,
                    $platformUser->id,
                    $platformUser->user_id,
                    $platformUser->user_name,
                    $platformUser->balance,
                    BatchTransferBackLog::STATUS_TRANSFER_IN_SUCCESSFUL
                );

                # 设置平台会员为未注册
                $platformUser->update([
                    'platform_user_id' => null,
                    'platform_created_at' => null,
                ]);

                $this->info($platformUser->user_name . '将' . $platformCode . '转回主钱包成功');
            } catch (\Exception $e) {
                # 添加转账记录
                BatchTransferBackLog::add(
                    $platformUser->platform_code,
                    $platformUser->id,
                    $platformUser->user_id,
                    $platformUser->user_name,
                    $platformUser->balance,
                    BatchTransferBackLog::STATUS_TRANSFER_IN_FAIL
                );
                $this->warn($platformUser->user_name . '将' . $platformCode . '转回主钱包失败， 失败原因：' . $e->getMessage());
            }

        }

    }
}
