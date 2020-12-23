<?php

namespace App\Services;

use App\Models\Affiliate;
use App\Models\AffiliateCommission;
use App\Models\Config;
use App\Models\Currency;
use App\Models\User;
use App\Repositories\AffiliateRepository;
use Carbon\Carbon;
use Illuminate\Support\Facades\Log;

class AffiliateService
{
    public function calculateCommission(User $user, $startAt, $endAt)
    {
        $currency = Currency::findByCodeFromCache($user->currency);
        # 读取代理抽水直属下级百分比, 无资料无法继续
        if (!$childCommissionPercent = Config::findValue('child_commission_percent')) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取返上级代理佣金百分比失败, 终止操作');
            return null;
        }

        # 读取产品手续费百分比, 无资料无法继续
        if (!$productFeePercent = Config::findValue('product_fee_percent')) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取产品手续费百分比失败, 终止操作');
            return null;
        }

        # 读取代理分红最小出款金额, 无资料无法继续
        if (!$payoutMinLimit = $currency->payout_comm_mini_limit) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取代理分红最小出款金额, 终止操作');
            return null;
        }

        # 读取充值手续费, 无资料无法继续
        if (!$depositFeePercent = Config::findValue('deposit_fee_percent')) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取充值手续费百分比失败, 终止操作');
            return null;
        }

        # 读取提款手续费, 无资料无法继续
        if (!$withdrawalFeePercent = Config::findValue('withdrawal_fee_percent')) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取提款手续费百分比失败, 终止操作');
            return null;
        }

        # 读取代理盈亏最小给款活跃人数限制, 无资料无法继续
        if (!$activeCountMinLimit = Config::findValue('active_count_min_limit')) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取最低活跃人数限制失败, 终止操作');
            return null;
        }

        # 读取代理分红清空限制
        if (!$commissionClearLimit = Config::findValue('commission_clear_limit')) {
            Log::stack(['calculate-affiliate-commission'])->warning('读取代理分红清空限制, 终止操作');
            return null;
        }

        # 判断会员是否有代理账号切代理账号可使用
        $affiliate = $user->affiliate;
        if (!$affiliate || !$affiliate->isActive()) {
            Log::stack(['calculate-affiliate-commission'])->warning($user->name . ' 不存在代理账号或者代理账号不可使用, 无法计算分红');
            return null;
        }

        # 检查代理奖励是否设置
        if (!$commissionSettings = $affiliate->commission_setting) {
            Log::stack(['calculate-affiliate-commission'])->warning($user->name . ' 未设定分红奖励, 无法计算分红');
            return null;
        }

        # 检查周期内是否已建立了分红奖励
        if (AffiliateCommission::isExistsByDate($user->id, $startAt, $endAt)) {
            Log::stack(['calculate-affiliate-commission'])->warning($user->name . ' 周期内已计算分红, 无需计算分红');
            return null;
        }

        # 初始化代理分红奖励
        $affiliateCommission = new AffiliateCommission([
            'user_id'                       => $user->id,
            'user_name'                     => $user->name,
            'affiliate_id'                  => $affiliate->id,
            'currency'                      => $user->currency,
            'start_at'                      => $startAt,
            'end_at'                        => $endAt,
            'calculate_setting'             => null,
            'rake'                          => 0,
            'profit'                        => 0,
            'stake'                         => 0,
            'rebate'                        => 0,
            'sub_adjustment'                => 0,
            'affiliate_adjustment'          => 0,
            'active_count'                  => 0,
            'parent_commission'             => 0,
            'promotion'                     => 0,
            'deposit'                       => 0,
            'withdrawal'                    => 0,
            'transaction_cost'              => 0,
            'bear_cost'                     => 0,
            'product_cost'                  => 0,
            'net_loss'                      => 0,
            'sub_commission'                => 0,
            'sub_commission_percent'        => $childCommissionPercent,
            'remain_commission'             => 0,
            'previous_remain_commission'    => 0,
            'total_commission'              => 0,
            'payout_commission'             => 0,
        ]);

        # 计算所有直属下级盈亏
        $subGameData = AffiliateRepository::getAllSubUserGameData($user->id, $startAt, $endAt);
        $affiliateCommission->profit    = !empty($subGameData['profit']) ? -1 * $subGameData['profit'] : 0;
        $affiliateCommission->stake     = !empty($subGameData['stake'])  ? $subGameData['stake'] : 0;
        $affiliateCommission->rebate    = !empty($subGameData['rebate']) ? $subGameData['rebate'] : 0;

        # 获取所有充值和提现数据
        $subTransactionData = AffiliateRepository::getAllSubUserTransactionData($user->id, $startAt, $endAt);
        $affiliateCommission->deposit           = !empty($subTransactionData['deposit'])    ? $subTransactionData['deposit'] : 0;
        $affiliateCommission->withdrawal        = !empty($subTransactionData['withdrawal']) ? $subTransactionData['withdrawal'] : 0;
        $affiliateCommission->sub_adjustment    = !empty($subTransactionData['adjustment']) ? $subTransactionData['adjustment'] : 0;
        $affiliateCommission->promotion         = !empty($subTransactionData['promotion'])  ? $subTransactionData['promotion']  : 0;

        # 获取代理的自身调整adjustment
        $selfTransactionData = AffiliateRepository::getSelfTransactionData($user->id, $startAt, $endAt);
        $affiliateCommission->affiliate_adjustment  = !empty($selfTransactionData['adjustment']) ? $selfTransactionData['adjustment'] : 0;
        
        # 获取所有直属下级抽成
        $affiliateCommission->sub_commission = AffiliateRepository::getAllSubAgentCommission($user->id, $startAt, $endAt);
        # 获取上个周期剩余数据
        if ($recentAffiliateCommission = AffiliateRepository::getRecentAffiliateCommission($user->id)) {
            $affiliateCommission->previous_remain_commission = $recentAffiliateCommission->remain_commission;
            $affiliateCommission->total_commission = $recentAffiliateCommission->remain_commission;
        }

        # 充提手续费 = 充值 * 充值手续费(1%) + 提现 * 提现手续费(1%)
        $affiliateCommission->transaction_cost = ($affiliateCommission->deposit * $depositFeePercent + $affiliateCommission->withdrawal * $withdrawalFeePercent) / 100;

        # 代理承担费用 = 返点 + 优惠 + 下级会员调整金额
        $affiliateCommission->bear_cost = $affiliateCommission->rebate + $affiliateCommission->promotion + $affiliateCommission->sub_adjustment;

        # 基础盈亏 = 公司盈亏 + 棋牌抽成 - 代理承担费用
        $affiliateCommission->net_loss = $affiliateCommission->profit + $affiliateCommission->rake - $affiliateCommission->bear_cost;

        # 产品费用
        $affiliateCommission->product_cost = $affiliateCommission->net_loss  * $productFeePercent / 100;

        $affiliateCommission->calculate_setting = $this->getCalculateSetting($commissionSettings, $affiliateCommission->profit);
        if ($affiliateCommission->calculate_setting) {
            # 总奖励 = 基础盈亏 * 分红系数 - 基础盈亏 * 产品手续费系数 - 代理调整金额 + 下级分红抽成 + 上期未支付金额(上面已添加);
            $affiliateCommission->total_commission += $affiliateCommission->net_loss  * $affiliateCommission->calculate_setting['value'] / 100
                - $affiliateCommission->product_cost - $affiliateCommission->affiliate_adjustment + $affiliateCommission->sub_commission - $affiliateCommission->transaction_cost;

            if ($affiliateCommission->total_commission > 0) {
                # 奖励若大于0, 判断奖励是否达到派发条件，如果未达到则转成下次派发并计算上级抽成
                if ($affiliateCommission->total_commission >= $payoutMinLimit) {
                    $affiliateCommission->payout_commission = $affiliateCommission->total_commission;

                    # 计算上级分红抽成
                    $affiliateCommission->parent_commission = $affiliateCommission->total_commission * $childCommissionPercent / 100;
                } else {
                    $affiliateCommission->remain_commission = $affiliateCommission->total_commission;
                }
            } else {
                # 奖励若小于0， 判断是否达到清零条件, 没有达到则将本次分红计算数据转到下个周期的计算数据
                if (abs($affiliateCommission->total_commission) > abs($commissionClearLimit)) {
                    $affiliateCommission->remain_commission = $affiliateCommission->total_commission;
                }
            }

        }

        # 计算活跃人数,如果活跃人数小于设定值上述数据只记录实际无效，剩余分红抓上次分红
        $affiliateCommission->active_count = AffiliateRepository::getSubActiveUserCount($user->id, $startAt, $endAt);
        if ($affiliateCommission->active_count < $activeCountMinLimit) {
            $affiliateCommission->remain_commission = $affiliateCommission->previous_remain_commission;
            $affiliateCommission->total_commission  = 0;
            $affiliateCommission->payout_commission = 0;
        }

        return $affiliateCommission;
    }

    /**
     * 根据条件获取计算条件
     *
     * @param $settings
     * @param $profit
     * @return null
     */
    public function getCalculateSetting($settings, $profit)
    {
        $calculateSetting = null;
        $settings = collect($settings)->sortBy('tier')->toArray();

        foreach ($settings as $setting) {
            if ($profit >= $setting['profit']) {
                $calculateSetting = $setting;
            }
        }

        return $calculateSetting;
    }

    /**
     * 根据月份获取代理分红数据
     *
     * @param Affiliate $affiliate
     * @param $month
     * @return \Illuminate\Database\Eloquent\Builder[]|\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection
     */
    public function getCommissionByMonth(Affiliate $affiliate, $month)
    {
        # 如果月份是当前月份，则需要通过计算获取
        # 如果月份是其他月份，直接拉取分红报表数据
        $month = Carbon::parse($month);
        if ($month->month == now()->month) {
            $commission = $this->calculateCommission($affiliate->user, $month->firstOfMonth()->copy(), $month->lastOfMonth());
            $commissions = collect([$commission]);
        } else {
            $commissions =  AffiliateCommission::query()
                ->where('affiliate_id', $affiliate->id)
                ->where('start_at', $month->startOfMonth()->toDateString())
                ->get();
        }

        return $commissions;
    }
}