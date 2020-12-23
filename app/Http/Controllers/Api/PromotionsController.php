<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Api\PromotionRequest;
use App\Models\Promotion;
use App\Models\PromotionClaimUser;
use App\Services\PromotionService;
use App\Transformers\PromotionClaimUserTransformer;
use App\Transformers\PromotionTransformer;
use Illuminate\Http\Request;

class PromotionsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/promotion_types/{code}/promotions",
     *      operationId="api.promotions.index",
     *      tags={"Api-优惠"},
     *      summary="优惠列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="code", in="path", description="优惠类型code", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Promotion"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function index(Request $request)
    {
        $now      = now();
        $currency = $request->header('currency');
        $code     = $request->code;

        $promotions = Promotion::getAll()->where('status', true)
            ->where('is_agent', false)
            ->sortByDesc('sort')
            ->filter(function ($value) use ($currency, $now, $code) {

                if (!empty($value->display_start_at) && $value->display_start_at > $now) {
                    return false;
                }

                if (!empty($value->display_end_at) && $value->display_end_at < $now) {
                    return false;
                }

                if (!in_array($code, $value->show_types)) {
                    return false;
                }

                return $value->checkCurrencySet($currency);
            })
            ->map(function ($value) {

                $value->is_claimed = false;

                if ($this->user) {
                    $value->is_claimed = PromotionClaimUser::isClaimed($value->id, $this->user->id);
                }

                return $value;
            });

        return $this->response->collection($promotions, new PromotionTransformer('front_index'));
    }

    /**
     * @OA\Get(
     *      path="/promotions",
     *      operationId="api.promotions.all_index",
     *      tags={"Api-优惠"},
     *      summary="优惠列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="promotion_type_code", in="query", description="优惠类型code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="is_agent", in="query", description="是否是代理", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Promotion"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function allIndex(Request $request)
    {
        $now      = now();
        $currency = $request->header('currency');
        $isAgent = $request->input('is_agent', false);

        $promotions = Promotion::getAll();

        if ($request->has('promotion_type_code')) {
            $promotions = $promotions->where('promotion_type_code', $request->route('code'));
        }

        $promotions = $promotions->where('display_start_at', '<=', $now)
            ->where('display_end_at', '>=', $now)
            ->sortByDesc('sort')
            ->where('status', true)
            ->where('is_agent', $isAgent)
            ->filter(function ($value) use ($currency) {
                return $value->checkCurrencySet($currency);
            })
            ->map(function ($value) {

                $value->is_claimed = false;

                if ($this->user) {
                    $value->is_claimed = PromotionClaimUser::isClaimed($value->id, $this->user->id);
                }

                return $value;
            });

        return $this->response->collection($promotions, new PromotionTransformer('front_index'));
    }

    /**
     * @OA\Get(
     *      path="/promotions/{promotion}",
     *      operationId="api.promotions.show",
     *      tags={"Api-优惠"},
     *      summary="优惠详情",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="code", in="path", description="优惠类型code", @OA\Schema(type="string")),
     *      @OA\Parameter(name="promotion", in="path", description="优惠id", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Promotion")
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function show(Promotion $promotion, Request $request)
    {
        $promotion->is_claimed = false;

        if ($this->user) {
            $promotion->is_claimed = PromotionClaimUser::isClaimed($promotion->id, $this->user->id);
        }
        return $this->response->item($promotion, new PromotionTransformer('front_show'));
    }

    /**
     * @OA\Post(
     *      path="/promotions/{promotion}/claim",
     *      operationId="api.bonuses.claim",
     *      tags={"Api-优惠"},
     *      summary="申请红利",
     *      @OA\Parameter(name="promotion", in="path", description="优惠id", @OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="code", type="string", description="关联code"),
     *                  @OA\Property(property="front_remark", type="string", description="会员备注"),
     *                  required={"front_remark"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *         response=201,
     *         description="successful operation",
     *         @OA\JsonContent(ref="#/components/schemas/PromotionClaimUser"),
     *      ),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function claim(Promotion $promotion, PromotionRequest $request, PromotionService $promotionService)
    {
        if (!$promotion->isUser()) {
            return $this->response->errorNotFound();
        }

        $relatedModel = null;
        if ($request->code && !empty($promotion->related_type) && !$relatedModel = (new PromotionService())->getRelatedModel($promotion->related_type, $request->code)) {
            return $this->response->errorNotFound();
        }
        $promotionClaimUser = $promotionService->claim($promotion, $this->user, $relatedModel, $request->front_remark);

        return $this->response->item($promotionClaimUser, new PromotionClaimUserTransformer())->setStatusCode(201);
    }
}
