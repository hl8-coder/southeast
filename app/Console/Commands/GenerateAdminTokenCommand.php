<?php

namespace App\Console\Commands;

use App\Models\Admin;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;


class GenerateAdminTokenCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:generate-admin-token';

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
        $adminId = $this->ask('输入管理员id');

        if (!$admin = Admin::find($adminId)) {
            return $this->error('管理员不存在');
        }

        // 一年以后过期
        $ttl = 365*24*60;
        $this->info(Auth::guard('admin')->setTTL($ttl)->fromUser($admin));
    }
}
