<?php
namespace App\Transformers;

use App\Models\CompanyBankAccountReport;
use App\Models\PgAccountReport;

/**
 * @OA\Schema(
 *   schema="PgAccountReport",
 *   type="object",
 *   @OA\Property(property="company_bank_account_code", description="公司银行卡code", type="string"),
 *   @OA\Property(property="opening_balance", description="开始金额", type="number"),
 *   @OA\Property(property="ending_balance", description="结束金额", type="number"),
 *   @OA\Property(property="buffer_in", description="buffer转入", type="number"),
 *   @OA\Property(property="buffer_out", description="buffer转出", type="number"),
 *   @OA\Property(property="deposit", description="充值", type="number"),
 *   @OA\Property(property="withdrawal", description="提现", type="number"),
 *   @OA\Property(property="adjustment", description="调整", type="number"),
 *   @OA\Property(property="internal_transfer", description="内部转账", type="number"),
 * )
 */
class PgAccountReportTransformer extends Transformer
{
    public function transform(PgAccountReport $report)
    {
        return [
            'payment_platform_code'   => $report->payment_platform_code,
            'start_balance'           => thousands_number($report->start_balance),
            'end_balance'             => thousands_number($report->end_balance),
            'deposit'                 => thousands_number($report->deposit),
            'deposit_fee'             => thousands_number($report->deposit_fee),
            'withdraw'                => thousands_number($report->withdraw),
            'withdraw_fee'            => thousands_number($report->withdraw_fee),
            'date'                    => $report->date,
            'created_at'              => convert_time($report->created_at),
        ];
    }
}