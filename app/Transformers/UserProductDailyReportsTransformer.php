<?php


namespace App\Transformers;


use App\Models\UserProductDailyReport;

class UserProductDailyReportsTransformer extends Transformer
{
    /**
     * @OA\Schema(
     *   schema="UserProductDailyReports",
     *   type="object",
     *   @OA\Property(property="id", type="integer", description="ID"),
     *   @OA\Property(property="user_id", type="integer", description="会员ID"),
     *   @OA\Property(property="user_name", type="string", description="会员名"),
     *   @OA\Property(property="currency", type="string", description="币别"),
     *   @OA\Property(property="platform_code", type="string", description="平台code"),
     *   @OA\Property(property="product_code", type="string", description="产品code"),
     *   @OA\Property(property="date", type="string", description="所属日期"),
     *   @OA\Property(property="bet_num", type="number", description="注单数"),
     *   @OA\Property(property="stake", type="number", description="总投注"),
     *   @OA\Property(property="effective_bet", type="number", description="有效流水"),
     *   @OA\Property(property="close_bonus_bet", type="number", description="关闭红利流水"),
     *   @OA\Property(property="close_cash_back_bet", type="number", description="关闭赎返流水"),
     *   @OA\Property(property="close_adjustment_bet", type="number", description="关闭调整流水"),
     *   @OA\Property(property="close_deposit_bet", type="number", description="关闭充值流水"),
     *   @OA\Property(property="calculate_rebate_bet", type="number", description="计算返点流水"),
     *   @OA\Property(property="calculate_reward_bet", type="number", description="计算积分流水"),
     *   @OA\Property(property="profit", type="number", description="总盈亏"),
     *   @OA\Property(property="effective_profit", type="number", description="会员有效盈亏"),
     *   @OA\Property(property="calculate_cash_back_profit", type="number", description="计算赎返盈亏"),
     *   @OA\Property(property="rebate", type="string", description="返点"),
     *   @OA\Property(property="bonus", type="string", description="红利"),
     *   @OA\Property(property="cash_back", type="string", description="赎返"),
     *   @OA\Property(property="proxy_bonus", type="string", description="代理红利"),
     *   @OA\Property(property="total_effective_bet", type="string", description="统计：总的有效投注流水（暂时不用）"),
     *   @OA\Property(property="total_effective_profit", type="string", description="统计：有效盈亏（暂时不用）"),
     *   @OA\Property(property="total_stake", type="string", description="统计：总投注"),
     *   @OA\Property(property="total_profit", type="string", description="统计：总盈亏"),
     * )
     */


    public function transform(UserProductDailyReport $report)
    {
        $data = [
            'id'                         => $report->id,
            'user_id'                    => $report->user_id,
            'user_name'                  => $report->user_name,
            'currency'                   => $report->user->currency,
            'platform_code'              => $report->platform_code,
            'product_code'               => $report->product_code,
            'date'                       => $report->date,
            'bet_num'                    => $report->bet_num,
            'stake'                      => $report->stake,
            'effective_bet'              => $report->effective_bet,
            'close_bonus_bet'            => $report->close_bonus_bet,
            'close_cash_back_bet'        => $report->close_cash_back_bet,
            'close_adjustment_bet'       => $report->close_adjustment_bet,
            'close_deposit_bet'          => $report->close_deposit_bet,
            'calculate_rebate_bet'       => $report->calculate_rebate_bet,
            'calculate_reward_bet'       => $report->calculate_reward_bet,
            'profit'                     => $report->profit,
            'effective_profit'           => $report->effective_profit,
            'calculate_cash_back_profit' => $report->calculate_cash_back_profit,
            'rebate'                     => $report->rebate,
            'bonus'                      => $report->bonus,
            'cash_back'                  => $report->cash_back,
            'proxy_bonus'                => $report->proxy_bonus,
        ];
        switch ($this->type) {
            case 'rm_tool_index':
                $data = [
                    'user_id'                => $report->user_id,
                    'user_name'              => $report->user->name,
                    'currency'               => $report->user->currency,
                    'total_effective_bet'    => thousands_number($report->total_effective_bet),
                    'total_stake'            => thousands_number($report->total_stake),
                    'total_profit'           => thousands_number($report->total_profit),
                    'total_effective_profit' => thousands_number($report->total_effective_profit),
                    'percent'                => format_number($report->percent * 100, 2),
                    'product_code'           => $this->data['product_code'],
                ];
                break;
            case 'rm_tool_detail':
            case 'rm_tool_detail_daily':
                $data = [
                    'user_id'                => $report->user_id,
                    'user_name'              => $report->user->name,
                    'currency'               => $report->user->currency,
                    'total_effective_bet'    => thousands_number($report->total_effective_bet),
                    'total_stake'            => thousands_number($report->total_stake),
                    'total_profit'           => thousands_number($report->total_profit),
                    'total_effective_profit' => thousands_number($report->total_effective_profit),
                    'percent'                => format_number($report->percent * 100, 2),
                    'product_code'           => $report->product_code,
                    'date'                   => $report->date,
                ];
                break;
        }

        return $data;
    }
}
