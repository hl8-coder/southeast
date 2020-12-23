<?php

namespace App\Console\Commands;

use App\Jobs\TransactionProcessJob;
use App\Models\Transaction;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AddFailTransactionsToJobCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:add-fail-transactions-to-job';

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
        # 获取2分钟以上还未添加进队列或者失败的transaction
        $transactions = Transaction::query()->where('created_at', '<=', now()->subMinutes(2))
                        ->whereIn('status', [
                            Transaction::STATUS_CREATED,
                            Transaction::STATUS_FAIL,
                        ])
                        ->get();

        foreach ($transactions as $transaction) {

            if ($transaction->status == Transaction::STATUS_FAIL) {
                $transaction->update([
                    'status' => Transaction::STATUS_CREATED,
                ]);
            }

            Log::info('将NO为: ' . $transaction->order_no . ' Transaction重新弄加入队列处理');

            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }
    }
}
