<?php

namespace App\Jobs;

use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Services\BankTransactionService;
use App\Services\DepositBacksideService;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoDepositJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $deposit;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Deposit $deposit)
    {
        $this->deposit = $deposit;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $transactions = [];

        $deposit = $this->deposit;

        if (Deposit::STATUS_CREATED != $deposit->status
            || $deposit->payment_type != PaymentPlatform::PAYMENT_TYPE_BANKCARD
            || !(empty($deposit->auto_status) || Deposit::AUTO_STATUS_FAIL == $deposit->auto_status)
            || !$deposit->companyBankAccount->bank->is_auto_deposit
            ) {
            Log::stack(['auto_deposit'])->info('充值 ' . $deposit->order_no . ' 自动上分失败：不符合条件。');
            return;
        }

        $bankTransaction = (new BankTransactionService())->getTransaction($this->deposit);

        if (empty($bankTransaction)) {
            Log::stack(['auto_deposit'])->info('充值 ' . $deposit->order_no . ' 自动上分失败：未找到对应银行记录。');
            return;
        }

        $deposit->startAuto();

        Log::stack(['auto_deposit'])->info('充值 ' . $deposit->order_no . ' 开始自动上分');

        $depositBacksideService = new DepositBacksideService();

        try {
            DB::transaction(function () use ($deposit, &$transactions, $bankTransaction, $depositBacksideService) {
                # 自動match
                $transactions = $depositBacksideService->match($deposit->user, $deposit, $bankTransaction);
            });
        } catch (\Exception $e) {
            $deposit->autoFail($e->getMessage());
            Log::stack(['auto_deposit'])->info('充值 ' . $deposit->order_no . ' 自动上分失败：' . $e->getMessage());
            return;
        }

        if ($transactions) {
            foreach ($transactions as $transaction) {
                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        $deposit->autoSuccess();

        Log::stack(['auto_deposit'])->info('充值 ' . $deposit->order_no . ' 自动上分成功');

        return;
    }
}
