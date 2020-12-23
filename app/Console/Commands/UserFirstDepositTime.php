<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class UserFirstDepositTime extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:user_first_deposit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '会员第一次充值时间';

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
            if ($user->depositsSuccessFirst(1)->first()) {
                $user->first_deposit_at = $user->depositsSuccessFirst(1)->first()->deposit_at;
                $user->save();
            }
        }
    }
}
