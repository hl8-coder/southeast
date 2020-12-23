<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Models\AffiliateLink;
use App\Transformers\AffiliateLinkTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AffiliateLinksController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/affiliate_links",
     *      operationId="api.affiliate.affiliate_links.index",
     *      tags={"Api-代理"},
     *      summary="连接",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="type", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="platform", in="query", description="平台", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateLink"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function index(Request $request)
    {

        $links = QueryBuilder::for(AffiliateLink::query())
            ->allowedFilters([
                Filter::exact('type'),
                Filter::exact('platform'),
            ])
            ->orderByDesc('sort')
            ->get();
        $currency = $request->header('currency');
        $links = $links->filter(function($value) use ($currency) {

            return in_array($currency, $value->currencies);
        });;
        return $this->response->collection($links, new AffiliateLinkTransformer('front_index'));
    }
}
