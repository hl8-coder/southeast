<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Exports\BetHistoryExport;
use App\Exports\MemberProfileSummaryReportExport;
use App\Exports\PaymentReportExport;
use App\Models\User;
use App\Models\UserPlatformDailyReport;
use App\Repositories\AffiliateRepository;
use App\Services\AffiliateService;
use Carbon\Carbon;
use App\Models\Config;
use App\Models\Deposit;
use App\Models\Withdrawal;
use App\Models\Transaction;
use App\Models\GamePlatform;
use Illuminate\Support\Facades\DB;
use App\Models\GamePlatformProduct;
use App\Models\UserProductDailyReport;
use App\Http\Controllers\ApiController;
use App\Http\Requests\Affiliate\ReportRequest;
use App\Transformers\AffiliateCommissionTransformer;
use App\Transformers\AffiliateMemberReportTransformer;
use Maatwebsite\Excel\Facades\Excel;

class ReportsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/commission_summary",
     *      operationId="api.affiliates.commission_summary",
     *      tags={"Affiliate-代理"},
     *      summary="代理CommissionSummary",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateCommission"),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function commissionSummaryReport(ReportRequest $request, AffiliateService $service)
    {
        if ($month = $request->month) {
            $month = Carbon::parse($month)->firstOfMonth()->toDateString();
        } else {
            $month = now()->firstOfMonth()->toDateString();
        }
        $affiliate   = $this->user()->affiliate;
        $commissions = $service->getCommissionByMonth($affiliate, $month);

        if (empty($commissions[0])) {
            return $this->response->noContent();
        }
        return $this->response->item($commissions[0], new AffiliateCommissionTransformer());
    }

    /**
     * @OA\Get(
     *      path="/affiliate/member_profile_summary",
     *      operationId="api.affiliates.member_profile_summary",
     *      tags={"Affiliate-代理"},
     *      summary="代理MemberProfileSummary",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateMemberReport"),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function memberProfileSummaryReport(ReportRequest $request)
{
    $month = $request->month;
    if ($month) {
        $start = Carbon::parse($month)->firstOfMonth()->toDateTimeString();
        $end   = Carbon::parse($month)->endOfMonth()->toDateTimeString();
    } else {
        $start = now()->firstOfMonth()->toDateTimeString();
        $end   = now()->endOfMonth()->toDateTimeString();
    }
    $date      = [
        'start' => $start,
        'end'   => $end,
    ];
    $affiliate = $this->user();

    $users = $affiliate->subUsers()
        ->where("created_at", ">=", $start)
        ->where("created_at", "<=", $end)
        ->paginate($request->per_page);

    return $this->response->paginator($users, new AffiliateMemberReportTransformer('member_profileSummary', $date));
}


    /**
     * @OA\Get(
     *      path="/affiliate/member_profile_summary_export",
     *      operationId="api.affiliates.member_profile_summary_export",
     *      tags={"Affiliate-代理"},
     *      summary="代理MemberProfileSummaryExport",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/MemberProfileSummaryReportExport"),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function memberProfileSummaryReportExport(ReportRequest $request)
    {
        $affiliate = $this->user();

        return Excel::download(new MemberProfileSummaryReportExport($request, $affiliate), 'member_profile_summary.xlsx');
    }



    /**
     * @OA\Get(
     *      path="/affiliate/payment_report",
     *      operationId="api.affiliates.payment_report",
     *      tags={"Affiliate-代理"},
     *      summary="代理PaymentReport",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateMemberReport"),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function paymentReport(ReportRequest $request)
    {
        $month = $request->month;
        if ($month) {
            $startAt = Carbon::parse($month)->firstOfMonth();
            $endAt   = Carbon::parse($month)->endOfMonth();
        } else {
            $startAt = now()->firstOfMonth();
            $endAt   = now()->endOfMonth();
        }
        $affiliate = $this->user();
        $userIds   = $affiliate->subUsers->pluck('id')->toArray();

        $productReports = UserProductDailyReport::query()
            ->whereIn('user_id', $userIds)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->groupBy('user_id')
            ->get([
                'user_id',
                DB::raw('SUM(rebate) as rebate'),
            ]);

        $platformReports = UserPlatformDailyReport::query()
            ->whereIn('user_id', $userIds)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->groupBy('user_id')
            ->get([
                'user_id',
                DB::raw('SUM(deposit) as deposit'),
                DB::raw('SUM(withdrawal) as withdrawal'),
                DB::raw('SUM(promotion) as promotion'),
            ]);

        $productReportIds = $productReports->pluck('user_id')->toArray();
        $platformReportIds = $platformReports->pluck('user_id')->toArray();
        $uniqueUserIds = $productReportIds + $platformReportIds;

        $depositFeePercent = Config::findValue('deposit_fee_percent', 10);
        $withdrawalFeePercent = Config::findValue('withdrawal_fee_percent', 10);
        $users     = $affiliate->subUsers()->whereIn('id', array_unique($uniqueUserIds))->with(['info'])->paginate($request->per_page);
        $data      = [];
        foreach ($users as $key => $user) {
            # 获取游戏数据
            $selfGameData = $productReports->where('user_id', $user->id)->first();
            $user->total_rebate    = !empty($selfGameData['rebate']) ? $selfGameData['rebate'] : 0;

            # 获取平台数据
            $selfTransactionData = $platformReports->where('user_id', $user->id)->first();
            $user->total_deposit           = !empty($selfTransactionData['deposit'])    ? $selfTransactionData['deposit'] : 0;
            $user->total_withdrawal        = !empty($selfTransactionData['withdrawal']) ? $selfTransactionData['withdrawal'] : 0;
            $user->total_bonus             = !empty($selfTransactionData['promotion'])  ? $selfTransactionData['promotion']  : 0;

            $user->total_payment_fee = $user->total_deposit * $depositFeePercent / 100 + $user->total_withdrawal * $withdrawalFeePercent / 100;
        }

        $totalRebate     = $productReports->isNotEmpty() ?  $productReports->sum('rebate') : 0;
        $totalDeposit    = $platformReports->isNotEmpty() ? $platformReports->sum('deposit') : 0;
        $totalWithdrawal = $platformReports->isNotEmpty() ? $platformReports->sum('withdrawal') : 0;
        $totalBonus      = $platformReports->isNotEmpty() ? $platformReports->sum('promotion') : 0;
        $totalPaymentFee = $totalDeposit * $depositFeePercent / 100 + $totalWithdrawal * $withdrawalFeePercent / 100;


        $data['total'] = [
            'deposit'     => thousands_number($totalDeposit),
            'withdrawal'  => thousands_number($totalWithdrawal),
            'payment_fee' => thousands_number($totalPaymentFee),
            'bonus'       => thousands_number($totalBonus),
            'rebate'      => thousands_number($totalRebate),
        ];
        return $this->response->paginator($users, new AffiliateMemberReportTransformer('payment_report'))->setMeta($data);
    }

    /**
     * @OA\Get(
     *      path="/affiliate/payment_report/export",
     *      operationId="api.affiliates.payment_report/export",
     *      tags={"Affiliate-代理"},
     *      summary="导出代理PaymentReport",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PaymentReportExport"),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function paymentReportExport(ReportRequest $request)
    {
        $month = $request->month;
        if ($month) {
            $startAt = Carbon::parse($month)->firstOfMonth();
            $endAt   = Carbon::parse($month)->endOfMonth();
        } else {
            $startAt = now()->firstOfMonth();
            $endAt   = now()->endOfMonth();
        }
        $affiliate = $this->user();
        $userIds   = $affiliate->subUsers->pluck('id')->toArray();

        $productReports = UserProductDailyReport::query()
            ->whereIn('user_id', $userIds)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->groupBy('user_id')
            ->get([
                'user_id',
                DB::raw('SUM(rebate) as rebate'),
            ]);

        $platformReports = UserPlatformDailyReport::query()
            ->whereIn('user_id', $userIds)
            ->where('date', '>=', $startAt->toDateString())
            ->where('date', '<=', $endAt->toDateString())
            ->groupBy('user_id')
            ->get([
                'user_id',
                DB::raw('SUM(deposit) as deposit'),
                DB::raw('SUM(withdrawal) as withdrawal'),
                DB::raw('SUM(promotion) as promotion'),
            ]);

        $productReportIds = $productReports->pluck('user_id')->toArray();
        $platformReportIds = $platformReports->pluck('user_id')->toArray();
        $uniqueUserIds = $productReportIds + $platformReportIds;
        $depositFeePercent = Config::findValue('deposit_fee_percent', 10);
        $withdrawalFeePercent = Config::findValue('withdrawal_fee_percent', 10);

        $users     = $affiliate->subUsers()->whereIn('id', array_unique($uniqueUserIds))->get();
        foreach ($users as $key => $user) {
            # 获取游戏数据
            $selfGameData = $productReports->where('user_id', $user->id)->first();
            $user->total_rebate    = !empty($selfGameData['rebate']) ? $selfGameData['rebate'] : 0;

            # 获取平台数据
            $selfTransactionData = $platformReports->where('user_id', $user->id)->first();
            $user->total_deposit           = !empty($selfTransactionData['deposit'])    ? $selfTransactionData['deposit'] : 0;
            $user->total_withdrawal        = !empty($selfTransactionData['withdrawal']) ? $selfTransactionData['withdrawal'] : 0;
            $user->total_bonus             = !empty($selfTransactionData['promotion'])  ? $selfTransactionData['promotion']  : 0;

            $user->total_payment_fee = $user->total_deposit * $depositFeePercent / 100 + $user->total_withdrawal * $withdrawalFeePercent / 100;
        }
        return Excel::download(new PaymentReportExport($users), 'report.xlsx');
    }

    /**
     * @OA\Get(
     *      path="/affiliate/company_product_report",
     *      operationId="api.affiliates.company_product_report",
     *      tags={"Affiliate-代理"},
     *      summary="代理Company Win/Loss Based On Products",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", description="类型列表", @OA\Items(
     *                  @OA\Property(property="total_stake", type="string", description="总投注"),
     *                  @OA\Property(property="total_profit", type="string", description="总盈亏"),
     *                  @OA\Property(property="total_rakes", type="string", description="返点"),
     *                  @OA\Property(property="type", type="string", description="类型"),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function companyProductReport(ReportRequest $request)
    {
        $affiliate  = $this->user();
        $subUserIds = $affiliate->subUsers()->pluck('id');
        $month      = $request->month;
        if (!empty($month)) {
            $start = Carbon::parse($month)->firstOfMonth()->toDateTimeString();
            $end   = Carbon::parse($month)->endOfMonth()->toDateTimeString();
        } else {
            $start = now()->firstOfMonth()->toDateTimeString();
            $end   = now()->endOfMonth()->toDateTimeString();
        }

        $requestType = $request->filter["product_type"] ?? '';
        $types       = GamePlatformProduct::$types;
        if ($requestType) {
            $types = [$requestType => GamePlatformProduct::$types[$requestType]];
        }

        $data = [];
        foreach ($types as $key => $type) {
            $gamePlatformProduct = GamePlatformProduct::query()->where('type', $key)->pluck('code');
            $reports             = UserProductDailyReport::query()
                ->whereIn('user_id', $subUserIds)
                ->whereIn('product_code', $gamePlatformProduct)
                ->where([
                    [
                        'created_at', '>=', $start
                    ],
                    [
                        'created_at', '<', $end
                    ]
                ])
                ->select(
                    DB::raw("sum(stake) as total_stake"),
                    DB::raw("sum(profit) as total_profit"),
                    DB::raw("sum(rebate) as total_rakes")
                )
                ->first();
            $data['data'][]      = [
                'type'         => $key,
                'display_type' => __('dropList.' . GamePlatformProduct::$typesForTranslation[$key]),
                'total_stake'  => thousands_number($reports->total_stake),
                'total_profit' => thousands_number($reports->total_profit),
                'total_rakes'  => thousands_number($reports->total_rakes),
            ];
        }

        return $data;
    }

    /**
     * @OA\Get(
     *      path="/affiliate/company_product_report/{product}",
     *      operationId="api.affiliates.company_product_report.detail",
     *      tags={"Affiliate-代理"},
     *      summary="代理Company Win/Loss Based On Products",
     *      @OA\Parameter(
     *         name="product",
     *         in="path",
     *         description="product ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", description="会员列表", @OA\Items(
     *                  @OA\Property(property="total_stake", type="string", description="总投注"),
     *                  @OA\Property(property="total_profit", type="string", description="总盈亏"),
     *                  @OA\Property(property="user_name", type="string", description="用户名"),
     *              )),
     *              @OA\Property(property="total", description="会员列表", @OA\Items(
     *                  @OA\Property(property="total_stake", type="string", description="总投注"),
     *                  @OA\Property(property="total_profit", type="string", description="总盈亏"),
     *                  @OA\Property(property="code", type="string", description="产品名"),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function companyProductDetailReport(ReportRequest $request, $type)
    {
        $affiliate  = $this->user();
        $subUserIds = $affiliate->subUsers()->pluck('id');
        $month      = $request->month;
        if (!empty($month)) {
            $start = Carbon::parse($month)->firstOfMonth()->toDateTimeString();
            $end   = Carbon::parse($month)->endOfMonth()->toDateTimeString();
        } else {
            $start = now()->firstOfMonth()->toDateTimeString();
            $end   = now()->endOfMonth()->toDateTimeString();
        }
        $gamePlatformProduct = GamePlatformProduct::query()->where('type', $type)->pluck('code');
        $info                = UserProductDailyReport::query()
            ->where([
                [
                    'created_at', '>=', $start
                ],
                [
                    'created_at', '<=', $end
                ]
            ])
            ->whereIn('user_id', $subUserIds)
            ->whereIn('product_code', $gamePlatformProduct)
            ->select(
                DB::raw("sum(stake) as total_stake, sum(profit) as total_profit, user_name")
            )
            ->groupBy('user_name')->get();
        $total = [
            'total_stake'  => thousands_number($info->sum('total_stake')),
            'total_profit' => thousands_number($info->sum('total_profit')),
            'code'         => transfer_show_value($type, GamePlatformProduct::$types),
        ];
        foreach ($info as $value) {
            $value->user_name    = hidden_name($value->user_name);
            $value->total_stake  = thousands_number($value->total_stake);
            $value->total_profit = thousands_number($value->total_profit);
        }
        $data  = [
            'data'  => $info,
            'total' => $total,
        ];
        return $data;
    }

    /**
     * @OA\Get(
     *      path="/affiliate/company_provider_report",
     *      operationId="api.affiliates.company_provider_report",
     *      tags={"Affiliate-代理"},
     *      summary="代理Company Win/Loss By Provider",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="data", description="平台报表", @OA\Items(
     *                  @OA\Property(property="total_stake", type="string", description="总投注"),
     *                  @OA\Property(property="total_profit", type="string", description="总盈亏"),
     *                  @OA\Property(property="total_rakes", type="string", description="总返点"),
     *                  @OA\Property(property="active", type="string", description="活跃会员"),
     *                  @OA\Property(property="code", type="string", description="平台"),
     *              )),
     *              @OA\Property(property="total", description="会员列表", @OA\Items(
     *                  @OA\Property(property="total_stake", type="string", description="总投注"),
     *                  @OA\Property(property="total_profit", type="string", description="总盈亏"),
     *                  @OA\Property(property="total_rakes", type="string", description="总返点"),
     *                  @OA\Property(property="total_active", type="string", description="活跃会员"),
     *              )),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function companyProviderReport(ReportRequest $request)
    {
        $affiliate  = $this->user();
        $subUserIds = $affiliate->subUsers()->pluck('id');
        $month      = $request->month;
        if (!empty($month)) {
            $start = Carbon::parse($month)->firstOfMonth()->toDateTimeString();
            $end   = Carbon::parse($month)->endOfMonth()->toDateTimeString();
        } else {
            $start = now()->firstOfMonth()->toDateTimeString();
            $end   = now()->endOfMonth()->toDateTimeString();
        }

        $gamePlatforms = GamePlatform::query()->get();
        $data          = [];
        $total         = [
            'total_stake'  => 0,
            'total_profit' => 0,
            'total_rakes'  => 0,
            'total_active' => UserProductDailyReport::query()->where([
                [
                    'created_at', '>=', $start
                ],
                [
                    'created_at', '<', $end
                ]
            ])
                                  ->whereIn('user_id', $subUserIds)
                                  ->select(DB::raw("count(distinct `user_name`) as active"))
                                  ->first()['active'],
        ];
        foreach ($gamePlatforms as $platform) {
            $report                = UserProductDailyReport::query()
                ->where([
                    [
                        'platform_code', $platform->code
                    ],
                    [
                        'created_at', '>=', $start
                    ],
                    [
                        'created_at', '<', $end
                    ]
                ])
                ->whereIn('user_id', $subUserIds)
                ->select(
                    DB::raw("sum(stake) as total_stake"),
                    DB::raw("sum(profit) as total_profit"),
                    DB::raw("sum(rebate) as total_rakes"),
                    DB::raw("count(distinct `user_name`) as active")
                )
                ->first();
            $data['data'][]        = [
                'code'         => $platform->code,
                'total_stake'  => thousands_number($report['total_stake']),
                'total_profit' => thousands_number($report['total_profit']),
                'total_rakes'  => thousands_number($report['total_rakes']),
                'active'       => $report['active'],
            ];
            $total['total_stake']  += $report['total_stake'];
            $total['total_profit'] += $report['total_profit'];
            $total['total_rakes']  += $report['total_rakes'];
        }
        $total['total_stake']  = thousands_number($total['total_stake']);
        $total['total_profit'] = thousands_number($total['total_profit']);
        $total['total_rakes']  = thousands_number($total['total_rakes']);
        $data['total']         = $total;
        return $data;
    }
}
