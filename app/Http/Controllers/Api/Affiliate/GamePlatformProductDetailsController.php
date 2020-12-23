<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Models\GamePlatform;
use App\Models\GamePlatformProduct;
use App\Models\UserProductDailyReport;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GamePlatformProductDetailsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/game_platform_product_details/{detail}",
     *      operationId="api.affiliate.product_details.show",
     *      tags={"Affiliate-代理"},
     *      summary="产品详情",
     *      @OA\Parameter(
     *         name="detail",
     *         in="path",
     *         description="类型key",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="object",
     *                  @OA\Property(property="total_profit", type="string", description="总盈亏"),
     *                  @OA\Property(property="total_turnover", type="integer", description="总流水"),
     *                  @OA\Property(property="data", description="游戏列表", @OA\Items(
     *                  @OA\Property(property="code", type="string", description="产品code"),
     *                  @OA\Property(property="img", type="string", description="图片地址"),
     *                  @OA\Property(property="total_effective_profit", type="", description="公司盈亏"),
     *                  @OA\Property(property="active", type="number", description="活跃数量"),
     *                  @OA\Property(property="total_bet", type="number", description="总投注，暂时使用"),
     *                  @OA\Property(property="total_effective_bet", type="number", description="流水"),
     *              )),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request, $detail)
    {
        if (!empty($request->filter["month"])) {
            $start = Carbon::parse($request->filter["month"])->firstOfMonth()->toDateTimeString();
            $end   = Carbon::parse($request->filter["month"])->endOfMonth()->toDateTimeString();
        } else {
            $start = now()->firstOfMonth()->toDateTimeString();
            $end   = now()->endOfMonth()->toDateTimeString();
        }
        $affiliate     = $this->user()->affiliate;
        $subUserIds    = $affiliate->subUsers()->pluck('id');
        $products      = GamePlatformProduct::getAll()->where('type', $detail);
        $data          = [];
        $totalProfit   = 0;
        $totalTurnover = 0;
        foreach ($products as $product) {
            $info           = UserProductDailyReport::query()
                ->where([
                    [
                        'platform_code', $product->platform_code
                    ],
                    [
                        'product_code', $product->code
                    ],
                    [
                        'created_at', '>=', $start
                    ],
                    [
                        'created_at', '<', $end
                    ]
                ])
                ->whereIn('user_id', $subUserIds)
                ->select(DB::raw("sum(effective_bet) as total_effective_bet, sum(effective_profit) as total_effective_profit, sum(`bet_num`) as total_bet, COUNT(DISTINCT user_id) as active"))
                ->first();
            $totalProfit    += $info->total_effective_profit;
            $totalTurnover  += $info->total_effective_bet;
            $data['data'][] = [
                'code'                   => $product->code,
                'img'                    => $product->one_web_img_path,
                'total_effective_profit' => thousands_number($info->total_effective_profit),
                'active'                 => $info->active,
                'total_bet'              => $info->total_bet ? $info->total_bet : '0',
                'total_effective_bet'    => thousands_number($info->total_effective_bet),
            ];
        }
        $data['total_profit']   = thousands_number($totalProfit);
        $data['total_turnover'] = thousands_number($totalTurnover);
        return $data;
    }
}