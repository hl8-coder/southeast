<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\UserRebatePrizeRequest;
use App\Jobs\TransactionProcessJob;
use App\Models\RiskGroup;
use App\Models\Transaction;
use App\Models\UserRebatePrize;
use App\Repositories\UserRebatePrizeRepository;
use App\Services\TransactionService;
use App\Transformers\UserRebatePrizeTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Maatwebsite\Excel\Facades\Excel;

class UserRebatePrizesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/marketing/user_rebate_prizes",
     *      operationId="backstage.marketing.user_rebate_prizes.index",
     *      tags={"Backstage-返点"},
     *      summary="会员返点列表(marketing)",
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="会员风控等级", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[vip_id]", in="query", description="会员vip等级", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[rebate_code]", in="query", description="返利code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[min_payout]", in="query", description="大于最小返点金额", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[max_payout]", in="query", description="小于最大返点金额", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[marketing_initiate_payout]", in="query", description="是否派发 1 or 0", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserRebatePrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function marketingIndex(Request $request)
    {
        $filters = [
            'user_name',
            Filter::exact('risk_group_id'),
            Filter::exact('currency'),
            Filter::exact('vip_id'),
            Filter::exact('product_code'),
            Filter::exact('rebate_code'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('min_payout'),
            Filter::scope('max_payout'),
            Filter::scope('marketing_initiate_payout'),
        ];

        $ORM = UserRebatePrize::query();

        if($request->order) {
            $order = explode('_', $request->order);
            $sortType = array_pop($order);
            $ORM->orderBy(implode('_', $order), $sortType);
        }

        $prizes = QueryBuilder::for($ORM)->allowedFilters($filters)
                ->latest()
                ->paginate($request->per_page);

        $totalAmount = QueryBuilder::for(UserRebatePrize::class)->allowedFilters($filters)->sum('prize');

        return $this->response->paginator($prizes, new UserRebatePrizeTransformer('marketing'))
            ->setMeta([
                'info' => [
                    'Total Rebate Amt' => $totalAmount,
                ],
            ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/marketing/user_rebate_prizes/download",
     *      operationId="backstage.marketing.user_rebate_prizes.download",
     *      tags={"Backstage-返点"},
     *      summary="下载会员返点列表(marketing)",
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="会员风控等级", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[vip_id]", in="query", description="会员vip等级", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[rebate_code]", in="query", description="返利code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[min_payout]", in="query", description="大于最小返点金额", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[max_payout]", in="query", description="小于最大返点金额", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserRebatePrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function downloadMarketingReport(Request $request)
    {
        $filters = [
            'user_name',
            Filter::exact('risk_group_id'),
            Filter::exact('currency'),
            Filter::exact('vip_id'),
            Filter::exact('product_code'),
            Filter::exact('rebate_code'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('min_payout'),
            Filter::scope('max_payout'),
            Filter::scope('marketing_initiate_payout'),
        ];

        $prizes = QueryBuilder::for(UserRebatePrize::class)->allowedFilters($filters)
            ->latest()
            ->get();

        $headings = [
            'NO',
            'Rebate Date',
            'Currency',
            'Product',
            'Rebate Code',
            'Member Code',
            'Risk ID',
            'Gross Eligible Stake Amt',
            'Balance Bonus Rolover',
            'Multiplier',
            'Rebate Amt',
            'Rebate Computation Date',
            'Initiate Payout',
            'Initiate Payout By',
            'Initiate Payout Date',
        ];

        $exportData = [];
        foreach ($prizes as $prize) {
            $exportData[] = [
                'id'                    => $prize->id,
                'date'                  => $prize->date,
                'currency'              => $prize->currency,
                'product_code'          => $prize->product_code,
                'rebate_code'           => $prize->rebate_code,
                'user_name'             => $prize->user_name,
                'display_risk_group_id' => transfer_show_value($prize->risk_group_id, RiskGroup::getDropList()),
                'effective_bet'         => $prize->effective_bet,
                'calculate_rebate_bet'  => $prize->calculate_rebate_bet,
                'multipiler'            => thousands_number($prize->multipiler),
                'prize'                 => $prize->prize,
                'created_at'            => convert_time($prize->created_at),
                'display_status'        => $prize->isWaitingMarketSend() ? 'No' : 'Yes',
                'marketing_admin_name'  => $prize->marketing_admin_name,
                'marketing_sent_at'     => convert_time($prize->marketing_sent_at),
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'auto_rebate_initiate_payout_report.xlsx');
    }

    /**
     * @OA\Get(
     *      path="/backstage/member/user_rebate_prizes",
     *      operationId="backstage.member.user_rebate_prizes.index",
     *      tags={"Backstage-返点"},
     *      summary="会员返点列表(member)",
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[rebate_code]", in="query", description="返利code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserRebatePrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function memberIndex(Request $request)
    {
        $prizes = QueryBuilder::for(UserRebatePrize::class)
            ->allowedFilters([
                Filter::exact('user_name'),
                Filter::exact('currency'),
                Filter::exact('product_code'),
                Filter::exact('rebate_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            ])
            ->where('status', UserRebatePrize::STATUS_SUCCESS)
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($prizes, new UserRebatePrizeTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/payment/user_rebate_prizes",
     *      operationId="backstage.payment.user_rebate_prizes.index",
     *      tags={"Backstage-返点"},
     *      summary="会员返点列表(payment)",
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="会员风控等级", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[vip_id]", in="query", description="会员vip等级", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[rebate_code]", in="query", description="返利code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[payment_initiate_payout]", in="query", description="是否派发 1or0", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserRebatePrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function paymentIndex(Request $request)
    {
        $filters = [
            'user_name',
            Filter::exact('risk_group_id'),
            Filter::exact('currency'),
            Filter::exact('vip_id'),
            Filter::exact('product_code'),
            Filter::exact('rebate_code'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('payment_initiate_payout'),
        ];

        $prizes = QueryBuilder::for(UserRebatePrize::class)
            ->allowedFilters($filters)
            ->whereIn('status', UserRebatePrize::$paymentShowStatuses)
            ->latest()
            ->paginate($request->per_page);

        $totalAmount = QueryBuilder::for(UserRebatePrize::class)
            ->allowedFilters($filters)
            ->whereIn('status', UserRebatePrize::$paymentShowStatuses)
            ->sum('prize');

        return $this->response->paginator($prizes, new UserRebatePrizeTransformer('payment'))
            ->setMeta([
                'info' => [
                    'Total Rebate Amt' => $totalAmount,
                ],
            ]);
    }

    /**
     * @OA\Get(
     *      path="/backstage/payment/user_rebate_prizes/download",
     *      operationId="backstage.payment.user_rebate_prizes.download",
     *      tags={"Backstage-返点"},
     *      summary="下载会员返点列表(payment)",
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="会员风控等级", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[vip_id]", in="query", description="会员vip等级", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[rebate_code]", in="query", description="返利code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[min_payout]", in="query", description="大于最小返点金额", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[max_payout]", in="query", description="小于最大返点金额", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[payment_initiate_payout]", in="query", description="是否派发 1 or 0", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserRebatePrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function downloadPaymentReport(Request $request)
    {
        $filters = [
            'user_name',
            Filter::exact('risk_group_id'),
            Filter::exact('currency'),
            Filter::exact('vip_id'),
            Filter::exact('product_code'),
            Filter::exact('rebate_code'),
            Filter::scope('start_at'),
            Filter::scope('end_at'),
            Filter::scope('min_payout'),
            Filter::scope('max_payout'),
            Filter::scope('payment_initiate_payout'),
        ];

        $prizes = QueryBuilder::for(UserRebatePrize::class)->allowedFilters($filters)
            ->whereIn('status', UserRebatePrize::$paymentShowStatuses)
            ->latest()
            ->get();

        $headings = [
            'NO',
            'Rebate Date',
            'Product',
            'Rebate Code',
            'Member Code',
            'Currency',
            'Risk ID',
            'Gross Eligible Stake Amt',
            'Balance Eligible Stake Amt',
            'Balance Bonus Rolover',
            'Multiplier',
            'Rebate Amt',
            'Rebate Computation Date',
            'Initiate Payout',
            'Initiate Payout By',
            'Initiate Payout Date',
        ];

        $exportData = [];
        foreach ($prizes as $prize) {
            $exportData[] = [
                'id'                    => $prize->id,
                'date'                  => $prize->date,
                'product_code'          => $prize->product_code,
                'rebate_code'           => $prize->rebate_code,
                'user_name'             => $prize->user_name,
                'currency'              => $prize->currency,
                'display_risk_group_id' => transfer_show_value($prize->risk_group_id, RiskGroup::getDropList()),
                'effective_bet'         => $prize->effective_bet,
                'close_bonus_bet'       => $prize->close_bonus_bet,
                'calculate_rebate_bet'  => $prize->calculate_rebate_bet,
                'multipiler'            => thousands_number($prize->multipiler),
                'prize'                 => $prize->prize,
                'created_at'            => convert_time($prize->created_at),
                'display_status'        => $prize->isWaitingPaymentSend() ? 'No' : 'Yes',
                'payment_admin_name'    => $prize->payment_admin_name,
                'payment_sent_at'       => convert_time($prize->payment_sent_at),
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'auto_rebate_initiate_payout_report.xlsx');
    }

    /**
     * @OA\Patch(
     *      path="/backstage/user_rebate_prizes/marketing_send",
     *      operationId="backstage.user_rebate_prizes.marketing_send",
     *      tags={"Backstage-返点"},
     *      summary="Marketing派发返点",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_rebate_prize_ids", type="array", description="会员返点id", @OA\Items()),
     *                  required={"user_rebate_prize_ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content.",
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function marketingSend(UserRebatePrizeRequest $request)
    {
        UserRebatePrize::query()->whereIn('id', $request->user_rebate_prize_ids)
            ->where('is_manual_send', true)
            ->where('status', UserRebatePrize::STATUS_WAITING_MARKET_SEND)
            ->update([
                'status'                => UserRebatePrize::STATUS_WAITING_PAYMENT_SEND,
                'marketing_admin_name'  => $this->user->name,
                'marketing_sent_at'     => now(),
            ]);

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/user_rebate_prizes/payment_send",
     *      operationId="backstage.user_rebate_prizes.payment_send",
     *      tags={"Backstage-返点"},
     *      summary="Payment派发返点",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_rebate_prize_ids", type="array", description="会员返点id", @OA\Items()),
     *                  required={"user_rebate_prize_ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="No Content",
     *          @OA\JsonContent(
     *              type="object",
     *              @OA\Property(property="fail_ids", description="失败的返点id", type="array", @OA\Items()),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function paymentSend(UserRebatePrizeRequest $request)
    {
        $userRebatePrizes = UserRebatePrize::query()->whereIn('id', $request->user_rebate_prize_ids)->get();

        $failUserRebatePrizes = [];

        foreach ($userRebatePrizes as $userRebatePrize) {

            if (!$userRebatePrize->isWaitingPaymentSend()) {
                $failUserRebatePrizes[] = $userRebatePrize->id;
                continue;
            }

            try {
                $transaction = DB::transaction(function() use ($userRebatePrize) {
                    if (UserRebatePrizeRepository::setSuccess($userRebatePrize, $this->user->name)) {
                        return app(TransactionService::class)->addTransaction(
                            $userRebatePrize->user,
                            $userRebatePrize->prize,
                            Transaction::TYPE_REBATE_PRIZE,
                            $userRebatePrize->id
                        );
                    }

                    return null;
                });

            } catch (\Exception $exception) {
                $failUserRebatePrizes[] = $userRebatePrize->id;
                continue;
            }

            if ($transaction) {

                dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
            }
        }

        return $this->response->array(['fail_ids' => $failUserRebatePrizes]);
    }
}
