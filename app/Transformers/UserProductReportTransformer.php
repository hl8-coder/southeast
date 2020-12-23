<?php
namespace App\Transformers;

use App\Models\UserProductDailyReport;

/**
 * @OA\Schema(
 *   schema="UserProductReport",
 *   type="object",
 *   @OA\Property(property="product_code", type="string", description="产品code"),
 *   @OA\Property(property="open_bet", type="number", description="未开奖投注"),
 *   @OA\Property(property="stake", type="number", description="总投注"),
 *   @OA\Property(property="effective_bet", type="number", description="有效投注"),
 *   @OA\Property(property="effective_profit", type="number", description="有效盈亏"),
 *   @OA\Property(property="profit", type="number", description="总盈亏"),
 *   @OA\Property(property="ratio", type="number", description="比例"),
 * )
 */
class UserProductReportTransformer extends Transformer
{
    public function transform(UserProductDailyReport $report)
    {
        return [
            'product_code'      => $report->product_code,
            'open_bet'          => thousands_number($report->open_bet),
            'stake'             => thousands_number($report->stake),
            'effective_bet'     => thousands_number($report->effective_bet),
            'effective_profit'  => thousands_number($report->effective_profit),
            'profit'            => thousands_number($report->profit),
            'ratio'             => !empty($report->profit) ? format_number($report->effective_bet * 100/ $report->profit, 2) : 0,
        ];
    }
}