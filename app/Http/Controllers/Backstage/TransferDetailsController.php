<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\TransferDetail;
use App\Transformers\TransferDetailTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class TransferDetailsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/transfer_details",
     *      operationId="backstage.transfer_detail.index",
     *      tags={"Backstage-会员账户"},
     *      summary="会员转账列表",
     *      @OA\Parameter(name="user_name", in="query", description="上级名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="to_user_name", in="query", description="下级名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="status", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="order_no", in="query", description="订单号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="start_at", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_at", in="query", description="查询结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TransferDetail"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $isAgent = $request->is_agent ?? false;
        $transactions = QueryBuilder::for(TransferDetail::class)
                        ->allowedFilters([
                            Filter::exact('user_name'),
                            Filter::exact('to_user_name'),
                            Filter::scope('start_at'),
                            Filter::scope('end_at'),
                            Filter::exact('order_no'),
                            Filter::exact('status'),
                        ])
                        ->isAgent($isAgent)
                        ->latest()
                        ->get();

        $transactionPages = $this->paginate($request, $transactions);

        return $this->response->paginator($transactionPages, new TransferDetailTransformer())->addMeta('total', [
            'amount'       => thousands_number($transactions->where('status', TransferDetail::STATUS_SUCCESS)->sum('amount')),
            'transactions' => $transactionPages->count(),
        ]);
    }
}
