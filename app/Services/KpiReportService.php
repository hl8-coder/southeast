<?php


namespace App\Services;

# Kpi 包含多个字段数据，不能一次性生成一整条数据，包括更新也是
# 因为涉及到统计，并且并不清楚统计部分是增加还是减少，这里尽量使用 队列 + 从新统计 的逻辑
# 这个逻辑基本达成的要求：
#   1、基本可以保证数据的及时性
#   2、在保证数据及时性的同时减轻 DB 的I/O，避免对线上的业务造成冲击
#   3、防止并发数据频繁重复更新，因为更新的结果是一样的，队列的时候需要缓存上锁，锁的状态与数据更新待定 TODO

# 每次字段的更新与生成，都要检测该条数据是否已经存在，如果不存在则需要生成
# 所以每次更新与修改，都要检查数据是否存在，并且决定是否创建，方便后续操作不会报异常
use App\Models\Adjustment;
use App\Models\CompanyBankAccountTransaction;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\KpiReport;
use App\Models\PaymentPlatform;
use App\Models\PgAccountTransaction;
use App\Models\PromotionClaimUser;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserBonusPrize;
use App\Models\UserInfo;
use App\Models\UserPlatformDailyReport;
use App\Models\UserProductDailyReport;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class KpiReportService
{

    /**
     * 根据日期查找或者创建一条 report 数据
     *
     * @param string|null $date 需要定位数据的日期
     * @param string $currency 币别
     * @return KpiReport
     */
    public function getReportByDate(string $currency, string $date = null)
    {
        if ($date == null) {
            $dateString = now()->toDateString();
        } else {
            $dateString = Carbon::parse($date)->toDateString();
        }
        $report = KpiReport::query()->firstOrCreate(['date' => $dateString, 'currency' => $currency]);
        $report->refresh();
        return $report;
    }

    public function updateColumn($type, $currency, $date = null)
    {
        if (!in_array($type, KpiReport::$typeList)) {
            $message = 'service, type index error, type=>' . $type;
            Log::channel('kpi')->error($message);
            return false;
        }

        $currencyCode = Currency::getAll()->pluck('code')->toArray();
        if (empty($currency) || !in_array($currency, $currencyCode)) {
            return false;
        }

        if ($date == null) {
            $dateString = now()->toDateString();
        } else {
            $dateString = Carbon::parse($date)->toDateString();
        }

        $functionName = to_camel_case($type);
        return $this->$functionName($dateString, $currency);
    }

    // total_deposit
    // 也可以通过 天报表关联用户表，使用 currency 条件查询
    public function totalDeposit(string $date, string $currency)
    {
        $dateEnd       = Carbon::parse($date)->endOfDay()->toDateTimeString();
        $dateStart     = Carbon::parse($date)->startOfDay()->toDateTimeString();
        $amountDeposit = Deposit::query()->where('currency', $currency)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where('tag', Deposit::TAG_CLOSED)
            ->where('deposit_at', '>=', $dateStart)
            ->where('deposit_at', '<=', $dateEnd)
            ->sum('arrival_amount');

        $amountAdjustment = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.type', Adjustment::TYPE_DEPOSIT)
            ->where('adjustments.category', Adjustment::CATEGORY_DEPOSIT)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.verified_at', '>=', $dateStart)
            ->where('adjustments.verified_at', '<=', $dateEnd)
            ->sum('adjustments.amount');

        // 为了保证与每日报表保持一致，决定从每日报表拉取数据，发现从明日报表拉取的数据没有 adjustment 部分，暂时不用，等待后续排查，
        //$totalDeposit          = UserPlatformDailyReport::query()
        //    ->leftJoin('users', 'users.id', '=', 'user_platform_daily_reports.user_id')
        //    ->where('users.currency', $currency)
        //    ->where('user_platform_daily_reports.date', $date)
        //    ->where('user_platform_daily_reports.platform_code', UserAccount::MAIN_WALLET)
        //    ->sum('user_platform_daily_reports.deposit');
        //dd($amountAdjustment, $amountDeposit, $totalDeposit);

        $totalDeposit = $amountDeposit + $amountAdjustment;
        $report       = $this->getReportByDate($currency, $date);

        $report->total_deposit = $totalDeposit;
        return $report->save();
    }

    // total_withdrawal
    public function totalWithdrawal(string $date, string $currency)
    {
        //$totalWithdrawal = UserPlatformDailyReport::query()
        //    ->leftJoin('users', 'users.id', '=', 'user_platform_daily_reports.user_id')
        //    ->where('users.currency', $currency)
        //    ->where('user_platform_daily_reports.date', $date)
        //    ->where('user_platform_daily_reports.platform_code', UserAccount::MAIN_WALLET)
        //    ->sum('user_platform_daily_reports.withdrawal');

        $dateEnd   = Carbon::parse($date)->endOfDay()->toDateTimeString();
        $dateStart = Carbon::parse($date)->startOfDay()->toDateTimeString();

        $amountAdjustment = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.type', Adjustment::TYPE_WITHDRAW)
            ->where('adjustments.category', Adjustment::CATEGORY_WD)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.verified_at', '>=', $dateStart)
            ->where('adjustments.verified_at', '<=', $dateEnd)
            ->sum('adjustments.amount');

        $amountWithdraw = Withdrawal::query()
            ->where('currency', $currency)
            ->where('status', Withdrawal::STATUS_SUCCESSFUL)
            ->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->sum('amount');

        $totalWithdrawal = $amountWithdraw + $amountAdjustment;
        $report          = $this->getReportByDate($currency, $date);

        $report->total_withdrawal = $totalWithdrawal;
        return $report->save();
    }

    // net_profit
    public function netProfit(string $date, string $currency)
    {
        // "Net Profit" = Win/Loss - promotion cost - rebate - adjustment (credit) + adjustment (debit) - bank_fee
        //                  total_payout, total_promotion_cost, total_rebate, total_adjustment, bank_fee,
        // $totalProfit = UserProductDailyReport::query()->where('date', $date)->sum('profit');
        //
        // $dateEnd   = Carbon::parse($date)->endOfDay();
        // $dateStart = Carbon::parse($date)->startOfDay();
        //
        // // rebate
        // $depositAmount = Adjustment::query()->where('updated_at', '>=', $dateStart)
        //     ->where('updated_at', '<=', $dateEnd)
        //     ->where('category', Adjustment::CATEGORY_REBATE)
        //     ->where('status', Adjustment::STATUS_SUCCESSFUL)
        //     ->where('type', Adjustment::TYPE_DEPOSIT)
        //     ->sum('amount');
        //
        // $withdrawalAmount = Adjustment::query()->where('updated_at', '>=', $dateStart)
        //     ->where('updated_at', '<=', $dateEnd)
        //     ->where('category', Adjustment::CATEGORY_REBATE)
        //     ->where('status', Adjustment::STATUS_SUCCESSFUL)
        //     ->where('type', Adjustment::TYPE_WITHDRAW)
        //     ->sum('amount');
        //
        // $rebate = $depositAmount - $withdrawalAmount;
        //
        //
        // // promotion cost(adjust)
        // $categoryInclude = [
        //     Adjustment::CATEGORY_REBATE,
        //     Adjustment::CATEGORY_PROMOTION,
        //     Adjustment::CATEGORY_WELCOME_BONUS,
        //     Adjustment::CATEGORY_RETENTION,
        //     Adjustment::CATEGORY_ACCOUNT_SAFETY
        // ];
        // $depositAmount   = Adjustment::query()->where('updated_at', '>=', $dateStart)
        //     ->where('updated_at', '<=', $dateEnd)
        //     ->whereIn('category', $categoryInclude)
        //     ->where('status', Adjustment::STATUS_SUCCESSFUL)
        //     ->where('type', Adjustment::TYPE_DEPOSIT)
        //     ->sum('amount');
        //
        // $withdrawalAmount = Adjustment::query()->where('updated_at', '>=', $dateStart)
        //     ->where('updated_at', '<=', $dateEnd)
        //     ->whereIn('category', $categoryInclude)
        //     ->where('status', Adjustment::STATUS_SUCCESSFUL)
        //     ->where('type', Adjustment::TYPE_WITHDRAW)
        //     ->sum('amount');
        //
        // $promotionCost = $depositAmount - $withdrawalAmount;
        //
        // // adjustment
        // $categoryExclude = [
        //     Adjustment::CATEGORY_REBATE,
        //     Adjustment::CATEGORY_PROMOTION,
        //     Adjustment::CATEGORY_WELCOME_BONUS,
        //     Adjustment::CATEGORY_RETENTION,
        //     Adjustment::CATEGORY_ACCOUNT_SAFETY
        // ];
        // $depositAmount   = Adjustment::query()->where('updated_at', '>=', $dateStart)
        //     ->where('updated_at', '<=', $dateEnd)
        //     ->whereNotIn('category', $categoryExclude)
        //     ->where('status', Adjustment::STATUS_SUCCESSFUL)
        //     ->where('type', Adjustment::TYPE_DEPOSIT)
        //     ->sum('amount');
        //
        // $withdrawalAmount = Adjustment::query()->where('updated_at', '>=', $dateStart)
        //     ->where('updated_at', '<=', $dateEnd)
        //     ->whereNotIn('category', $categoryExclude)
        //     ->where('status', Adjustment::STATUS_SUCCESSFUL)
        //     ->where('type', Adjustment::TYPE_WITHDRAW)
        //     ->sum('amount');
        //
        // $adjustment = $depositAmount - $withdrawalAmount;

        // "Net Profit" = Win/Loss - promotion cost - rebate - adjustment (credit) + adjustment (debit) - bank_fee
        //                  total_payout, total_promotion_cost, total_rebate, total_adjustment, bank_fee,

        $report    = $this->getReportByDate($currency, $date);
        $netProfit = $report->total_payout + $report->total_promotion_cost + $report->total_rebate +
            $report->total_adjustment + $report->bank_fee;

        $report->net_profit = $netProfit;
        return $report->save();
    }

    // total_new_members
    public function totalNewMembers(string $date, string $currency)
    {
        $dateStart       = Carbon::parse($date)->startOfDay();
        $dateEnd         = Carbon::parse($date)->endOfDay();
        $newMemberNumber = User::query()->where('currency', $currency)
            ->where('is_agent', false)
            ->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->count();

        $report = $this->getReportByDate($currency, $date);

        $report->total_new_members = $newMemberNumber;
        return $report->save();
    }

    // total_active_members
    public function totalActiveMembers(string $date, string $currency)
    {
        $dateString = Carbon::parse($date)->toDateString();
        $actives    = UserProductDailyReport::query()
            ->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('users.currency', $currency)
            ->where('user_product_daily_reports.date', $dateString)
            ->where('user_product_daily_reports.stake', '>', 0)
            ->groupBy('users.currency')
            ->select(DB::raw('COUNT(DISTINCT user_product_daily_reports.user_id) as active'))
            ->get();

        if ($actives->isNotEmpty()) {
            $activeNumber = $actives[0]->active;
            $report       = $this->getReportByDate($currency, $date);

            $report->total_active_members = $activeNumber;
            return $report->save();
        }
        return true;
    }

    // total_login_members
    public function totalLoginMembers(string $date, string $currency)
    {
        $dateStart   = Carbon::parse($date)->startOfDay();
        $dateEnd     = Carbon::parse($date)->endOfDay();
        $loginNumber = UserInfo::query()
            ->leftJoin('users', 'users.id', '=', 'user_info.user_id')
            ->where('users.currency', $currency)
            ->where('user_info.last_login_at', '>=', $dateStart)
            ->where('user_info.last_login_at', '<=', $dateEnd)
            ->count();

        $report = $this->getReportByDate($currency, $date);

        $report->total_login_members = $loginNumber;
        return $report->save();
    }

    // total_deposit_members
    public function totalDepositMembers(string $date, string $currency)
    {
        //$totalDeposit = UserPlatformDailyReport::query()
        //    ->leftJoin('users', 'users.id', '=', 'user_platform_daily_reports.user_id')
        //    ->where('users.currency', $currency)
        //   ->where('user_platform_daily_reports.date', $date)
        //    ->where('user_platform_daily_reports.platform_code', UserAccount::MAIN_WALLET)
        //    ->where('user_platform_daily_reports.deposit', '>', 0)
        //    ->count();

        $dateEnd      = Carbon::parse($date)->endOfDay()->toDateTimeString();
        $dateStart    = Carbon::parse($date)->startOfDay()->toDateTimeString();
        $countDeposit = Deposit::query()->where('currency', $currency)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where('tag', Deposit::TAG_CLOSED)
            ->where('deposit_at', '>=', $dateStart)
            ->where('deposit_at', '<=', $dateEnd)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        $countAdjustment = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.type', Adjustment::TYPE_DEPOSIT)
            ->where('adjustments.category', Adjustment::CATEGORY_DEPOSIT)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.verified_at', '>=', $dateStart)
            ->where('adjustments.verified_at', '<=', $dateEnd)
            ->distinct()
            ->pluck('adjustments.user_id')
            ->toArray();

        $userIdUnique = array_unique(array_merge($countAdjustment, $countDeposit));
        $report       = $this->getReportByDate($currency, $date);

        $report->total_deposit_members = count($userIdUnique);
        return $report->save();
    }

    // total_withdrawal_members
    public function totalWithdrawalMembers(string $date, string $currency)
    {
        //$totalWithdrawal = UserPlatformDailyReport::query()
        //    ->leftJoin('users', 'users.id', '=', 'user_platform_daily_reports.user_id')
        //    ->where('users.currency', $currency)
        //    ->where('user_platform_daily_reports.date', $date)
        //    ->where('user_platform_daily_reports.platform_code', UserAccount::MAIN_WALLET)
        //    ->where('user_platform_daily_reports.withdrawal', '<', 0)
        //    ->count();

        $dateEnd   = Carbon::parse($date)->endOfDay()->toDateTimeString();
        $dateStart = Carbon::parse($date)->startOfDay()->toDateTimeString();

        $countAdjustment = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.type', Adjustment::TYPE_WITHDRAW)
            ->where('adjustments.category', Adjustment::CATEGORY_WD)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.verified_at', '>=', $dateStart)
            ->where('adjustments.verified_at', '<=', $dateEnd)
            ->distinct()
            ->pluck('adjustments.user_id')
            ->toArray();

        $countWithdraw = Withdrawal::query()
            ->where('currency', $currency)
            ->where('status', Withdrawal::STATUS_SUCCESSFUL)
            ->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->distinct()
            ->pluck('user_id')
            ->toArray();

        $userIdUnique = array_unique(array_merge($countAdjustment, $countWithdraw));

        $totalWithdrawal = count($userIdUnique);
        $report          = $this->getReportByDate($currency, $date);

        $report->total_withdrawal_members = $totalWithdrawal;
        return $report->save();
    }

    // total_count_deposit
    public function totalCountDeposit(string $date, string $currency)
    {
        $dateStart    = Carbon::parse($date)->startOfDay();
        $dateEnd      = Carbon::parse($date)->endOfDay();
        $depositCount = Deposit::query()
            ->where('currency', $currency)
            ->where('deposit_at', '>=', $dateStart)
            ->where('deposit_at', '<=', $dateEnd)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->count();

        $report = $this->getReportByDate($currency, $date);

        $report->total_count_deposit = $depositCount;
        return $report->save();
    }

    // total_count_withdrawal
    public function totalCountWithdrawal(string $date, string $currency)
    {
        $dateStart       = Carbon::parse($date)->startOfDay();
        $dateEnd         = Carbon::parse($date)->endOfDay();
        $withdrawalCount = Withdrawal::query()
            ->where('currency', $currency)
            ->where('updated_at', '>=', $dateStart)
            ->where('updated_at', '<=', $dateEnd)
            ->where('status', Withdrawal::STATUS_SUCCESSFUL)
            ->count();

        $report = $this->getReportByDate($currency, $date);

        $report->total_count_withdrawal = $withdrawalCount;
        return $report->save();
    }

    // total_turnover
    public function totalTurnover(string $date, string $currency)
    {
        $totalStake = UserProductDailyReport::query()
            ->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('users.currency', $currency)
            ->where('user_product_daily_reports.date', $date)
            ->sum('user_product_daily_reports.stake');

        $report = $this->getReportByDate($currency, $date);

        $report->total_turnover = $totalStake;
        return $report->save();
    }

    // total_payout
    public function totalPayout(string $date, string $currency)
    {
        $totalProfit = UserProductDailyReport::query()
            ->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('users.currency', $currency)
            ->where('user_product_daily_reports.date', $date)
            ->sum('user_product_daily_reports.profit');

        $report = $this->getReportByDate($currency, $date);

        $report->total_payout = -1 * $totalProfit;
        return $report->save();
    }


    //  total_rebate, rebate 给会员为 正
    public function totalRebate(string $date, string $currency)
    {
        $dateStart = Carbon::parse($date)->startOfDay();
        $dateEnd   = Carbon::parse($date)->endOfDay();

        $depositAmount = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.updated_at', '>=', $dateStart)
            ->where('adjustments.updated_at', '<=', $dateEnd)
            ->where('adjustments.category', Adjustment::CATEGORY_REBATE)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.type', Adjustment::TYPE_DEPOSIT)
            ->sum('adjustments.amount');

        $withdrawalAmount = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.updated_at', '>=', $dateStart)
            ->where('adjustments.updated_at', '<=', $dateEnd)
            ->where('adjustments.category', Adjustment::CATEGORY_REBATE)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.type', Adjustment::TYPE_WITHDRAW)
            ->sum('adjustments.amount');

        $report               = $this->getReportByDate($currency, $date);
        $report->total_rebate = $depositAmount - $withdrawalAmount;
        return $report->save();
    }

    //  total_adjustment, 给会员为 正
    public function totalAdjustment(string $date, string $currency)
    {
        $categoryExclude = [
            Adjustment::CATEGORY_REBATE,
            Adjustment::CATEGORY_PROMOTION,
            Adjustment::CATEGORY_WELCOME_BONUS,
            Adjustment::CATEGORY_RETENTION,
            Adjustment::CATEGORY_ACCOUNT_SAFETY,
            Adjustment::CATEGORY_WD,
            Adjustment::CATEGORY_DEPOSIT,
        ];
        $dateStart       = Carbon::parse($date)->startOfDay();
        $dateEnd         = Carbon::parse($date)->endOfDay();
        $depositAmount   = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.updated_at', '>=', $dateStart)
            ->where('adjustments.updated_at', '<=', $dateEnd)
            ->whereNotIn('adjustments.category', $categoryExclude)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.type', Adjustment::TYPE_DEPOSIT)
            ->sum('adjustments.amount');

        $withdrawalAmount = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.updated_at', '>=', $dateStart)
            ->where('adjustments.updated_at', '<=', $dateEnd)
            ->whereNotIn('adjustments.category', $categoryExclude)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.type', Adjustment::TYPE_WITHDRAW)
            ->sum('adjustments.amount');

        $report = $this->getReportByDate($currency, $date);

        $report->total_adjustment = $depositAmount - $withdrawalAmount;
        return $report->save();
    }

    // total_promotion_cost
    public function totalPromotionCost(string $date, string $currency)
    {
        $categoryInclude = [
            Adjustment::CATEGORY_REBATE,
            Adjustment::CATEGORY_PROMOTION,
            Adjustment::CATEGORY_WELCOME_BONUS,
            Adjustment::CATEGORY_RETENTION,
            Adjustment::CATEGORY_ACCOUNT_SAFETY
        ];
        $dateStart       = Carbon::parse($date)->startOfDay();
        $dateEnd         = Carbon::parse($date)->endOfDay();
        $depositAmount   = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.updated_at', '>=', $dateStart)
            ->where('adjustments.updated_at', '<=', $dateEnd)
            ->whereIn('adjustments.category', $categoryInclude)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.type', Adjustment::TYPE_DEPOSIT)
            ->sum('adjustments.amount');

        $withdrawalAmount = Adjustment::query()
            ->leftJoin('users', 'users.id', '=', 'adjustments.user_id')
            ->where('users.currency', $currency)
            ->where('adjustments.updated_at', '>=', $dateStart)
            ->where('adjustments.updated_at', '<=', $dateEnd)
            ->whereIn('adjustments.category', $categoryInclude)
            ->where('adjustments.status', Adjustment::STATUS_SUCCESSFUL)
            ->where('adjustments.type', Adjustment::TYPE_WITHDRAW)
            ->sum('adjustments.amount');

        $report = $this->getReportByDate($currency, $date);

        $report->total_promotion_cost = $depositAmount - $withdrawalAmount;
        return $report->save();
    }

    // total_bank_fee
    public function totalBankFee(string $date, string $currency)
    {
        // deposit 与 withdrawal 表的 fee 原有字段没有计算或这记录到该信息
        // 需要重新计算，重新计算涉及到 payment platform 中 不同的 code 对应的的支付方式，不同的支付方式对应不同的费率
        $feeInfos = PaymentPlatform::query()->where('is_fee', true)->get(['id', 'min_fee', 'max_fee', 'fee_rebate']);

        $totalFee = 0;

        $dateStart = Carbon::parse($date)->startOfDay();
        $dateEnd   = Carbon::parse($date)->endOfDay();
        Deposit::query()
            ->where('currency', $currency)
            ->where('deposit_at', '>=', $dateStart)
            ->where('deposit_at', '<=', $dateEnd)
            ->where('status', Deposit::STATUS_RECHARGE_SUCCESS)
            ->where('tag', Deposit::TAG_CLOSED)
            ->chunk(3, function ($deposits) use (&$totalFee, $feeInfos) {
                foreach ($deposits as $deposit) {
                    $bankFee = 0;
                    if ($deposit->bank_fee == 0) {
                        $feeInfo = $feeInfos->where('id', $deposit->payment_platform_id)->first();
                        if ($feeInfo) {
                            $feeRebate = $feeInfo->fee_rebate / 100;
                            $bankFee   = $feeRebate * $deposit->amount;
                            $bankFee   = $bankFee < $feeInfo->min_fee ? $feeInfo->min_fee : $bankFee;
                            $bankFee   = $bankFee > $feeInfo->max_fee ? $feeInfo->max_fee : $bankFee;
                        }
                    } else {
                        $bankFee = $deposit->bank_fee;
                    }
                    $totalFee += $bankFee;
                }
            });

        $withdrawFee = Withdrawal::query()
            ->where('currency', $currency)
            ->where('status', Withdrawal::STATUS_SUCCESSFUL)
            ->where('created_at', '>=', $dateStart)
            ->where('created_at', '<=', $dateEnd)
            ->sum('fee');

        // 内部转账的费用计算在 VND 里面
        if ($currency == 'VND') {
            $bankFee = CompanyBankAccountTransaction::query()
                ->where('created_at', '>=', $dateStart)
                ->where('created_at', '<=', $dateEnd)
                ->whereNull('user_name')
                ->sum('fee');

            $pgFee    = PgAccountTransaction::query()
                ->where('created_at', '>=', $dateStart)
                ->where('created_at', '<=', $dateEnd)
                ->whereNull('user_name')
                ->sum('fee');
            $totalFee = $totalFee + $bankFee + $pgFee;
        }

        $totalFee = $totalFee + $withdrawFee;
        $report   = $this->getReportByDate($currency, $date);

        $report->total_bank_fee = $totalFee;
        return $report->save();

    }

    // total_promotion_cost_by_code
    public function totalPromotionCostByCode(string $date, string $currency)
    {
        $dateStart = Carbon::parse($date)->startOfDay();
        $dateEnd   = Carbon::parse($date)->endOfDay();

        $bonusIds = PromotionClaimUser::query()
            ->leftJoin('users', 'users.id', '=', 'promotion_claim_users.user_id')
            ->where('users.currency', $currency)
            ->where('promotion_claim_users.updated_at', '>=', $dateStart)
            ->where('promotion_claim_users.updated_at', '<=', $dateEnd)
            ->where('promotion_claim_users.status', PromotionClaimUser::STATUS_APPROVE)
            ->whereNotNull('promotion_claim_users.related_id')
            ->pluck('promotion_claim_users.related_id')
            ->toArray();

        $totalPrize = UserBonusPrize::query()->whereIn('id', $bonusIds)->sum('prize');

        $report = $this->getReportByDate($currency, $date);

        $report->total_promotion_cost_by_code = $totalPrize;
        return $report->save();
    }
}
