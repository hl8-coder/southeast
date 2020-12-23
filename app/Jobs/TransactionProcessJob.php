<?php

namespace App\Jobs;

use App\Models\Transaction;
use App\Models\UserAccount;
use App\Services\TransactionService;
use Exception;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;

class TransactionProcessJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $transaction;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Transaction $transaction)
    {
        $this->transaction = $transaction;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if ($this->transaction && $this->transaction->isCreated()) {
            try {
                $this->transaction->start();
                DB::transaction(function() {
                    (new TransactionService())->process($this->transaction);
                });
            } catch (Exception $e) {
                $this->transaction->fail(str_limit($e->getMessage(), 256, '...'));
            }
        }
    }
}
