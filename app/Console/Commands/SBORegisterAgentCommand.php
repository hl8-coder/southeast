<?php

namespace App\Console\Commands;

use App\GamePlatforms\SBOPlatform;
use App\Models\GamePlatform;
use Illuminate\Console\Command;

class SBORegisterAgentCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:sbo-register-agent {--agent_name=} {--currency=} ';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    protected $password = '123qwe';

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
        $agentName = $this->option('agent_name');
        $currency  = $this->option('currency');
        $platform  = GamePlatform::findByCode('SBO');
        try {
            if ((new SBOPlatform([null, $platform]))->registerAgent($agentName, $this->password, $currency)) {
                $this->info('注册成功，币别: ' . $currency . ',代理名称: ' . $agentName);
            }
        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }
}
