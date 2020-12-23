<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;

class GenerateUserTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:generate-user-token';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate User Token';

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
        $userId = $this->ask('输入会员id');

        if (!$user = User::find($userId)) {
            return $this->error('会员不存在');
        }

        // 一年以后过期
        $ttl = 365*24*60;
        $this->info(Auth::guard('api')->setTTL($ttl)->fromUser($user));
    }
}
