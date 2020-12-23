<?php

namespace App\Http\Controllers\Backstage;

use Illuminate\Http\Request;
use App\Models\GameBetDetail;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use App\Exports\BetHistoryExport;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Transformers\GameBetDetailTransformer;

class GameBetDetailsController extends BackstageController
{
    public function notIndex(Request $request)
    {

        $input = remove_null($request->filter);
        if (empty($input)) {
            $message = 'You need to input at least one filter condition.';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        $fields = [
            'id',
            'order_id',
            'user_name',
            'bet_at',
            'user_stake',
            'platform_status',
            'user_profit',
            'status',
            'odds',
            'payout_at',
            'stake',
            'game_name',
            'product_code',
            'bet'
        ];

        $allowedFilters = [
            Filter::exact('user_name'),
            Filter::exact('game_name'),
            Filter::exact('product_code'),
            Filter::exact('status'),
            Filter::exact('platform_status'),
            Filter::exact('user_currency'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('payout_start_at'),
            Filter::scope('payout_end_at'),
        ];

        $query = QueryBuilder::for(GameBetDetail::class)
            ->select(['id'])
            ->allowedFilters($allowedFilters)
            ->orderByDesc('bet_at');

        $details = GameBetDetail::query()->select($fields)->whereIn('id', $query)->paginate($request->per_page);

//        $sumInfo = GameBetDetail::query()->whereIn('id', $query)
//            ->first([
//                DB::raw("SUM(user_stake) as user_stake"),
//                DB::raw("SUM(bet) as bet"),
//                DB::raw("SUM(user_profit) as user_profit"),
//            ]);

//        $info = [
//            ['key' => 'stake', 'value' => thousands_number($sumInfo->user_stake)],
//            ['key' => 'real bet', 'value' => thousands_number($sumInfo->bet)],
//            ['key' => 'won lose amt', 'value' => thousands_number($sumInfo->user_profit)],
//        ];

        $info = [];

        return $this->response->paginator($details, new GameBetDetailTransformer())->setMeta(['info' => $info]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/game_bet_details",
     *      operationId="backstage.game_bet_details.index",
     *      tags={"Backstage-游戏"},
     *      summary="获取游戏投注列表",
     *      @OA\Parameter(name="filter[product_code]", in="query", description="游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[payout_start_at]", in="query", description="结算查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[payout_end_at]", in="query", description="结算查询结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GameBetDetail"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $where = array();

        # 页大小.
        $pageSize = $request->input('per_page');

        # 过滤条件.
        $filter = $request->input('filter');

        if (!empty($filter) && !empty($filter['user_name'])) {
            $where[] = array('user_name', '=', $filter['user_name']);
        }

        if (!empty($filter) && !empty($filter['game_name'])) {
            $where[] = array('game_name', '=', $filter['game_name']);
        }

        if (!empty($filter) && !empty($filter['product_code'])) {
            $where[] = array('product_code', '=', $filter['product_code']);
        }

        if (!empty($filter) && !empty($filter['status'])) {
            $where[] = array('status', '=', $filter['status']);
        }

        if (!empty($filter) && !empty($filter['platform_status'])) {
            $where[] = array('platform_status', '=', $filter['platform_status']);
        }

        if (!empty($filter) && !empty($filter['user_currency'])) {
            $where[] = array('user_currency','=',$filter['user_currency']);
        }

        if (!empty($filter) && !empty($filter['start_at'])) {
            $where[] = array('bet_at', '>=', $filter['start_at']);
        }

        if (!empty($filter) && !empty($filter['end_at'])) {
            $where[] = array('bet_at', '<=', $filter['end_at']);
        }

        if (!empty($filter) && !empty($filter['payout_start_at'])) {
            $where[] = array('payout_at', '>=', $filter['payout_start_at']);
        }

        if (!empty($filter) && !empty($filter['payout_end_at'])) {
            $where[] = array('payout_at', '<=', $filter['payout_end_at']);
        }

        if (empty($where)) {
            $message = 'You need to input at least one filter condition.';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }

        $query = new GameBetDetail();

        $attributes = ['id','order_id','user_name','bet_at','user_stake','platform_status','user_profit','status','odds','payout_at','stake','game_name','product_code', 'bet'];

        $details = $query->getListWithPaginate($where,$pageSize,'bet_at DESC',$attributes);

        $totalStake = $query->getSum('user_stake', $where);

        $totalRealBet = $query->getSum('bet', $where);

        $winLoseAmt = $query->getSum('user_profit', $where);

//        $totalMember = $query->getUniqueMemberNum($where);

        $info = [
            ['key' => 'stake', 'value' => thousands_number($totalStake)],
            ['key' => 'real bet', 'value' => thousands_number($totalRealBet)],
            ['key' => 'won lose amt', 'value' => thousands_number($winLoseAmt)],
//            ['key' => 'total_data', 'value' => thousands_number($details->total(), 0)],
//            ['key' => 'unique_member', 'value' => thousands_number($totalMember, 0)],
        ];
        return $this->response->paginator($details, new GameBetDetailTransformer())->setMeta(['info' => $info]);
    }


    /**
     * @OA\Get(
     *      path="/backstage/game_bet_details/excel",
     *      operationId="backstage.game_bet_details.export_excel",
     *      tags={"Backstage-游戏"},
     *      summary="下载游戏投注列表",
     *      @OA\Parameter(name="filter[product_code]", in="query", description="游戏产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *              @OA\Items(ref="#/components/schemas/BetHistoryExport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function exportExcel(Request $request)
    {
        set_time_limit(300);
        $input = remove_null($request->filter);
        if (empty($input)) {
            $message = 'You need to input a product code at least.';
            return $this->response->error($message, 422)->withHeader(['X-header-message' => $message]);
        }
        return Excel::download(new BetHistoryExport($request), 'history.xlsx');
    }
}
