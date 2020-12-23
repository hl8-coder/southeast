<?php

namespace App\Exports;

use App\Models\GameBetDetail;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Schema(
 *   schema="BetHistoryExport",
 *   type="object",
 *   @OA\Property(property="order_id", type="string", description="投注ID", format="date-time"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="bet_at", type="string", description="投注时间"),
 *   @OA\Property(property="odds", type="string", description="赔率"),
 *   @OA\Property(property="user_stake", type="integer", description="总投注额"),
 *   @OA\Property(property="status", type="string", description="投注状态"),
 *   @OA\Property(property="user_profit", type="integer", description="盈亏总额"),
 *   @OA\Property(property="ticket_status", type="string", description="盈亏状态"),
 *   @OA\Property(property="product_code", type="striing", description="产品code"),
 *   @OA\Property(property="game_name", type="string", description="游戏名称"),
 * )
 */
class BetHistoryExport implements WithMapping, ShouldAutoSize, FromCollection, WithHeadings
{
    use \Maatwebsite\Excel\Concerns\Exportable, SerializesModels;

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $filter = $this->request->input('filter');

        $where = array();

        if (!empty($filter) && !empty($filter['product_code'])) {
            $where[] = array('product_code', '=', $filter['product_code']);
        }

        if (!empty($filter) && !empty($filter['user_currency'])) {
            $where[] = array('user_currency','=',$filter['user_currency']);
        }

        if (!empty($filter) && !empty($filter['platform_status'])) {
            $where[] = array('platform_status', '=', $filter['platform_status']);
        }

        if (!empty($filter) && !empty($filter['user_name'])) {
            $where[] = array('user_name', '=', $filter['user_name']);
        }

        if (!empty($filter) && !empty($filter['start_at'])) {
            $where[] = array('bet_at', '>=', $filter['start_at']);
        }

        if (!empty($filter) && !empty($filter['end_at'])) {
            $where[] = array('bet_at', '<=', $filter['end_at']);
        }

        if (!empty($filter) && !empty($filter['game_name'])) {
            $where[] = array('game_name', '=', $filter['game_name']);
        }

        if (!empty($filter) && !empty($filter['payout_start_at'])) {
            $where[] = array('payout_at', '>=', $filter['payout_start_at']);
        }

        if (!empty($filter) && !empty($filter['payout_end_at'])) {
            $where[] = array('payout_at', '<=', $filter['payout_end_at']);
        }

        $query = new GameBetDetail();

        $attributes = ['id','bet_at','user_profit','order_id','user_name','product_code','game_name','odds','user_stake','bet','platform_status','status'];
        return $query->getAll($where,'bet_at DESC',$attributes,10000,'object');
    }

    public function map($row): array
    {
        $ticketStatus = 'Loss';
        $bet = $row->bet;
        $status       = GameBetDetail::$statuses;
        if ($row->user_profit > 0) {
            $ticketStatus = 'Won';
        } elseif ($row->user_profit == 0) {
            $ticketStatus = 'Draw';
            $bet = 0;
        }
        return [
            'order_id'        => $row->order_id,
            'user_name'       => $row->user_name,
            'product_code'    => $row->product_code,
            'game_name'       => $row->game_name,
            'bet_at'          => $row->bet_at,
            'odds'            => $row->odds,
            'user_stake'      => $row->user_stake,
            'real_bet'        => $bet,
            'platform_status' => transfer_show_value($row->platform_status, GameBetDetail::$platformStatuses),
            'user_profit'     => $row->user_profit,
            'ticket_status'   => $ticketStatus,
            'it_status'       => transfer_show_value($row->status, GameBetDetail::$statuses),
        ];
    }

    public function headings(): array
    {
        return ['Bet ID', 'Member Code', 'Product Code', 'Game Name', 'Date', 'Odds', 'Stake', 'Real Bet', 'Status', 'Won Lose Amt', 'Ticket Status', 'IT Status'];
    }

}
