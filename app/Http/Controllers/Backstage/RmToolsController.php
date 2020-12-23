<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\UserProductReportExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\RmToolsRequest;
use App\Http\Requests\Backstage\UserRiskRequest;
use App\Models\GamePlatformProduct;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserPlatformDailyReport;
use App\Models\UserPlatformMonthlyReport;
use App\Models\UserProductDailyReport;
use App\Models\UserProductMonthlyReport;
use App\Models\UserRisk;
use App\Repositories\ReportRepository;
use App\Transformers\UserProductDailyReportsTransformer;
use App\Transformers\UserTransformer;
use App\Transformers\UserRiskTransformer;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class RmToolsController extends BackstageController
{
    /**
     * @OA\Get(
     *     path="/backstage/member_data_query",
     *     operationId="backstage.member_data_query.index",
     *     tags={"Backstage-RM"},
     *     summary="member_data_query",
     *     description="member_data_query",
     *     @OA\Parameter(name="name_1", in="query", description="用户名", @OA\Schema(type="string")),
     *     @OA\Parameter(name="name_1", in="query", description="用户名", @OA\Schema(type="string")),
     *     @OA\Parameter(name="name_1", in="query", description="用户名", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[full_name]", in="query", description="全称", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *     @OA\Parameter(name="order", in="query", description="排序，字段+_+排序方式。eg: created_at_desc", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[start_at]", in="query", description="启始日期", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User"),
     *         ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function memberDataQuery(Request $request)
    {
        $ORM = User::query();

        # 設定排序
        if ($request->order) {
            $order    = explode('_', $request->order);
            $sortType = array_pop($order);
            $sortKey  = implode($order, "_");
            $ORM->orderBy(implode('_', $order), $sortType);
        } else {
            $ORM->orderBy('created_at', 'desc');
        }

        $username = [];

        if (!empty($request->name_1)) {
            array_push($username, $request->name_1);
        }
        if (!empty($request->name_2)) {
            array_push($username, $request->name_2);
        }
        if (!empty($request->name_3)) {
            array_push($username, $request->name_3);
        }

        if ($username) {
            $ORM = $ORM->whereIn('name', $username);
        }

        $users = QueryBuilder::for($ORM)
            ->where('is_agent', false)
            ->allowedFilters(
                Filter::scope('full_name'),
                Filter::exact('currency'),
                Filter::scope('end_at'),
                Filter::scope('start_at')
            )
            ->paginate($request->per_page);
        return $this->response->paginator($users, new UserTransformer('member_data_query'));
    }

    /**
     * @OA\Get(
     *     path="/backstage/risk_category_listing?include=userRisks",
     *     operationId="backstage.risk_category_listing.index",
     *     tags={"Backstage-RM"},
     *     summary="risk_category_listing",
     *     description="risk_category_listing",
     *     @OA\Parameter(name="filter[name]", in="query", description="会员名", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[behavior]", in="query", description="行为", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[risk]", in="query", description="风险", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[start_at]", in="query", description="启始日期", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *     @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User"),
     *         ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function riskCategoryListing(Request $request)
    {
        $users = QueryBuilder::for(User::query())
            ->allowedFilters(
                Filter::exact('name'),
                Filter::scope('behaviour'),
                Filter::scope('risk'),
                Filter::exact('currency'),
                Filter::scope('end_at'),
                Filter::scope('start_at')
            )
            ->paginate($request->per_page);
        return $this->response->paginator($users, new UserTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/risk_category_listing/{user_id}",
     *      operationId="backstage.risk_category_listing.show",
     *      tags={"Backstage-RM"},
     *      summary="risk_category_listing",
     *      @OA\Parameter(
     *         name="user_id",
     *         in="path",
     *         description="userID",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserRisk"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function riskCategoryListingShow(User $user, Request $request)
    {
        $risks = $user->userRisks()
            ->orderBy('created_at', 'desc')
            ->paginate($request->per_page);
        return $this->response->paginator($risks, new UserRiskTransformer());
    }

    /**
     * @OA\Post(
     *     path="/backstage/risk_category_listing?include=userRisks",
     *     operationId="backstage.risk_category_listing.index",
     *     tags={"Backstage-RM"},
     *     summary="添加risk_category_listing",
     *     description="添加risk_category_listing",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="behaviour", type="integer", description="行为"),
     *                  @OA\Property(property="user_id", type="integer", description="会员ID"),
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *                  required={"behaviour", "user_id", "remark"}
     *              ),
     *          ),
     *      ),
     *     @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserRisk"),
     *         ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function storeUserRisk(UserRiskRequest $request)
    {
        $data               = remove_null($request->all());
        $data['updated_by'] = $this->user()->name;

        $risk = UserRisk::query()->create($data);

        return $this->response->item($risk, new UserRiskTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/member_data_query/account_summary",
     *      operationId="backstage.account_summary.show",
     *      tags={"Backstage-RM"},
     *      summary="account_summary",
     *      @OA\Parameter(name="user_id", in="query", description="用户ID", @OA\Schema(type="integer")),
     *      @OA\Property(property="data", description="[]", @OA\Items(
     *          @OA\Property(property="date", type="string", description="日期"),
     *          @OA\Property(property="deposit", type="string", description="充值"),
     *          @OA\Property(property="withdrawal", type="string", description="提现"),
     *          @OA\Property(property="adjustment", type="string", description="调额"),
     *          @OA\Property(property="stakeOfSport", type="string", description="体育投注"),
     *          @OA\Property(property="open_bet", type="string", description="未开奖投注"),
     *          @OA\Property(property="effective_bet", type="string", description="真实投注"),
     *          @OA\Property(property="bonus", type="string", description="红利"),
     *          @OA\Property(property="stakeOfOther", type="string", description="其他投注"),
     *      )),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getAccountSummary(Request $request)
    {
        if (!$request->user_id) {
            return $this->response->error('Please input user_id', 422);
        }
        $user_id   = $request->user_id;
        $endDay    = Carbon::today()->addDay(1)->toDateString();
        $startDay  = Carbon::now()->subMonth()->firstOfMonth()->toDateString();
        $periods   = CarbonPeriod::create($startDay, $endDay);
        $sportCode = GamePlatformProduct::query()->where('type', GamePlatformProduct::TYPE_FISH)->pluck('code')->toArray();

        $userPlatformDailyReports = UserPlatformDailyReport::query()
            ->where([
                [
                    'user_id', $user_id,
                ],
                [
                    'platform_code', UserAccount::MAIN_WALLET,
                ],
                [
                    'date', '>=', $startDay,
                ],
                [
                    'date', '<', $endDay,
                ],
            ])
            ->orderBy('date', 'desc')
            ->get();

        $userProductDailyReports = UserProductDailyReport::query()
            ->where([
                [
                    'user_id', $user_id,
                ],
                [
                    'date', '>=', $startDay,
                ],
                [
                    'date', '<', $endDay,
                ],
            ])
            ->orderBy('date', 'desc')
            ->get();
        $totalDeposit            = 0;
        $totalWithdrawal         = 0;
        $totalAdjustment         = 0;
        $totalStakeOfSport       = 0;
        $totalStakeOfOther       = 0;
        $totalOpenBet            = 0;
        $totalEffectiveBet       = 0;
        $totalProfit             = 0;
        $totalBonus              = 0;

        $data = [];
        foreach ($periods as $period) {
            $date              = convert_date($period);
            $info              = $this->getInfo($date, $userPlatformDailyReports, $userProductDailyReports, $sportCode);
            $data[]            = [
                'date'           => $date,
                'deposit'        => thousands_number($info['deposit']),
                'withdrawal'     => thousands_number($info['withdrawal']),
                'adjustment'     => thousands_number($info['adjustment']),
                'stake_of_sport' => thousands_number($info['stake_of_sport']),
                'stake_of_other' => thousands_number($info['stake_of_other']),
                'open_bet'       => thousands_number($info['open_bet']),
                'effective_bet'  => thousands_number($info['effective_bet']),
                'stake_return'   => thousands_number($info['stake_return']),
                'bonus'          => thousands_number($info['bonus']),
            ];
            $totalDeposit      += $info['deposit'];
            $totalWithdrawal   += $info['withdrawal'];
            $totalAdjustment   += $info['adjustment'];
            $totalStakeOfSport += $info['stake_of_sport'];
            $totalStakeOfOther += $info['stake_of_other'];
            $totalOpenBet      += $info['open_bet'];
            $totalEffectiveBet += $info['effective_bet'];
            $totalProfit       += $info['stake_return'];
            $totalBonus        += $info['bonus'];
        }
        rsort($data);

        $total = [
            'date'           => 'Total',
            'deposit'        => thousands_number($totalDeposit),
            'withdrawal'     => thousands_number($totalWithdrawal),
            'adjustment'     => thousands_number($totalAdjustment),
            'stake_of_sport' => thousands_number($totalStakeOfSport),
            'stake_of_other' => thousands_number($totalStakeOfOther),
            'open_bet'       => thousands_number($totalOpenBet),
            'effective_bet'  => thousands_number($totalEffectiveBet),
            'stake_return'   => thousands_number($totalProfit),
            'bonus'          => thousands_number($totalBonus),
        ];
        array_push($data, $total);
        return $this->response->array($data);
    }

    /**
     * @OA\Get(
     *      path="/backstage/member_data_query/account_summary_by_month",
     *      operationId="backstage.account_summary_by_month.show",
     *      tags={"Backstage-RM"},
     *      summary="account_summary",
     *      @OA\Parameter(name="user_id", in="query", description="用户ID", @OA\Schema(type="integer")),
     *      @OA\Property(property="data", description="[]", @OA\Items(
     *          @OA\Property(property="date", type="string", description="日期"),
     *          @OA\Property(property="deposit", type="string", description="充值"),
     *          @OA\Property(property="withdrawal", type="string", description="提现"),
     *          @OA\Property(property="adjustment", type="string", description="调额"),
     *          @OA\Property(property="stakeOfSport", type="string", description="体育投注"),
     *          @OA\Property(property="open_bet", type="string", description="未开奖投注"),
     *          @OA\Property(property="effective_bet", type="string", description="真实投注"),
     *          @OA\Property(property="bonus", type="string", description="红利"),
     *          @OA\Property(property="stakeOfOther", type="string", description="其他投注"),
     *      )),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function accountSummaryByMonth(Request $request)
    {
        if (!$request->user_id) {
            return $this->response->error('Please input user_id', 422);
        }

        $user_id   = $request->user_id;
        $start     = Carbon::now()->addYears(-1)->firstOfYear()->toDateString();
        $sportCode = GamePlatformProduct::query()->where('type', GamePlatformProduct::TYPE_FISH)->pluck('code')->toArray();

        $userPlatformMonthlyReports = UserPlatformMonthlyReport::query()
            ->where([
                [
                    'user_id', $user_id,
                ],
                [
                    'platform_code', UserAccount::MAIN_WALLET,
                ],
                [
                    'date', '>=', $start,
                ],
                [
                    'date', '<', now(),
                ],
            ])
            ->orderBy('date', 'desc')
            ->get();

        $userProductMonthlyReports = UserProductMonthlyReport::query()
            ->where([
                [
                    'user_id', $user_id,
                ],
                [
                    'date', '>=', $start,
                ],
                [
                    'date', '<', now(),
                ],
            ])
            ->orderBy('date', 'desc')
            ->get();

        $data                 = [];
        $totalDeposit         = 0;
        $totalWithdrawal      = 0;
        $totalAdjustment      = 0;
        $totalStakeOfSport    = 0;
        $totalStakeOfOther    = 0;
        $totalOpenBet         = 0;
        $totalEffectiveBet    = 0;
        $totalProfit          = 0;
        $totalBonus           = 0;
        $lastYearDeposit      = 0;
        $lastYearWithdrawal   = 0;
        $lastYearAdjustment   = 0;
        $lastYearStakeOfSport = 0;
        $lastYearStakeOfOther = 0;
        $lastYearOpenBet      = 0;
        $lastYearEffectiveBet = 0;
        $lastYearProfit       = 0;
        $lastYearBonus        = 0;
        for ($i = 1; $i <= date('m'); $i++) {
            $month        = $i < 10 ? '0' . $i : $i;
            $date         = date('Y') . '-' . $month;
            $deposit      = 0;
            $withdrawal   = 0;
            $adjustment   = 0;
            $stakeOfSport = 0;
            $stakeOfOther = 0;
            $openBet      = 0;
            $profit       = 0;
            $effectiveBet = 0;
            $bonus        = 0;
            foreach ($userPlatformMonthlyReports as $platformReport) {
                if ($platformReport->date === $date) {
                    $deposit    = $platformReport->deposit;
                    $withdrawal = $platformReport->withdrawal;
                    $adjustment = $platformReport->adjustment;
                }

                if (strstr($platformReport->date, date("Y", strtotime("-1 year")))) {
                    $lastYearDeposit    += $platformReport->deposit;
                    $lastYearWithdrawal += $platformReport->withdrawal;
                    $lastYearAdjustment += $platformReport->adjustment;
                }
            }
            foreach ($userProductMonthlyReports as $productReport) {
                if ($productReport->date === $date) {
                    $effectiveBet = $productReport->effective_bet;
                    $bonus        = $productReport->bonus;
                    $profit       = $productReport->profit;

                    if (in_array($productReport->product_code, $sportCode)) {
                        $stakeOfSport = $productReport->stake;
                        $openBet      = $productReport->open_bet;
                    } else {
                        $stakeOfOther = $productReport->stake;
                    }
                }

                if (strstr($platformReport->date, date("Y", strtotime("-1 year")))) {
                    $lastYearEffectiveBet += $platformReport->effective_bet;
                    $lastYearBonus        += $platformReport->bonus;
                    $lastYearProfit       += $platformReport->profit;
                    if (in_array($productReport->product_code, $sportCode)) {
                        $lastYearStakeOfSport += $productReport->stake;
                        $lastYearOpenBet      += $productReport->open_bet;
                    } else {
                        $lastYearStakeOfOther += $productReport->stake;
                    }
                }
            }
            $data[]            = [
                'date'           => $date,
                'deposit'        => thousands_number($deposit),
                'withdrawal'     => thousands_number($withdrawal),
                'adjustment'     => thousands_number($adjustment),
                'stake_of_sport' => thousands_number($stakeOfSport),
                'stake_of_other' => thousands_number($stakeOfOther),
                'open_bet'       => thousands_number($openBet),
                'effective_bet'  => thousands_number($effectiveBet),
                'stake_return'   => thousands_number($profit),
                'bonus'          => thousands_number($bonus),
            ];
            $totalDeposit      += $deposit;
            $totalWithdrawal   += $withdrawal;
            $totalAdjustment   += $adjustment;
            $totalStakeOfSport += $stakeOfSport;
            $totalStakeOfOther += $stakeOfOther;
            $totalOpenBet      += $openBet;
            $totalEffectiveBet += $effectiveBet;
            $totalProfit       += $profit;
            $totalBonus        += $bonus;
        }

        rsort($data);

        $total = [
            'date'           => date('Y'),
            'deposit'        => thousands_number($totalDeposit),
            'withdrawal'     => thousands_number($totalWithdrawal),
            'adjustment'     => thousands_number($totalAdjustment),
            'stake_of_sport' => thousands_number($totalStakeOfSport),
            'stake_of_other' => thousands_number($totalStakeOfOther),
            'open_bet'       => thousands_number($totalOpenBet),
            'effective_bet'  => thousands_number($totalEffectiveBet),
            'stake_return'   => thousands_number($totalProfit),
            'bonus'          => thousands_number($totalBonus),
        ];
        array_push($data, $total);
        $lastYear = [
            'date'           => date("Y", strtotime("-1 year")),
            'deposit'        => thousands_number($lastYearDeposit),
            'withdrawal'     => thousands_number($lastYearWithdrawal),
            'adjustment'     => thousands_number($lastYearAdjustment),
            'stake_of_sport' => thousands_number($lastYearStakeOfSport),
            'stake_of_other' => thousands_number($lastYearStakeOfOther),
            'open_bet'       => thousands_number($lastYearOpenBet),
            'effective_bet'  => thousands_number($lastYearEffectiveBet),
            'stake_return'   => thousands_number($lastYearProfit),
            'bonus'          => thousands_number($lastYearBonus),
        ];
        array_push($data, $lastYear);
        return $this->response->array($data);
    }

    public function getInfo($date, $platformReports, $productReports, $sportCode)
    {
        $deposit      = 0;
        $withdrawal   = 0;
        $adjustment   = 0;
        $stakeOfSport = 0;
        $stakeOfOther = 0;
        $openBet      = 0;
        $profit       = 0;
        $effectiveBet = 0;
        $bonus        = 0;
        foreach ($platformReports as $platformReport) {
            if ($platformReport->date === $date) {
                $deposit    = $platformReport->deposit;
                $withdrawal = $platformReport->withdrawal;
                $adjustment = $platformReport->adjustment;
            }
        }
        foreach ($productReports as $productReport) {
            if ($productReport->date === $date) {
                $effectiveBet = $productReport->effective_bet;
                $bonus        = $productReport->bonus;
                $profit       = $productReport->profit;

                if (in_array($productReport->product_code, $sportCode)) {
                    $stakeOfSport = $productReport->stake;
                    $openBet      = $productReport->open_bet;
                } else {
                    $stakeOfOther = $productReport->stake;
                }
            }
        }
        return [
            'date'           => $date,
            'deposit'        => $deposit,
            'withdrawal'     => $withdrawal,
            'adjustment'     => $adjustment,
            'stake_of_sport' => $stakeOfSport,
            'stake_of_other' => $stakeOfOther,
            'open_bet'       => $openBet,
            'effective_bet'  => $effectiveBet,
            'stake_return'   => $profit,
            'bonus'          => $bonus,
        ];
    }

    # 根据用户和产品 和时间在周期，统计用户输赢 include=user

    /**
     * @OA\Get(
     *      path="/backstage/rmtools/user_product_report?include=user",
     *      operationId="backstage.rmtools.user_product_report",
     *      tags={"Backstage-RM"},
     *      summary="获取用户基本风控信息",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="用户名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", required=false, description="币别", @OA\Schema(type="string")),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserProductDailyReports"),
     *         ),
     *      ),
     *     security={
     *          {"bearer": {}},
     *     }
     *  )
     */
    public function userProductReport(RmToolsRequest $request)
    {
        // 条件 user_name, product_code, 带排序
        $ORM = UserProductDailyReport::query();

        if($request->order) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $rawString = "user_id, sum(`effective_bet`) as total_effective_bet, 
        sum(`stake`) as total_stake, sum(`profit`) as total_profit, sum(`effective_profit`) as total_effective_profit, if(sum(`stake`)=0,  0, sum(`profit`)/sum(`stake`)) as percent";
        $data      = QueryBuilder::for($ORM)->select(DB::raw($rawString))
                    ->allowedFilters([
                        Filter::exact('user_name'),
                        Filter::exact('product_code'),
                        Filter::scope('start_at'),
                        Filter::scope('end_at'),
                        Filter::scope('currency')
                    ])
                    ->groupBy(['user_id'])
                    ->paginate($request->per_page);
        $params    = ['product_code' => $request->input('filter.product_code', null)];
        return $this->response()->paginator($data, new UserProductDailyReportsTransformer('rm_tool_index', $params));
    }


    /**
     * @OA\Get(
     *      path="/backstage/rmtools/user_product_report/export",
     *      operationId="backstage.rmtools.user_product_report.export",
     *      tags={"Backstage-RM"},
     *      summary="下载用户基本风控信息",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="用户名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", required=false, description="币别", @OA\Schema(type="string")),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(
     *         response=200,
     *         description="请求成功",
     *     @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserProductReportExport"),
     *         ),
     *      ),
     *     security={
     *          {"bearer": {}},
     *     }
     *  )
     */
    public function userProductReportExport(Request $request)
    {
        $ORM = UserProductDailyReport::query();

        if($request->order) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $rawString = "user_id, sum(`effective_bet`) as total_effective_bet, 
        sum(`stake`) as total_stake, sum(`profit`) as total_profit, sum(`effective_profit`) as total_effective_profit, if(sum(`stake`)=0, 0, sum(`profit`)/sum(`stake`)) as percent";
        $data      = QueryBuilder::for(UserProductDailyReport::class)->select(DB::raw($rawString))
            ->allowedFilters(Filter::exact('user_name'), Filter::exact('product_code'),
                Filter::scope('start_at'), Filter::scope('end_at'), Filter::scope('currency'))
            ->groupBy(['user_id'])
            ->get();
        $params    = ['product_code' => $request->input('filter.product_code', null)];
        return Excel::download(new UserProductReportExport($data, $params), 'report.xlsx');
    }


    /**
     * @OA\Get(
     *      path="/backstage/rmtools/user_product_report_detail?include=user",
     *      operationId="backstage.rmtools.user_product_report_detail",
     *      tags={"Backstage-RM"},
     *      summary="获取用户产品信息详情",
     *      @OA\Parameter(name="filter[user_name]", in="query", required=true,description="用户名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", required=false, description="币别", @OA\Schema(type="string")),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}},
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserProductDailyReports"),
     *         ),
     *      ),
     *  )
     */
    public function userProductReportDetail(Request $request)
    {
        $ORM = UserProductDailyReport::query();

        if($request->order) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $rawString = "user_id, sum(`effective_bet`) as total_effective_bet, product_code,
        sum(`stake`) as total_stake, sum(`profit`) as total_profit, sum(`effective_profit`) as total_effective_profit, if(sum(`stake`)=0, 0, sum(`profit`)/sum(`stake`)) as percent";
        $data      = QueryBuilder::for($ORM)
            ->select(DB::raw($rawString))
            ->allowedFilters([
                Filter::exact('user_name'),
                Filter::exact('product_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('currency')
            ])
            ->groupBy(['product_code', 'user_id'])
            ->paginate($request->per_page);

        $dataCollection               = collect($data->items());
        $total['total_stake']         = thousands_number($dataCollection->sum('total_stake'));
        $total['total_profit']        = thousands_number($dataCollection->sum('total_profit'));
        $total['total_effective_bet'] = thousands_number($dataCollection->sum('total_effective_bet'));

        return $this->response()->paginator($data, new UserProductDailyReportsTransformer('rm_tool_detail'))->setMeta($total);
    }


    /**
     * @OA\Get(
     *      path="/backstage/rmtools/user_product_report_detail_daily?include=user",
     *      operationId="backstage.rmtools.user_product_report_detail_daily",
     *      tags={"Backstage-RM"},
     *      summary="获取用户产品信息每天详情",
     *      @OA\Parameter(name="filter[user_name]", in="query", required=true,description="用户名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", required=true, description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", required=false, description="币别", @OA\Schema(type="string")),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}},
     *     },
     *     @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/UserProductDailyReports"),
     *         ),
     *      ),
     *  )
     */
    public function userProductReportDetailDaily(Request $request)
    {
        $ORM = UserProductDailyReport::query();

        if($request->order) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $rawString = "user_id, sum(`effective_bet`) as total_effective_bet, product_code, date,
        sum(`stake`) as total_stake, sum(`profit`) as total_profit, sum(`effective_profit`) as total_effective_profit, if(sum(`stake`)=0, 0, sum(`profit`)/sum(`stake`)) as percent";
        $data      = QueryBuilder::for($ORM)
            ->select(DB::raw($rawString))
            ->allowedFilters([
                Filter::exact('user_name'),
                Filter::exact('product_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('currency')
            ])
            ->groupBy(['date', 'user_id', 'product_code'])
            ->paginate($request->per_page);

        $dataCollection               = collect($data->items());
        $total['total_stake']         = thousands_number($dataCollection->sum('total_stake'));
        $total['total_profit']        = thousands_number($dataCollection->sum('total_profit'));
        $total['total_effective_bet'] = thousands_number($dataCollection->sum('total_effective_bet'));
        return $this->response()->paginator($data, new UserProductDailyReportsTransformer('rm_tool_detail_daily'))->setMeta($total);
    }


    /**
     * @OA\Get(
     *      path="/backstage/rmtools/user_risk_summary?include=vip,info",
     *      operationId="backstage.rmtools.user_risk_summary",
     *      tags={"Backstage-RM"},
     *      summary="获取用户列表风控信息",
     *      @OA\Parameter(name="filter[name]", in="query", required=false, description="用户名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", required=false,  description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", required=false,  description="风控组", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[payment_group_id]", in="query", required=false,  description="支付组", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[full_name]", in="query", required=false,  description="全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[email]", in="query", required=false,  description="邮箱", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", required=false,  description="电话号码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_url]", in="query", required=false,  description="注册地址", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_ip]", in="query", required=false,  description="注册IP", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[deposit]", in="query", required=false,  description="是否有充值", @OA\Schema(type="boolean")),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      },
     *     @OA\Response(
     *         response=200,
     *         description="请求成功",
     *         @OA\JsonContent(
     *             type="array",
     *             @OA\Items(ref="#/components/schemas/User"),
     *         ),
     *      ),
     *  )
     */
    public function userRiskSummary(Request $request)
    {
        // 搜索条件：用户名，币别，风控组别，支付组别，全名，邮箱，联系号码，注册IP，注册url
        $summary = QueryBuilder::for(User::class)
            // 移除代理
            ->isUser()
            ->allowedFilters([
                'name',
                Filter::exact('currency'),
                Filter::exact('risk_group_id'),
                Filter::exact('payment_group_id'),
                Filter::scope('full_name'),
                Filter::scope('email'),
                Filter::scope('phone'),
                Filter::scope('register_url'),
                Filter::scope('register_ip'),
                Filter::scope('deposit'),
            ])
            ->paginate($request->per_page);

        $userIds = collect($summary->items())->pluck('id')->toArray();

        $riskAccountSummary = UserPlatformDailyReport::query()
            ->select(DB::raw('user_id, sum(deposit) as total_deposit, sum(withdrawal) as total_withdrawal'))
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->get();

        $riskReportSummary = UserProductDailyReport::query()
            ->select(DB::raw('user_id, sum(stake) as total_stake, sum(profit) as total_profit, sum(bonus) as total_bonus,
            sum(bet_num) as total_bet_num, sum(`effective_bet`) as total_effective_bet'))
            ->whereIn('user_id', $userIds)
            ->groupBy('user_id')
            ->get();

        return $this->response->paginator($summary, new UserTransformer('risk_summary', ['account_summary' => $riskAccountSummary, 'report_summary' => $riskReportSummary]));
    }
}
