<?php

namespace App\Jobs;

use App\Models\GamePlatformTransferDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Services\GamePlatformService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class CheckWaitTransferDetailJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected  $detail;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(GamePlatformTransferDetail $detail)
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
        if (!$this->detail->isChecking()) {
            return;
        }

        $service = new GamePlatformService();

        try {
            $detail = $service->check($this->detail->user, $this->detail->platform, ['detail' => $this->detail]);
        } catch (\Exception $e) {
            GamePlatformTransferDetailRepository::setWaitManualConfirm($this->detail);
            Log::stack(['check_wait_transfer_detail'])->info('检查第三方转账：' . $this->detail->id . ' 失败, 原因:' . $e->getMessage());
            return;
        }

        # 后续处理，如果是adjustment的单就不需要进行后续处理了
        if (!empty($detail) && !$detail->isAdjustmentDetail()) {
            # 后续处理
            if ($detail->isIncome()) {
                $service->transferInAfterDo($detail, $detail->user, $detail->userBonusPrize);
            } else {
                $service->transferOutAfterDo($detail, $detail->user, false);
            }
        }
        Log::stack(['check_wait_transfer_detail'])->info('检查第三方转账：' . $this->detail->id . ' 成功');

        # 如果超过配置check次数转为人工确认
//        if ($this->detail->isWait() && GamePlatformTransferDetailRepository::isExceedCheckLimit($this->detail)) {
//            GamePlatformTransferDetailRepository::setWaitManualConfirm($this->detail);
//        }
    }
}
