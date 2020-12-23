<?php

namespace App\Jobs;

use App\Services\NexmoService;
use App\Services\SMSService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class NexmoJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $phone;
    public $code;
    public $isNexmo;
    public $currency;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($phone, $code, $currency, bool $isNexmo = true)
    {
        $this->phone   = $phone;
        $this->code    = $code;
        $this->isNexmo = $isNexmo;
        $this->currency = $currency;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->isNexmo) {
            $service = new NexmoService();
            $service->sms($this->phone, $this->code, $this->currency);
        } else {
            $smsService = new SMSService();

            $smsService->sms($this->phone, $this->code, $this->currency);
        }
    }
}
