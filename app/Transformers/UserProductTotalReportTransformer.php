<?php
namespace App\Transformers;

use App\Models\UserProductTotalReport;

/**
 * @OA\Schema(
 *   schema="UserProductTotalReport",
 *   type="object",
 *   @OA\Property(property="open_bet", type="number", description="未开奖投注"),
 *   @OA\Property(property="stake", type="number", description="总投注"),
 *   @OA\Property(property="effective_bet", type="number", description="有效投注"),
 *   @OA\Property(property="effective_profit", type="number", description="有效盈亏"),
 *   @OA\Property(property="profit", type="number", description="总盈亏"),
 *   @OA\Property(property="ratio", type="number", description="比例"),
 * )
 */
class UserProductTotalReportTransformer extends Transformer
{
    public function transform(UserProductTotalReport $report)
    {
        return [
            'open_bet'          => 0,
            'stake'             => thousands_number($report->stake),
            'effective_bet'     => thousands_number($report->effective_bet),
            'effective_profit'  => thousands_number($report->effective_profit),
            'profit'            => thousands_number($report->profit),
            'ratio'             => !empty($report->profit) ? format_number($report->effective_bet * 100/ $report->profit, 2) : 0,
        ];
    }
}