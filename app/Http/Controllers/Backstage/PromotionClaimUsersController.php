<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\PromotionClaimUserRequest;
use App\Models\PromotionClaimUser;
use App\Transformers\PromotionClaimUserTransformer;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Http\Request;

class PromotionClaimUsersController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/promotion_claim_users?include=user",
     *      operationId="backstage.promotion_claim_users.index",
     *      tags={"Backstage-优惠"},
     *      summary="优惠申请列表",
     *      @OA\Parameter(name="filter[user_name]", in="query", description="会员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[related_code]", in="query", description="关联code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[promotion_code]", in="query", description="优惠code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="查询结束时间", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/PromotionClaimUser"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $claimUsers = QueryBuilder::for(PromotionClaimUser::class)
                ->allowedFilters(
                    Filter::exact('user_name'),
                    Filter::exact('related_code'),
                    Filter::exact('promotion_code'),
                    Filter::scope('currency'),
                    Filter::scope('start_at'),
                    Filter::scope('end_at')
                )
                ->latest()
                ->paginate($request->per_page);

        return $this->response->paginator($claimUsers, new PromotionClaimUserTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/promotion_claim_users/{promotion_claim_user}/status",
     *      operationId="backstage.promotion_claim_users.status",
     *      tags={"Backstage-优惠"},
     *      summary="更改优惠申请会员状态",
     *      @OA\Parameter(
     *          name="promotion_claim_user",
     *          in="path",
     *          description="优惠会员id",
     *          @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="status", type="integer", description="状态 2:approve 3:reject"),
     *                  required={"status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204, description="No Content",),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function updateStatus(PromotionClaimUser $promotionClaimUser, PromotionClaimUserRequest $request)
    {
        if (!$promotionClaimUser->promotion->isNeedVerified()) {
            return $this->response->error('No need verified', 422);
        }

        $promotionClaimUser->update([
            'status'        => $request->status,
            'admin_name'    => $this->user->name,
        ]);

        return $this->response->noContent();
    }
}
