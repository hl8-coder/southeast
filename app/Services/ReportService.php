<?php

namespace App\Services;

use App\Models\CompanyBankAccountReport;
use App\Models\CompanyBankAccountTransaction;
use App\Models\Game;
use App\Models\PgAccountReport;
use App\Models\PgAccountTransaction;
use App\Models\User;
use App\Models\UserPlatformDailyReport;
use App\Models\UserPlatformMonthlyReport;
use App\Models\UserPlatformTotalReport;
use App\Models\UserProductDailyReport;
use App\Models\UserProductMonthlyReport;
use App\Models\UserProductTotalReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ReportService
{

    /**
     * 报表统计
     *
     * @param User $user
     * @param $productCode
     * @param $data
     * @param $date
     */
    public function productReport(User $user, $productCode, $data, $date)
    {
        # 日报表
        UserProductDailyReport::productRecord($user, $productCode, $data, $date);

        # 月报表
        UserProductMonthlyReport::productRecord($user, $productCode, $data, Carbon::parse($date)->format('Y-m'));

        # 总报表
        UserProductTotalReport::productRecord($user, $productCode, $data);
    }

    /**
     * 游戏平台报表统计
     *
     * @param User      $user           会员
     * @param string    $platformCode   平台code
     * @param integer   $type           类型
     * @param float     $value          数值
     * @param string    $transactionAt  时间
     */
    public function platformReport(User $user, $platformCode, $type, $value, $transactionAt)
    {
        # 日报表
        UserPlatformDailyReport::platformRecord($user, $platformCode, $type, $value, $transactionAt->toDateString());

        # 月报表
        UserPlatformMonthlyReport::platformRecord($user, $platformCode, $type, $value, $transactionAt->format('Y-m'));

        # 总报表
        UserPlatformTotalReport::platformRecord($user, $platformCode, $type, $value);
    }

    /**
     * 公司银行卡报表统计
     *
     * @param CompanyBankAccountTransaction $transaction
     */
    public function companyBankAccountReport(CompanyBankAccountTransaction $transaction)
    {
        $field = CompanyBankAccountReport::$typeMappingFields[$transaction->type];

        # 如果是buffer字段需要拆分
        if ('buffer' == CompanyBankAccountReport::$typeMappingFields[$transaction->type]) {
            $field = $transaction->is_income ? 'buffer_in' : 'buffer_out';
        }

        $amount = $transaction->is_income ? abs($transaction->total_amount) : -1 * abs($transaction->total_amount);

        $report = CompanyBankAccountReport::query()->firstOrCreate([
            'company_bank_account_code'     =>  $transaction->company_bank_account_code,
            'date'                          =>  $transaction->created_at->toDateString(),
        ]);

        $before_balance = $transaction->after_balance - $amount;

        if (empty($report->opening_balance)) {
            $report->setPrimaryKeyQuery()->update([
                $field              => DB::raw("$field + $amount"),
                'opening_balance'   => $before_balance,
                'ending_balance'    => $transaction->after_balance,
            ]);
        } else {
            $report->setPrimaryKeyQuery()->update([
                $field              => DB::raw("$field + $amount"),
                'ending_balance'    => $transaction->after_balance,
            ]);
        }
    }

    /**
     * 第三方通道报表统计.
     *
     * @param PgAccountTransaction $transaction
     */
    public function pgAccountReport(PgAccountTransaction $transaction)
    {
        if ($transaction->is_income) {
            $data = [
                'deposit' => DB::raw("deposit + $transaction->amount"),
                'deposit_fee' => DB::raw("deposit_fee + $transaction->fee")
            ];
        } else {
            $data = [
                'withdraw' => DB::raw("withdraw + $transaction->amount"),
                'withdraw_fee' => DB::raw("withdraw_fee + $transaction->fee")
            ];
        }

        if ($transaction->type == PgAccountTransaction::TYPE_USER_DEPOSIT) { # 用户存款
            $before_balance = $transaction->after_balance - abs($transaction->amount);
        } elseif ($transaction->type == PgAccountTransaction::TYPE_COMPANY_WITHDRAWAL) { # 公司从第三方提款
            $before_balance = $transaction->after_balance + abs($transaction->total_amount);
        } else { # 手动调整金额.
            $before_balance = $transaction->is_income ? $transaction->after_balance - abs($transaction->total_amount) : $transaction->after_balance + abs($transaction->total_amount);
        }

        $report = PgAccountReport::query()->firstOrCreate([
            'payment_platform_code'     =>  $transaction->payment_platform_code,
            'date'                      =>  $transaction->created_at->toDateString(),
        ]);


        if (empty($report->start_balance)) {
            $data['start_balance'] = $before_balance;
            $data['end_balance'] = $transaction->after_balance;
            $report->setPrimaryKeyQuery()->update($data);
        } else {
            $data['end_balance'] = $transaction->after_balance;
            $report->setPrimaryKeyQuery()->update($data);
        }


    }
}
