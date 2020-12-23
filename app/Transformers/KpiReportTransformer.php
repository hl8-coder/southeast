<?php


namespace App\Transformers;


use App\Models\KpiReport;

/**
 * @OA\Schema(
 *   schema="KpiReport",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="date", type="string", description="日期"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="total_deposit", type="string", description="Deposit"),
 *   @OA\Property(property="total_withdrawal", type="integer", description="Withdrawal"),
 *   @OA\Property(property="net_profit", type="string", description="Net Profit"),
 *   @OA\Property(property="total_new_members", type="string", description="New Member"),
 *   @OA\Property(property="total_active_members", type="string", description="Active User"),
 *   @OA\Property(property="total_login_members", type="string", description="Login User"),
 *   @OA\Property(property="total_deposit_members", type="string", description="Deposit User"),
 *   @OA\Property(property="total_withdrawal_members", type="string", description="Withdraw User"),
 *   @OA\Property(property="total_count_deposit", type="string", description="Deposit Record"),
 *   @OA\Property(property="total_count_withdrawal", type="string", description="Withdrawal Record"),
 *   @OA\Property(property="total_turnover", type="string", description="Total Turnover"),
 *   @OA\Property(property="total_payout", type="string", description="Total Payout"),
 *   @OA\Property(property="total_rebate", type="string", description="Member Rebate"),
 *   @OA\Property(property="total_adjustment", type="string", description="Member Adjustment"),
 *   @OA\Property(property="total_promotion_cost", type="string", description="Promotion Cost"),
 *   @OA\Property(property="total_promotion_cost_by_code", type="string", description="Promotion Cost(Code)"),
 *   @OA\Property(property="total_bank_fee", type="string", description="Bank Fees"),
 *   @OA\Property(property="gp", type="string", description="GP"),
 *   @OA\Property(property="gp_after_rebate", type="string", description="GPAR"),
 *   @OA\Property(property="gp_percent", type="string", description="GP(%)"),
 *   @OA\Property(property="gp_after_rebate_percent", type="string", description="GPAR(%)"),
 * )
 */
class KpiReportTransformer extends Transformer
{
    public function transform(KpiReport $kpi)
    {
        $gp                      = $kpi->total_turnover - $kpi->total_payout;
        $gp_after_rebate         = $kpi->total_turnover - $kpi->total_payout - $kpi->total_rebate;
        $gp_percent              = $kpi->total_turnover != 0 ? ($gp / $kpi->total_turnover) * 100 : 0;
        $gp_after_rebate_percent = $kpi->total_turnover != 0 ? ($gp_after_rebate / $kpi->total_turnover) * 100 : 0;
        return [
            'id'                           => $kpi->id,
            'date'                         => $kpi->date,
            'currency'                     => $kpi->currency,
            'total_deposit'                => thousands_number($kpi->total_deposit),
            'total_withdrawal'             => thousands_number($kpi->total_withdrawal),
            'net_profit'                   => thousands_number($kpi->net_profit),
            'total_new_members'            => thousands_number($kpi->total_new_members, 0),
            'total_active_members'         => thousands_number($kpi->total_active_members, 0),
            'total_login_members'          => thousands_number($kpi->total_login_members, 0),
            'total_deposit_members'        => thousands_number($kpi->total_deposit_members, 0), // 8
            'total_withdrawal_members'     => thousands_number($kpi->total_withdrawal_members, 0), // 20
            'total_count_deposit'          => thousands_number($kpi->total_count_deposit), // 21
            'total_count_withdrawal'       => thousands_number($kpi->total_count_withdrawal), // 22
            'total_turnover'               => thousands_number($kpi->total_turnover), // 9
            'total_payout'                 => thousands_number($kpi->total_payout), // 10
            'total_rebate'                 => thousands_number($kpi->total_rebate), // 11
            'total_adjustment'             => thousands_number($kpi->total_adjustment), // 12
            'total_promotion_cost'         => thousands_number($kpi->total_promotion_cost), // 13
            'total_promotion_cost_by_code' => thousands_number($kpi->total_promotion_cost_by_code),
            'total_bank_fee'               => thousands_number($kpi->total_bank_fee), // 14

            'gp'                      => thousands_number($gp),
            'gp_after_rebate'         => thousands_number($gp_after_rebate),
            'gp_percent'              => $gp_percent,
            'gp_after_rebate_percent' => $gp_after_rebate_percent,
            'updated_at'              => $kpi->updated_at
        ];
    }
}
