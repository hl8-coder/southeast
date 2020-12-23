<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Models\AffiliateAnnouncement;
use App\Transformers\AffiliateAnnouncementTransformer;
use Illuminate\Http\Request;
use App\Http\Controllers\ApiController;

class AffiliateAnnouncementsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliates/announcements",
     *      operationId="api.affiliate.announcements.index",
     *      tags={"Affiliate-代理"},
     *      summary="获取公告列表",
     *      @OA\Parameter(
     *         name="currency",
     *         in="header",
     *         description="币别",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Parameter(
     *         name="category",
     *         in="query",
     *         description="显示类型 1:Banking Option 2:Promotion 3:News",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/AffiliateAnnouncement")
     *       )
     *     )
     */
    public function index(Request $request)
    {
        $currency = $request->header('currency');

        $now = now()->toDateTimeString();

        $announcements = AffiliateAnnouncement::getAll()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->where('status', true)
            ->where('category', $request->category)
            ->filter(function ($value) use ($currency){
                return $value->checkCurrencySet($currency);
            })
            ->sortByDesc('sort')
            ->sortByDesc('created_at');

        return $this->response->collection($announcements, new AffiliateAnnouncementTransformer("font_index"));
    }
}
