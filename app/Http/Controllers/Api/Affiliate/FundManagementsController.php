<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Models\TransferDetail;
use App\Transformers\TransferDetailTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class FundManagementsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/fund_managements",
     *      operationId="api.affiliate.fund_managements.index",
     *      tags={"Affiliate-代理"},
     *      summary="代理转账",
     *      @OA\Parameter(name="filter[status]", in="query", description="用户名", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[chanel]", in="query", description="渠道", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[is_agent]", in="query", description="是否是代理", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="注册查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="注册查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TransferDetail"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $affiliate = $this->user();
        $funds     = QueryBuilder::for(TransferDetail::class)
            ->where("user_id", $affiliate->id)
            ->orderBy("id", "desc")
            ->allowedFilters(
                Filter::exact('status'),
                Filter::scope('chanel'),
                Filter::scope('is_agent'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->paginate($request->per_page);

        return $this->response->paginator($funds, new TransferDetailTransformer('affiliate_fund_managements'));
    }
}
