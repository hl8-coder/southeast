<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Models\Affiliate;
use App\Models\AffiliateSubTransferBalance;
use App\Models\User;
use App\Transformers\AffiliateSubTransferBalanceTransformer;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class DownlineManagementController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/down_line_managements",
     *      operationId="api.affiliate.down_line_managements",
     *      tags={"Affiliate-代理"},
     *      summary="团队管理",
     *      @OA\Parameter(name="filter[name]", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[is_agent]", in="query", description="是否是代理", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="注册查询开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="注册查询结束日期", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/User"),
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
        $user = $this->user();
        $users = QueryBuilder::for(User::class)
            ->where("parent_id", $user->id)
            ->allowedFilters(
                'name',
                Filter::exact('is_agent'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->orderBy("id", "desc")
            ->paginate($request->per_page);

        return $this->response->paginator($users, new UserTransformer('down_line_managements'));
    }

    public function fundsIndex(Request $request)
    {
        $user = $this->user();
        $affiliate = Affiliate::where('user_id', $user->id)->first();
        $ORM = AffiliateSubTransferBalance::query();
        $ORM = $ORM->where('affiliate_id', $affiliate->id);
        $funds = QueryBuilder::for($ORM)
            ->orderBy("id", "desc")
            ->allowedFilters(
                Filter::scope('status'),
                Filter::scope('method'),
                Filter::scope('type'),
                Filter::scope('start_at'),
                Filter::scope('end_at')
            )
            ->paginate($request->per_page);

        return $this->response->paginator($funds, new AffiliateSubTransferBalanceTransformer());
    }
}
