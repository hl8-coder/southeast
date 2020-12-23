<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\UserProductDailyReport;
use App\Transformers\UserProductDailyReportsTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UserProductDailyReportsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_product_daily_reports",
     *      operationId="backstage.user_product_daily_reports",
     *      tags={"Backstage-报表"},
     *      summary="获取会员产品日报表",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserProductDailyReports"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function index(Request $request)
    {

        $ORM = UserProductDailyReport::query();

        if($request->order) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $reports = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )
            ->latest()
            ->paginate($request->per_page);

        $stake = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('stake');
        $effective_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('effective_bet');

        $close_bonus_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('close_bonus_bet');

        $close_cash_back_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('close_cash_back_bet');

        $close_adjustment_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('close_adjustment_bet');

        $close_deposit_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('close_deposit_bet');

        $calculate_rebate_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('calculate_rebate_bet');

        $calculate_reward_bet = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('calculate_reward_bet');

        $profit = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('profit');

        $effective_profit = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('effective_profit');

        $calculate_cash_back_profit = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('calculate_cash_back_profit');

        $rebate = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('rebate');

        $bonus = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('bonus');

        $cash_back = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('cash_back');

        $proxy_bonus = QueryBuilder::for($ORM)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::exact('user_name'),
                Filter::exact('date'),
                Filter::scope('currency')
            )->sum('proxy_bonus');

        $info = [
            'stake' => $stake,
            'effective_bet' => $effective_bet,
            'close_bonus_bet' => $close_bonus_bet,
            'close_cash_back_bet' => $close_cash_back_bet,
            'close_adjustment_bet' => $close_adjustment_bet,
            'close_deposit_bet' => $close_deposit_bet,
            'calculate_rebate_bet' => $calculate_rebate_bet,
            'calculate_reward_bet' => $calculate_reward_bet,
            'profit' => $profit,
            'effective_profit' => $effective_profit,
            'calculate_cash_back_profit' => $calculate_cash_back_profit,
            'rebate' => $rebate,
            'bonus' => $bonus,
            'cash_back' => $cash_back,
            'proxy_bonus'=> $proxy_bonus
        ];


        return $this->response->paginator($reports, new UserProductDailyReportsTransformer())->setMeta($info);
    }
}
