<?php
namespace App\Transformers;

use App\Models\UserPlatformTotalReport;

/**
 * @OA\Schema(
 *   schema="UserMainWalletTotalReport",
 *   type="object",
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="deposit", type="number", description="总充值"),
 *   @OA\Property(property="withdrawal", type="number", description="总提现"),
 *   @OA\Property(property="transfer_in", type="number", description="总转入"),
 *   @OA\Property(property="transfer_out", type="number", description="总转出"),
 *   @OA\Property(property="adjustment", type="number", description="总调整"),
 *   @OA\Property(property="available_balance", type="number", description="主钱包余额"),
 * )
 */
class UserMainWalletTotalReportTransformer extends Transformer
{
    public function transform(UserPlatformTotalReport $report)
    {
        return [
            'user_name'         => $report->user_name,
            'deposit'           => thousands_number($report->deposit),
            'withdrawal'        => thousands_number($report->withdrawal),
            'transfer_in'       => thousands_number($report->transfer_in),
            'transfer_out'      => thousands_number($report->transfer_out),
            'adjustment'        => thousands_number($report->adjustment),
            'available_balance' => thousands_number($report->available_balance),
        ];
    }
}