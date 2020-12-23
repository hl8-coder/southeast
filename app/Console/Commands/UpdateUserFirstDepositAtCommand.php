<?php

namespace App\Console\Commands;

use App\Models\Deposit;
use App\Models\User;
use Illuminate\Console\Command;

class UpdateUserFirstDepositAtCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:update-user-first-deposit-at';

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
        $users = User::query()->whereNull('first_deposit_at')->get();
        foreach ($users as $user) {
            $deposit = $user->deposits()->where('status', Deposit::STATUS_RECHARGE_SUCCESS)->first();
            if ($deposit) {
                $user->update([
                    'first_deposit_at' => $deposit->deposit_at
                ]);
                $this->info($user->name . '更新第一次充值时间成功');
            }
        }
    }
}
