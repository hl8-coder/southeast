<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\UserBonusPrizeRequest;
use App\Models\Model;
use App\Models\Bonus;
use App\Models\UserBonusPrize;
use App\Transformers\UserBonusPrizeTransformer;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Exports\ExcelTemplateExport;


class UserBonusPrizesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/user_bonus_prizes/user_index",
     *      operationId="backstage.users.user_bonus_prizes.index",
     *      tags={"Backstage-红利"},
     *      summary="会员红利列表",
     *      @OA\Parameter(name="filter[bonus_code]", in="query", description="红利代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserBonusPrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userIndex(Request $request)
    {
        return $this->index($request, 'user');
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_bonus_prizes/report_index",
     *      operationId="backstage.user_bonus_prizes.index",
     *      tags={"Backstage-红利"},
     *      summary="报表红利列表",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[bonus_code]", in="query", description="红利代码", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[product_code]", in="query", description="产品code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="创建开始时间", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="创建结束时间", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserBonusPrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function reportIndex(Request $request)
    {
        return $this->index($request, 'report');
    }

    /**
     * @OA\Delete(
     *      path="/backstage/user_bonus_prizes/{user_bonus_prize}",
     *      operationId="backstage.user_bonus_prizes.close",
     *      tags={"Backstage-红利"},
     *      summary="关闭红利",
     *      @OA\Parameter(name="user_bonus_prize", in="path", description="会员红利奖励", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="remark", type="string", description="备注"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content",),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function close(UserBonusPrize $userBonusPrize, UserBonusPrizeRequest $request)
    {
        # 关闭流水同时需要关闭流水要求值
        $userBonusPrize->adminClose($this->user->name, $request->remark);

        return $this->response->noContent();
    }

    public function index(Request $request, $type='')
    {
        $query = QueryBuilder::for(UserBonusPrize::class)
            ->allowedFilters(
                Filter::exact('currency'),
                Filter::exact('user_name'),
                Filter::exact('bonus_code'),
                Filter::exact('product_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            );

        if ('user' == $type) {
            $query->where('status', UserBonusPrize::STATUS_SUCCESS);
        }

        $prizes = $query->with('user')->latest()->paginate($request->per_page);
        return $this->response->paginator($prizes, new UserBonusPrizeTransformer($type));
    }

    /**
     * @OA\Get(
     *      path="/backstage/user_bonus_prizes/user_index/export",
     *      operationId="backstage.users.user_bonus_prizes.export",
     *      tags={"Backstage-红利"},
     *      summary="会员红利列表",
     *      @OA\Parameter(name="filter[bonus_code]", in="query", description="红利代码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/UserBonusPrize"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function exportUserIndex(Request $request)
    {
        $headings = [
            'Member Code',
            'Currency',
            'Register Date',
            'Bonus Type',
            'Promo Code',
            'Products',
            'Bonus Amount',
            'Transfer Amount',
            'Total Deposit Amount',
        ];

        $prizes = QueryBuilder::for(UserBonusPrize::query())
            ->allowedFilters(
                Filter::exact('currency'),
                Filter::exact('user_name'),
                Filter::exact('bonus_code'),
                Filter::exact('product_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->where('status', UserBonusPrize::STATUS_SUCCESS)
            ->with('user')
            ->latest()
            ->get();

        $exportData = [];

        foreach ($prizes as $prize) {
            $exportData[] = [
                'member_code'          => $prize->user_name,
                'currency'             => $prize->currency,
                'registered_date'      => convert_time($prize->user->created_at),
                'bonus_type'           => transfer_show_value($prize->category, Bonus::$categories),
                'promo_code'           => $prize->bonus_code,
                'products'             => $prize->product_code,
                'bonus_amount'         => $prize->prize,
                'transfer_amount'      => thousands_number($prize->deposit_amount),
                'total_deposit_amount' => $prize->user->report ? thousands_number($prize->user->report->deposit) : 0,
            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'user_bonus_prize.xlsx');

    }

    public function exportPromotionCheckingTool(Request $request)
    {
        $headings = [
            'Member Code',
            'Currency',
            'Bonus Code',
            'Claim time',
            'Bonus Amount',
            'Rollover Amount',
            'Priciple Bet',
            'Meet Rollover',
            'Void',
            'By',
            'By Time',
        ];

        $prizes = QueryBuilder::for(UserBonusPrize::class)
            ->allowedFilters(
                Filter::exact('currency'),
                Filter::exact('user_name'),
                Filter::exact('bonus_code'),
                Filter::exact('product_code'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )->where('status', UserBonusPrize::STATUS_SUCCESS)
            ->with('user')
            ->latest()
            ->get();

        $exportData = [];

        foreach ($prizes as $prize) {
            $exportData[] = [
                'member_code'                => $prize->user_name,
                'currency'                   => $prize->currency,
                'bonus_code'                 => $prize->bonus_code,
                'created_at'                 => convert_time($prize->created_at),
                'bonus_amount'               => $prize->prize,
                'turnover_closed_value'      => $prize->turnover_closed_value,
                'turnover_current_value'     => $prize->turnover_current_value,
                'display_is_turnover_closed' => transfer_show_value($prize->is_turnover_closed, Model::$booleanDropList),
                'void'                       => !empty($prize->turnover_closed_admin_name) ? 'YES' : '',
                'turnover_closed_admin_name' => $prize->turnover_closed_admin_name,
                'turnover_closed_at'         => convert_time($prize->turnover_closed_at),

            ];
        }

        return Excel::download(new ExcelTemplateExport($exportData, $headings), 'user_bonus_prize.xlsx');
    }
}
