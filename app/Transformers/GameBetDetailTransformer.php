<?php

namespace App\Transformers;

use App\Models\GameBetDetail;

/**
 * @OA\Schema(
 *   schema="GameBetDetail",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="user_name", type="string", description="会员名字"),
 *   @OA\Property(property="bet_at", type="string", description="投注时间", format="date-time"),
 *   @OA\Property(property="order_id", type="string", description="注单id"),
 *   @OA\Property(property="bet_info", type="string", description="投注详情"),
 *   @OA\Property(property="user_stake", type="number", description="总投注额转化后数据"),
 *   @OA\Property(property="user_bet", type="number", description="有效投注额转化后数据"),
 *   @OA\Property(property="win_info", type="string", description="开奖信息"),
 *   @OA\Property(property="platform_status", type="integer", description="注单状态"),
 *   @OA\Property(property="display_platform_status", type="string", description="注单状态显示"),
 *   @OA\Property(property="ticket_status", type="string", description="中奖状态"),
 *   @OA\Property(property="user_profit", type="number", description="会员盈亏"),
 *   @OA\Property(property="platform_code", type="string", description="游戏平台code"),
 *   @OA\Property(property="product_code", type="string", description="游戏产品code"),
 *   @OA\Property(property="platform_currency", type="string", description="平台币别"),
 *   @OA\Property(property="game_type", type="number", description="游戏类型"),
 *   @OA\Property(property="game_code", type="string", description="游戏编码"),
 *   @OA\Property(property="game_name", type="string", description="游戏名称"),
 *   @OA\Property(property="user_id", type="integer", description="会员ID"),
 *   @OA\Property(property="issue", type="string", description="奖期"),
 *   @OA\Property(property="stake", type="number", description="总投注额转化后数据"),
 *   @OA\Property(property="bet", type="number", description="总投注额原始数据"),
 *   @OA\Property(property="prize", type="number", description="中奖奖金"),
 *   @OA\Property(property="profit", type="number", description="会员盈亏"),
 *   @OA\Property(property="odds", type="string", description="赔率"),
 *   @OA\Property(property="after_balance", type="number", description="余额"),
 *   @OA\Property(property="payout_at", type="string", description="结算时间"),
 *   @OA\Property(property="user_currency", type="string", description="会员币别"),
 *   @OA\Property(property="user_prize", type="number", description="会员中奖奖金"),
 *   @OA\Property(property="platform_profit", type="number", description="平台盈亏"),
 *   @OA\Property(property="multiple", type="number", description="倍数"),
 *   @OA\Property(property="money_unit", type="string", description="资金单位"),
 *   @OA\Property(property="win_result", type="string", description="开奖结果"),
 *   @OA\Property(property="user_prize_group", type="string", description="会员当下奖金组"),
 *   @OA\Property(property="available_bet", type="number", description="可用投注"),
 *   @OA\Property(property="available_profit", type="number", description="可用盈亏"),
 *   @OA\Property(property="available_rebate_bet", type="number", description="可用于返点的"),
 *   @OA\Property(property="jpc", type="number", description="老虎机奖池贡献值"),
 *   @OA\Property(property="jpw", type="number", description="老虎机奖池中奖值"),
 *   @OA\Property(property="jpw_jpc", type="number", description="老虎机奖池中奖额玩家贡献部分"),
 *   @OA\Property(property="is_close", type="number", description="是否关闭"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display", type="string", description="状态显示"),
 *   @OA\Property(property="finished_at", type="string", description="处理完成时间"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="trace_logs", type="string", description="追踪日志"),
 * )
 */
class GameBetDetailTransformer extends Transformer
{
    public function transform(GameBetDetail $detail)
    {
        $ticketStatus = 'Loss';
        $bet = $detail->bet;
        $user_profit = $detail->user_profit;

        if ($detail->user_profit > 0) {
            $ticketStatus = 'Won';
        }

        if (in_array($detail->product_code,
                [
                    'ISB_Slot',
                    'PP_Slot',
                    'SP_Slot',
                    'GPI_THLT',
                    'GPI_SODE',
                    'SP_Fish',
                    'N2_Live',
                    'MGS_Slot',
                    'GPI_Live',
                    'GPI_Slot',
                    'GPI_Fish',
                    'GG_Fish',
                    'MGS_Live',
                    'GG_Slot',
                    'S128_Fish',
                    'B46_Sport',
                    'N2_Slot',
                    'EBET_Live',
                    'GPI_P2P',
                    'SA_Live',
                    'GPI_Lottery',
                    'IBC_Sport',
                    'MGS_Fish',
                    'SBO_Sport',
                    'IM_Sport',
                    'IM_ESport',
                    'SS_Slot',
                    'SS_Fish',
                    'PP_Fish',
                    'PT_Slot'
                ]
            ))
            {
                if ($detail->user_profit == 0) {
                    $ticketStatus = 'Draw';
                    $bet = 0;
            }
        }

        $payoutAt = convert_time($detail->payout_at);

        return [
            'id'                      => $detail->id,
            'order_id'                => $detail->order_id,
            'user_name'               => $detail->user_name,
            'bet_at'                  => convert_time($detail->bet_at),
//            'bet_info'                => $detail->bet_info,
            'user_stake'              => thousands_number($detail->user_stake),
            'user_bet'                => thousands_number($detail->user_bet),
//            'win_info'                => $detail->win_info,
            'platform_status'         => $detail->platform_status,//
            'display_platform_status' => transfer_show_value($detail->platform_status, GameBetDetail::$platformStatuses),
            'ticket_status'           => $ticketStatus,
            'user_profit'             => thousands_number($user_profit),
//            'trace_logs'              => $detail->trace_logs,
//            'remark'                  => $detail->remark,
//            'finished_at'             => $detail->finished_at,
            'status'                  => $detail->status,
            'display_status'          => transfer_show_value($detail->status, GameBetDetail::$statuses),
//            'is_close'                => $detail->is_close,
//            'jpw_jpc'                 => $detail->jpw_jpc,
//            'jpw'                     => $detail->jpw,
//            'jpc'                     => $detail->jpc,
            'odds'                    => $detail->odds,
//            'available_rebate_bet'    => $detail->available_rebate_bet,
//            'available_profit'        => $detail->available_profit,
//            'available_bet'           => $detail->available_bet,
//            'user_prize_group'        => $detail->user_prize_group,
//            'win_result'              => $detail->win_result,
//            'money_unit'              => $detail->money_unit,
//            'multiple'                => $detail->multiple,
//            'platform_profit'         => $detail->platform_profit,
//            'user_prize'              => $detail->user_prize,
//            'user_currency'           => $detail->user_currency,
            'payout_at'               => $payoutAt == '-0001-11-30 00:00:00' ? '' : $payoutAt,
//            'after_balance'           => $detail->after_balance,
//            'profit'                  => $detail->profit,
//            'prize'                   => $detail->prize,
            'bet'                     => $bet,
            'stake'                   => $detail->stake,
//            'issue'                   => $detail->issue,
//            'user_id'                 => $detail->user_id,
            'game_name'               => $detail->game_name,//
//            'game_code'               => $detail->game_code,
//            'game_type'               => $detail->game_type,
//            'platform_currency'       => $detail->platform_currency,
            'product_code'            => $detail->product_code,//
//            'platform_code'           => $detail->platform_code,
        ];
    }
}
