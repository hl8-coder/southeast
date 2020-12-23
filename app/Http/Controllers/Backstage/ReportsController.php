<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\ReportRequest;
use App\Models\GamePlatformProduct;
use App\Models\User;
use App\Models\UserAccount;
use App\Models\UserBonusPrize;
use App\Models\UserLoginLog;
use App\Models\DepositLog;
use App\Models\Deposit;
use App\Models\UserPlatformTotalReport;
use App\Models\UserProductDailyReport;
use App\Models\UserProductTotalReport;
use App\Repositories\ReportRepository;
use App\Repositories\DepositRepository;
use App\Repositories\UserBonusPrizeRepository;
use App\Repositories\UserRebatePrizeRepository;
use App\Repositories\UserRepository;
use App\Transformers\MembersActivityReportsTransformer;
use App\Transformers\UserLoginLogTransformer;
use App\Transformers\DepositLogTransformer;
use App\Transformers\DepositTransformer;
use App\Transformers\RebateComputationReportTransformer;
use App\Transformers\UserMainWalletTotalReportTransformer;
use App\Transformers\UserProductReportTransformer;
use App\Transformers\UserProductTotalReportTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_platform_total_reports",
     *      operationId="backstage.user_platform_total_reports.index",
     *      tags={"Backstage-报表"},
     *      summary="会员第三方平台报表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称",@OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="登录成功",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="platform_code", description="第三方游戏code", type="string"),
     *              @OA\Property(property="transfer_in", description="充值", type="number"),
     *              @OA\Property(property="transfer_out", description="提现", type="number"),
     *              @OA\Property(property="adjustment_in", description="调整进账", type="number"),
     *              @OA\Property(property="adjustment_out", description="调整出账", type="number"),
     *              @OA\Property(property="profit", description="平台盈亏", type="number"),
     *              @OA\Property(property="balance", description="钱包余额", type="number"),
     *              @OA\Property(property="promotion", description="优惠", type="number"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userPlatformTotalReportIndex(ReportRequest $request)
    {
        set_time_limit(0);

        $reports = ReportRepository::getUserPlatformTotalReport($request->filter['user_name']);

        # 转账 盈亏这里相对于会员 +:会员赢钱 -:会员输钱
        foreach ($reports as &$report) {
            $report['transfer_in']    = thousands_number($report['transfer_in']);
            $report['transfer_out']   = thousands_number($report['transfer_out']);
            $report['adjustment_in']  = thousands_number($report['adjustment_in']);
            $report['adjustment_out'] = thousands_number($report['adjustment_out']);
            $report['profit']         = thousands_number(-1 * $report['profit']);
            $report['promotion']      = thousands_number($report['promotion']);
            $report['balance']        = thousands_number($report['balance']);
        }

        return $this->response->array($reports);
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_product_reports",
     *      operationId="backstage.user_product_reports.index",
     *      tags={"Backstage-报表"},
     *      summary="会员第三方平台产品报表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称",@OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间",@OA\Schema(type="date-time")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间",@OA\Schema(type="date-time")),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserProductReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userProductReportIndex(ReportRequest $request)
    {
        $startAt = !empty($request->filter['start_at']) ? $request->filter['start_at'] : now()->toDateString();
        $endAt   = !empty($request->filter['end_at']) ? $request->filter['end_at'] : now()->toDateString();

        $reports = UserProductDailyReport::query()->where('user_name', $request->filter['user_name'])
            ->where('date', '>=', $startAt)
            ->where('date', '<=', $endAt)
            ->groupBy('product_code')->get([
                                               'product_code',
                                               DB::raw('SUM(open_bet) as open_bet'),
                                               DB::raw('SUM(stake) as stake'),
                                               DB::raw('SUM(effective_bet) as effective_bet'),
                                               DB::raw('SUM(profit) as profit'),
                                               DB::raw('SUM(effective_profit) as effective_profit'),
                                           ]);

        $result = [];

        $totalStake           = 0;
        $totalOpenBet         = 0;
        $totalEffectiveBet    = 0;
        $totalProfit          = 0;
        $totalEffectiveProfit = 0;

        foreach (GamePlatformProduct::getAll() as $product) {

            if ($report = $reports->where('product_code', $product->code)->first()) {
                $totalOpenBet         += $report->open_bet;
                $totalStake           += $report->stake;
                $totalEffectiveBet    += $report->effective_bet;
                $totalProfit          += $report->profit;
                $totalEffectiveProfit += $report->effective_profit;
                $result[]             = $report;
            } else {
                $result[] = new UserProductDailyReport([
                                                           'product_code'     => $product->code,
                                                           'open_bet'         => 0,
                                                           'stake'            => 0,
                                                           'effective_bet'    => 0,
                                                           'profit'           => 0,
                                                           'effective_profit' => 0,
                                                       ]);
            }
        }

        # total
        $result[] = new UserProductDailyReport([
                                                   'product_code'     => 'Total',
                                                   'open_bet'         => $totalOpenBet,
                                                   'stake'            => $totalStake,
                                                   'effective_bet'    => $totalEffectiveBet,
                                                   'profit'           => $totalProfit,
                                                   'effective_profit' => $totalEffectiveProfit,
                                               ]);

        return $this->response->collection(collect($result), new UserProductReportTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_product_total_reports",
     *      operationId="backstage.user_product_total_reports.index",
     *      tags={"Backstage-报表"},
     *      summary="会员第三方平台产品报表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称",@OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserProductTotalReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userProductTotalReportIndex(ReportRequest $request)
    {
        $report = UserProductTotalReport::query()->where('user_name', $request->filter['user_name'])
            ->get([
                      DB::raw('SUM(stake) as stake'),
                      DB::raw('SUM(effective_bet) as effective_bet'),
                      DB::raw('SUM(profit) as profit'),
                      DB::raw('SUM(effective_profit) as effective_profit'),
                  ]);

        return $this->response->collection($report, new UserProductTotalReportTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/rebate_computation_reports",
     *      operationId="backstage.rebate_computation_reports.index",
     *      tags={"Backstage-报表"},
     *      summary="会员有效流水报表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]",in="query",description="查询结束时间",@OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/RebateComputationReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function rebateComputationReportIndex(ReportRequest $request)
    {
        // old version logic
        $condition = []; // [['user_product_daily_reports.user_name', $userName], ['user_product_daily_reports.product_code', $productCode]]
        $now         = now()->toDateString();
        $startAt     = !empty($request->filter['start_at']) ? $request->filter['start_at'] : $now;
        $endAt       = !empty($request->filter['end_at']) ? $request->filter['end_at'] : $now;

        !empty($request->filter['user_name']) && $condition[] = ['user_product_daily_reports.user_name', $request->filter['user_name']];
        !empty($request->filter['product_code']) && $condition[] = ['user_product_daily_reports.product_code', $request->filter['product_code']];
        !empty($request->filter['end_at']) && $condition[] = ['user_product_daily_reports.date', '<=', $request->filter['end_at']];
        !empty($request->filter['start_at']) && $condition[] = ['user_product_daily_reports.date', '>=', $request->filter['start_at']];
        !empty($request->filter['currency']) && $condition[] = ['users.currency', $request->filter['currency']];

        $reports = UserProductDailyReport::query()
            ->where($condition)
            ->with('user')
            ->leftJoin('user_bonus_prizes', function ($join){
                $join->on('user_product_daily_reports.user_id', '=', 'user_bonus_prizes.user_id')
                    ->on('user_product_daily_reports.product_code', '=', 'user_bonus_prizes.product_code');
            })
            ->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->groupBy('user_product_daily_reports.user_id')
            ->groupBy('user_product_daily_reports.product_code')
            ->groupBy('user_bonus_prizes.bonus_code')
            ->groupBy('user_bonus_prizes.id')
            ->paginate($request->per_page, [
                'user_product_daily_reports.user_id',
                'user_product_daily_reports.product_code',
                'users.currency',
                'user_bonus_prizes.bonus_code',
                DB::raw('SUM(user_product_daily_reports.effective_bet) as user_total_bet, sum(user_bonus_prizes.turnover_closed_value) as total_turnover_value,user_bonus_prizes.id as user_bonus_prize_id'),
            ]);

        # 准备数据 对应用户最后领prize 的时间
        $userIdList                = collect($reports->items())->pluck('user_id')->toArray();
        $userLatestRebatePrizeList = UserRebatePrizeRepository::getLatestRebateByUser($userIdList);

        # 准备数据 对应 bonus 记录的数据
        $userBonusPrizeId   = collect($reports->items())
            ->where('user_bonus_prize_id', '<>', null)
            ->pluck('user_bonus_prize_id')
            ->toArray();
        $userBonusPrizeList = UserBonusPrize::query()->whereIn('id', $userBonusPrizeId)->get();

        $transferData = [
            'start_at'                      => $startAt,
            'end_at'                        => $endAt,
            'user_bonus_prize_list'         => $userBonusPrizeList,
            'user_latest_rebate_prize_list' => $userLatestRebatePrizeList,
        ];

        return $this->response->paginator($reports, new RebateComputationReportTransformer('', $transferData));
    }

    /**
     * @OA\Get(
     *      path="/backstage/rebate_computation_reports/user_bonus_prizes",
     *      operationId="backstage.rebate_computation_reports.user_bonus_prizes",
     *      tags={"Backstage-报表"},
     *      summary="会员有效流水报表对应的红利奖励",
     *      @OA\Parameter(name="filter[user_id]", in="query", description="会员id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]",in="query",description="查询结束时间",@OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserBonusPrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function getReportUserBonusPrizes(Request $request)
    {
        $filters = ['user_id', 'start_at', 'end_at', 'product_code'];
        $data    = $request->all();
        $data    = isset($data['filter']) ? $data['filter'] : [];

        foreach ($filters as $filter) {
            if (!isset($data[$filter])) {
                $data[$filter] = '';
            }
        }

        $prizes = UserBonusPrizeRepository::getBuilderByUserTimeAndProductCode(
            $data['user_id'],
            $data['start_at'],
            $data['end_at'],
            $data['product_code']
        )->paginate($request->per_page);

        return $this->response->paginator($prizes, new UserBonusPrizeIndexTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_main_wallet_total_report",
     *      operationId="backstage.user_main_wallet_total_report.index",
     *      tags={"Backstage-报表"},
     *      summary="会员主钱包统计报表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称",@OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Success",
     *          @OA\JsonContent(ref="#/components/schemas/UserMainWalletTotalReport"),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userMainWalletTotalReport(ReportRequest $request)
    {
        $report = UserPlatformTotalReport::query()->where('user_name', $request->filter['user_name'])
            ->where('platform_code', UserAccount::MAIN_WALLET)
            ->first();

        $report = $report ?? new UserPlatformTotalReport(['user_name' => $request->filter['user_name']]);

        if ($user = UserRepository::findByName($request->filter['user_name'])) {
            $report->available_balance = $user->account->getAvailableBalance();
        } else {
            $report->available_balance = 0;
        }

        return $this->response->item($report, new UserMainWalletTotalReportTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/active_user_report",
     *      operationId="backstage.active_user_report.index",
     *      tags={"Backstage-报表"},
     *      summary="总活跃会员报表",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", description="活跃数据", @OA\Items(
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="active", type="integer", description="活跃会员"),
     *                  @OA\Property(property="inactive", type="integer", description="不活跃会员"),
     *                  @OA\Property(property="margin", type="number", description="活跃/会员总数百分比)"),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function activeUserReportIndex(ReportRequest $request)
    {
        $startAt  = !empty($request->filter['start_at']) ? $request->filter['start_at'] : now()->toDateString();
        $endAt    = !empty($request->filter['end_at']) ? $request->filter['end_at'] : now()->toDateString();
        $currency = !empty($request->filter['currency']) ? $request->filter['currency'] : '';

        $builder = UserProductDailyReport::query()->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('date', '>=', $startAt)
            ->where('date', '<=', $endAt)
            ->where('stake', '>', 0);

        if ($currency) {
            $builder->where('users.currency', $currency);
        }

        $actives = $builder->groupBy('users.currency')
            ->get([
                      'users.currency',
                      DB::raw('COUNT(DISTINCT user_id) as active'),
                  ]);

        $countUsers = User::query()->isUser()->groupBy('currency')->get([
                                                                            'currency',
                                                                            DB::raw('COUNT(*) as total'),
                                                                        ])->pluck('total', 'currency')->toArray();

        $data = [];
        foreach ($actives as $active) {
            $total  = $countUsers[$active->currency];
            $data[] = [
                'currency' => $active->currency,
                'active'   => $active->active,
                'inactive' => $total - $active->active,
                'margin'   => format_number($active->active * 100 / $total, 2),
            ];
        }

        return $this->response->array([
                                          'data' => $data,
                                      ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/active_user_report_by_affiliate",
     *      operationId="backstage.active_user_report_by_affiliate.index",
     *      tags={"Backstage-报表"},
     *      summary="活跃会员平台报表[代理区分]",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", description="活跃数据", @OA\Items(
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="platform_active", type="integer", description="平台活跃会员"),
     *                  @OA\Property(property="affiliate_active", type="integer", description="代理活跃会员"),
     *                  @OA\Property(property="margin", type="number", description="代理/活跃总数百分比)"),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function activeUserReportByAffiliateIndex(ReportRequest $request)
    {
        $startAt  = !empty($request->filter['start_at']) ? $request->filter['start_at'] : now()->toDateString();
        $endAt    = !empty($request->filter['end_at']) ? $request->filter['end_at'] : now()->toDateString();
        $currency = !empty($request->filter['currency']) ? $request->filter['currency'] : '';


        $builder = UserProductDailyReport::query()->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('date', '>=', $startAt)
            ->where('date', '<=', $endAt)
            ->where('stake', '>', 0);

        if ($currency) {
            $builder->where('users.currency', $currency);
        }

        $actives = $builder->groupBy(DB::raw('users.affiliated_code is null'))
            ->groupBy('users.currency')
            ->get([
                      DB::raw('users.affiliated_code is null as is_platform_user'),
                      'users.currency',
                      DB::raw('COUNT(DISTINCT user_id) as active'),
                  ]);

        $data = [];

        foreach ($actives as $active) {
            $data[$active->currency]['currency'] = $active->currency;

            if ($active->is_platform_user) {
                $data[$active->currency]['platform_active'] = $active->active;
            } else {
                $data[$active->currency]['affiliate_active'] = $active->active;
            }
        }

        foreach ($data as &$item) {
            $item['platform_active']  = isset($item['platform_active']) ? $item['platform_active'] : 0;
            $item['affiliate_active'] = isset($item['affiliate_active']) ? $item['affiliate_active'] : 0;
            $item['margin']           = $item['affiliate_active'] * 100 / ($item['platform_active'] + $item['affiliate_active']);
            $item['margin']           = format_number($item['margin'], 2);

        }

        return $this->response->array([
                                          'data' => array_values($data),
                                      ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/active_user_report_by_product",
     *      operationId="backstage.active_user_report_by_product.index",
     *      tags={"Backstage-报表"},
     *      summary="活跃会员平台报表[产品区分]",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", description="活跃数据", @OA\Items(
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *                  @OA\Property(property="product_code", type="integer", description="产品活跃会员"),
     *              )),
     *              @OA\Property(property="fields", description="字段", @OA\Items()),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function activeUserReportByProductIndex(ReportRequest $request)
    {
        $startAt  = !empty($request->filter['start_at']) ? $request->filter['start_at'] : now()->toDateString();
        $endAt    = !empty($request->filter['end_at']) ? $request->filter['end_at'] : now()->toDateString();
        $currency = !empty($request->filter['currency']) ? $request->filter['currency'] : '';


        $builder = UserProductDailyReport::query()->leftJoin('users', 'users.id', '=', 'user_product_daily_reports.user_id')
            ->where('date', '>=', $startAt)
            ->where('date', '<=', $endAt)
            ->where('stake', '>', 0);

        if ($currency) {
            $builder->where('users.currency', $currency);
        }

        $actives = $builder->groupBy('product_code', 'users.currency')
            ->get([
                      'product_code',
                      'users.currency',
                      DB::raw('COUNT(DISTINCT user_id) as active'),
                  ]);

        # 获取所有产品
        $productCodes = GamePlatformProduct::getDropList();

        $data = [];
        # 初始化
        foreach ($actives as $active) {
            $data[$active->currency]['currency'] = $active->currency;

            foreach ($productCodes as $code) {
                $data[$active->currency][$code] = 0;
            }
        }

        foreach ($actives as $active) {
            $data[$active->currency][$active->product_code] = $active->active;
        }

        return $this->response->array([
                                          'data'   => array_values($data),
                                          'fields' => array_values($productCodes),
                                      ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/member_active_reports",
     *      operationId="backstage.member_active_reports",
     *      tags={"Backstage-报表"},
     *      summary="Members Activity Reports",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="Successful",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/MembersActivityReports"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function memberActiveReports(Request $request)
    {
        $users = QueryBuilder::for(User::query())
            ->where('is_agent', false)
            ->allowedFilters([
                                 Filter::exact('currency'),
                                 Filter::scope('start_at'),
                                 Filter::scope('end_at'),
                             ])
            ->select([
                         DB::raw('Date(created_at) as date'),
                         DB::raw('COUNT(name) as register'),
                         DB::raw('COUNT(first_deposit_at) as deposit'),
                         DB::raw('SUM(CASE WHEN status != ' . User::STATUS_ACTIVE . ' THEN  1 ELSE 0 END) inactive'),
                     ])
            ->orderBy('date', 'DESC')
            ->groupBy('date')
            ->get();

        $total = [
            'register' => $users->sum('register'),
            'deposit'  => $users->sum('deposit'),
            'inactive' => $users->sum('inactive'),
        ];

        $users = $this->paginateCollection($users, $request->per_page);

        return $this->response->paginator($users, new MembersActivityReportsTransformer())->setMeta(['total' => $total]);
    }

    public function appAccessLog(Request $request)
    {
        $userLog = QueryBuilder::for(UserLoginLog::query())
                    ->allowedFilters([
                         Filter::scope('member_code'),
                         Filter::scope('currency'),
                         Filter::scope('start_at'),
                         Filter::scope('end_at'),
                         Filter::scope('status'),
                     ])
            ->with('user')
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($userLog, new UserLoginLogTransformer());
    }

    public function exportAppAccessLog(Request $request)
    {
        $headings = [
            'Date',
            'Member Code',
            'Currency',
            'Log-in Time',
            'Status',
            'Remarks',
        ];

        $userLogs = QueryBuilder::for(UserLoginLog::class)
            ->allowedFilters(
                Filter::scope('member_code'),
                Filter::scope('currency'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::scope('status'),
            )
            ->with('user')
            ->latest()
            ->get();

        $exportData = [];

        foreach ($userLogs as $userLog) {
            $exportData[] = [
                'date'          => date("Y-m-d", strtotime($userLog->created_at)),
                'member_code'   => $userLog->user_name,
                'currency'      => $userLog->user->currency,
                'log-in-time'   => $userLog->created_at,
                'status'        => transfer_show_value($userLog->success_login, UserLoginLog::$loginStatus),
                'remark'        => $userLog->remark,
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'app_access_log.xlsx');
    }

    public function memberDepositHistoryDevice(Request $request)
    {

        $deposits = QueryBuilder::for(Deposit::query())
                        ->allowedFilters([
                             Filter::scope('member_code'),
                             Filter::exact('currency'),
                             Filter::exact('order_no'),
                             Filter::scope('start_at'),
                             Filter::scope('end_at'),
                             Filter::scope('status'),
                             Filter::exact('device'),
                        ])
        ->with('user')
        ->latest()
        ->paginate($request->per_page);

        return $this->response->paginator($deposits, new DepositTransformer());
    }

    public function exportMemberDepositHistoryLog(Request $request)
    {
        $headings = [
            'Date',
            'Member Code',
            'Transaction ID',
            'Currency',
            'Status',
            'Device',
        ];

        $memberDepositHistories = QueryBuilder::for(Deposit::query())
            ->allowedFilters(
                 Filter::scope('member_code'),
                 Filter::exact('currency'),
                 Filter::exact('order_no'),
                 Filter::scope('start_at'),
                 Filter::scope('end_at'),
                 Filter::scope('status'),
                 Filter::exact('device'),
            )
            ->with('user')
            ->latest()
            ->get();

        $exportData = [];

        foreach ($memberDepositHistories as $memberDepositHistory) {
            $exportData[] = [
                'date'          => $memberDepositHistory->deposit_at,
                'member_code'   => $memberDepositHistory->user->name,
                'order_no'      => $memberDepositHistory->order_no,
                'currency'      => $memberDepositHistory->currency,
                'status'        => transfer_show_value($memberDepositHistory->status, Deposit::$statues),
                'device'        => transfer_show_value($memberDepositHistory->device, User::$devices),
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'member_deposit_history_log.xlsx');
    }

    protected function paginateCollection($collection, $perPage, $pageName = 'page', $fragment = null)
    {
        $currentPage      = \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPage($pageName);
        $currentPageItems = $collection->slice(($currentPage - 1) * $perPage, $perPage)->values();
        parse_str(request()->getQueryString(), $query);
        unset($query[$pageName]);
        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $currentPageItems,
            $collection->count(),
            $perPage,
            $currentPage,
            [
                'pageName' => $pageName,
                'path'     => \Illuminate\Pagination\LengthAwarePaginator::resolveCurrentPath(),
                'query'    => $query,
                'fragment' => $fragment,
            ]
        );

        return $paginator;
    }

}
