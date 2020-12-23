<?php

namespace App\Console\Commands;

use App\Jobs\TransactionProcessJob;
use App\Models\Rebate;
use App\Models\RiskGroup;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserProductDailyReport;
use App\Repositories\UserRebatePrizeRepository;
use App\Repositories\UserRepository;
use App\Services\TransactionService;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class CalculateRebatesCommand extends Command
{
    /**
     * 按天统计返点奖励，并生成记录数据，后续根据返点派发中是否为自动派发，程序选择在生成派发订单是否自动派发奖金
     *
     * @var string
     */
    protected $signature = 'southeast:calculate-rebates';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Calculate Rebates';

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
     * 按天生成用户返点奖励数据，通常情况下根据昨天的用户的流水情况，生成返点数据
     *
     * @return mixed
     */
    public function handle()
    {
        # 获取所有返点
        $rebates = Rebate::getAll()->where('status', true)->sortByDesc('sort');
        foreach ($rebates as $rebate) {
            $this->calculateAllUserRebate($rebate);
        }
    }

    /**
     * 根据已经设置返点条件，找出该返点对应产品【product_code】在天报表里面，对应的所有的用户统计数据
     *
     * @param Rebate $rebate
     */
    public function calculateAllUserRebate(Rebate $rebate)
    {
        # 根据 rebate 设置对应的 product_code 获取过去一天所有会员日报表在天报表里面的统计数据
        $userProductDailyReports = UserProductDailyReport::getRebateReport($rebate);

        foreach ($userProductDailyReports as $report) {
            # 判断会员是否可以计算返点
            $user = UserRepository::find($report->user_id);
            if ($this->checkUserCanUserRebate($user, $rebate)) {
                $this->createRebatePrize($user, $rebate, $report);
            }
        }
    }

    /**
     * 判断会员是否能使用该返点
     *
     * @param User $user
     * @param Rebate $rebate
     * @return bool
     */
    public function checkUserCanUserRebate(User $user, Rebate $rebate)
    {
        if (!is_null($rebate->risk_group_id) && $rebate->risk_group_id != $user->risk_group_id) {
            return false;
        }

        if (!$rebate->getVipSet($user->vip_id)) {
            return false;
        }

        if (!$rebate->getCurrencySet($user->currency)) {
            return false;
        }

        # risk group rule limit
        $riskGroup = $user->riskGroup;
        if ($riskGroup){
            $rules = $riskGroup->rules ?? [];
            if (in_array(RiskGroup::RULE_NO_AUTO_REBATE, $rules)){
                return false;
            }
        }

        return true;
    }

    /**
     * 创建单个用户 单个返点条件返点奖励数据
     *
     * @param User $user
     * @param Rebate $rebate
     * @param UserProductDailyReport $report
     */
    public function createRebatePrize(User $user, Rebate $rebate, UserProductDailyReport $report)
    {
        try {
            $transaction = DB::transaction(function() use ($user, $rebate, $report) {

                # 创建 rebate prize 单条数据，并将派发形式 自动或者手动 写入到数据中
                $userRebatePrize = UserRebatePrizeRepository::create($user, $rebate, $report);

                # 人工派发的订单，订单创建后是 【等待市场派单】 状态
                # 设置了自动派发的订单，订单创建后是 【create】状态，会在接下来的判断，直接将 rebate prize 派发出去，并做相应的帐变累积统计
                if ($userRebatePrize && $userRebatePrize->isCreated()) {

                    if (UserRebatePrizeRepository::setSuccess($userRebatePrize, 'System')) {
                        # 帐变记录
                        $transaction = app(TransactionService::class)->addTransaction(
                            $report->user,
                            $userRebatePrize->prize,
                            Transaction::TYPE_REBATE_PRIZE,
                            $userRebatePrize->id
                        );

                        return $transaction;
                    }
                }

                return null;
            });
        } catch (\Exception $e) {
            Log::stack(['calculate_rebates'])->info('计算返点失败, 返点code: ' . $rebate->code . ', 数据来源：' . $report->id . ',错误信息：' . $e->getMessage());
            return;
        }

        if ($transaction) {
            dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
        }
    }
}
