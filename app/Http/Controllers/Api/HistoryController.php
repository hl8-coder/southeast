<?php

namespace App\Http\Controllers\Api;

use App\Models\PromotionClaimUser;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;
use App\Services\HistoryService;
use App\Models\GamePlatformTransferDetail;
use App\Models\Adjustment;
use App\Models\UserRebatePrize;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Carbon\Carbon;
use App\Transformers\HistoryDepositWithdrawalTransformer;
use App\Transformers\HistoryFundTransferTransformer;
use App\Transformers\HistoryAdjustmentTransformer;
use App\Transformers\HistoryPromotionClaimTransformer;
use App\Transformers\HistoryRebateTransformer;

class HistoryController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/history/deposit_withdrawal",
     *      operationId="api.history.deposit_withdrawal.index",
     *      tags={"Api-历程"},
     *      summary="充值提领历程",
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_from]", in="query", description="启始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_to]", in="query", description="结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/HistoryDepositWithdrawal"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function depositWithdrawal(Request $request)
    {
        # 需要 droplist 的时候，务必调用 HistoryService 下的 getDropList() 方法
        $result = (new HistoryService())->depositWithdrawal(
            $this->user()->id,
            $request->filter
        );

        $result = QueryBuilder::for($result)
            ->paginate($request->per_page);

        return $this->response->paginator($result, new HistoryDepositWithdrawalTransformer());
    }

    /**
     * @OA\Get(
     *      path="/history/fund_transfer",
     *      operationId="api.history.fund_transfer.index",
     *      tags={"Api-历程"},
     *      summary="钱包转帐历程",
     *      @OA\Parameter(name="filter[fo_status]", in="query", description="状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_from]", in="query", description="启始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_to]", in="query", description="结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[platform_code]", in="query", description="第三方平台code", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/HistoryFundTransfer"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function fundTransfer(Request $request)
    {
        # 预设抓7天
        if (!$request->date_from) {
            $request->merge(['date_from' => Carbon::today()->subDays(7)]);
        }

        $result = QueryBuilder::for(GamePlatformTransferDetail::class)
            ->where('user_id', $this->user()->id)
            ->allowedFilters(
                Filter::scope('fo_status'),
                Filter::scope('date_from'),
                Filter::scope('date_to'),
                Filter::scope('platform_code')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($result, new HistoryFundTransferTransformer());
    }

    /**
     * @OA\Get(
     *      path="/history/adjustment",
     *      operationId="api.history.adjustment.index",
     *      tags={"Api-历程"},
     *      summary="调整历程",
     *      @OA\Parameter(name="filter[fo_status]", in="query", description="状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_from]", in="query", description="启始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_to]", in="query", description="结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/HistoryAdjustment"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function adjustment(Request $request)
    {
        $filter = remove_null($request->filter);

        # 预设抓7天
        if (empty($filter['date_from'])) {
            $filter['date_from'] = Carbon::today()->subDays(7);
        }

        if (!empty($filter['date_from'])) {
            $filter['date_from'] = Carbon::parse($filter['date_from'])->startOfDay();
        }

        if (!empty($request->filter['date_to'])) {
            $filter['date_to'] = Carbon::parse($filter['date_to'])->endOfDay();
        }

        $request->merge(['filter' => $filter,]);

        $result = QueryBuilder::for(Adjustment::class)
            ->where("user_id", $this->user()->id)
            ->allowedFilters(
                Filter::scope('fo_status'),
                Filter::exact('type'),
                Filter::scope('date_from'),
                Filter::scope('date_to')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($result, new HistoryAdjustmentTransformer());
    }

    /**
     * @OA\Get(
     *      path="/history/promotion_claim",
     *      operationId="api.history.promotion_claim.index",
     *      tags={"Api-历程"},
     *      summary="优惠历程",
     *      @OA\Parameter(name="filter[date_from]", in="query", description="启始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_to]", in="query", description="结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/HistoryPromotionClaim"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function promotionClaim(Request $request)
    {
        # 预设抓7天
        if (!$request->date_from) {
            $request->merge(['date_from' => Carbon::today()->subDays(7)]);
        }

        $result = QueryBuilder::for(PromotionClaimUser::class)
            ->with('promotion')
            ->where('user_id', $this->user()->id)
            ->allowedFilters(
                Filter::scope('date_from'),
                Filter::scope('date_to')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($result, new HistoryPromotionClaimTransformer());
    }

    /**
     * @OA\Get(
     *      path="/history/rebate",
     *      operationId="api.history.rebate.index",
     *      tags={"Api-历程"},
     *      summary="返点历程",
     *      @OA\Parameter(name="filter[product_code]", in="query", description="游戏产品", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_from]", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[date_to]", in="query", description="查询结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/HistoryRebate"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function rebate(Request $request)
    {
        # 预设抓7天
        if (!$request->date_from) {
            $request->merge(["date_from" => Carbon::today()->subDays(7)]);
        }

        $result = QueryBuilder::for(UserRebatePrize::class)
            ->where("user_id", $this->user()->id)
            ->where("status", UserRebatePrize::STATUS_SUCCESS)
            ->allowedFilters(
                Filter::exact('product_code'),
                Filter::scope('date_from'),
                Filter::scope('date_to')
            )
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($result, new HistoryRebateTransformer());
    }
}
