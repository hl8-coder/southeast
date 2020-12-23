<?php

namespace App\Jobs;

use App\Models\GamePlatformTransferDetail;
use App\Models\UserMessage;
use App\Models\UserMessageDetail;
use App\Repositories\GamePlatformTransferDetailRepository;
use App\Services\GamePlatformService;
use App\Services\SMSService;
use App\Services\UserMessageService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\Log;

class SendMessageJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $userMessageDetail;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(UserMessageDetail $userMessageDetail)
    {
        $this->userMessageDetail = $userMessageDetail;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if (!$this->userMessageDetail->isDelivered()) {
            Log::stack(['send_message'])->info('status=' . $this->userMessageDetail->status . ' illegal');
            return ;
        }

        try {
            app(UserMessageService::class)->process($this->userMessageDetail);
        } catch (\Exception $e) {
            $this->userMessageDetail->setToFailed($e->getMessage());
            Log::stack(['send_message'])->info('system process error, message: '. $e->getMessage());
        }
    }
}
