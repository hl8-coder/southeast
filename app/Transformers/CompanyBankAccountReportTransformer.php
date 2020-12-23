<?php
namespace App\Transformers;

use App\Models\CompanyBankAccountReport;

/**
 * @OA\Schema(
 *   schema="CompanyBankAccountReport",
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
class CompanyBankAccountReportTransformer extends Transformer
{
    public function transform(CompanyBankAccountReport $report)
    {
        return [
            'company_bank_account_code' => $report->company_bank_account_code,
            'opening_balance'           => thousands_number($report->opening_balance),
            'ending_balance'            => thousands_number($report->ending_balance),
            'buffer_in'                 => thousands_number($report->buffer_in),
            'buffer_out'                => thousands_number($report->buffer_out),
            'deposit'                   => thousands_number($report->deposit),
            'withdrawal'                => thousands_number($report->withdrawal),
            'adjustment'                => thousands_number($report->adjustment),
            'internal_transfer'         => thousands_number($report->internal_transfer),
        ];
    }
}