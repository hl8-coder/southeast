<?php

namespace App\Console\Commands;

use App\Repositories\AffiliateRepository;
use App\Services\AffiliateService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class CalculateAffiliateCommissionCommand extends Command
{
    /**
     * php artisan southeast:calculate-affiliate-commission-command 1
     *
     * @var string
     */
    protected $signature = 'southeast:calculate-affiliate-commission-command {cal_month=1}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description.';

    protected $startAt;
    protected $endAt;
    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();

        $this->startAt = Carbon::now()->firstOfMonth()->subMonth()->firstOfMonth(); //上个月第一天
        $this->endAt = Carbon::now()->firstOfMonth()->subMonth()->lastOfMonth(); //上个月最后一天
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $calMonth = $this->argument('cal_month'); //算本月=0, 上月=1, 前月=2...依此类推

        $this->startAt = Carbon::now()->firstOfMonth()->subMonths($calMonth)->firstOfMonth(); //上个月第一天
        $this->endAt   = Carbon::now()->firstOfMonth()->subMonths($calMonth)->lastOfMonth(); //这个月第一天
        Log::stack(['calculate-affiliate-commission'])->info("====计算周期:{$this->startAt} ~ {$this->endAt}====");

        $service = new AffiliateService();

        # 循环所有代理计算分红
        foreach (AffiliateRepository::getAffiliateByParentIdList() as $user) {

            if ($affiliateCommission = $service->calculateCommission($user, $this->startAt, $this->endAt)) {

                # 获取代理银行卡
                if ($userBankAccount = $user->bankAccounts()->active()->first()) {
                    $affiliateCommission->bank_id       = $userBankAccount->bank_id;
                    $affiliateCommission->province      = $userBankAccount->province;
                    $affiliateCommission->city          = $userBankAccount->city;
                    $affiliateCommission->branch        = $userBankAccount->branch;
                    $affiliateCommission->account_no    = $userBankAccount->account_no;
                    $affiliateCommission->account_name  = $userBankAccount->account_name;
                }

                $affiliateCommission->save();
            }

            Log::stack(['calculate-affiliate-commission'])->warning($user->name . ' 完成代理计算。');
        }
    }
}
