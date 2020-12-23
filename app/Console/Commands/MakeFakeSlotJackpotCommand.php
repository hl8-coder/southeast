<?php

namespace App\Console\Commands;

use App\Models\ChangingConfig;
use App\Models\Currency;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class MakeFakeSlotJackpotCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:make-fake-slot-jackpot';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'make fake data for slot jackpot every minute';

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
        $initAmount = [
            'VND' => 96383331.59,
            'CNY' => 21059171.59,
            'USD' => 9021371.59,
            'THB' => 30589371.59,
        ];
        foreach ($initAmount as $currency => $initAmount){
            $this->make($currency, $initAmount);
        }

    }

    private function make($currency, $baseAmount)
    {
        $cacheKey = 'slot_jackpot_' . $currency;
        $code     = 'slot_jackpot_' . $currency;

        $amount = ChangingConfig::findValue($code, 0);
        if ($amount === 0) {
            $amount = $baseAmount;
            app(ChangingConfig::class)->create(['code' => $code, 'name' => $code, 'value' => $baseAmount, 'remark' => $currency . '_slot 奖池数据']);
        }
        $increaseAmount = random_int(20, 200) + rand(0, 99) / 100;
        $amount         += $increaseAmount;
        $amount         = format_number($amount, 2);
        ChangingConfig::setValue($code, $amount);
        Cache::put($cacheKey, $amount, now()->addSeconds(60));
    }
}
