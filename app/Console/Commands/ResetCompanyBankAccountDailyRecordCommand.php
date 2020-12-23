<?php

namespace App\Console\Commands;

use App\Models\CompanyBankAccount;
use Illuminate\Console\Command;

class ResetCompanyBankAccountDailyRecordCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:reset-company-bank-account-daily-records';

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
        CompanyBankAccount::query()->update([
            'daily_fund_out'    => 0,
            'daily_fund_in'     => 0,
            'daily_transaction' => 0,
        ]);
    }
}
