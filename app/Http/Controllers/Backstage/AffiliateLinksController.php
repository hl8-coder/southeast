<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\AffiliateLinkRequest;
use App\Models\AffiliateLink;
use App\Transformers\AffiliateLinkTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AffiliateLinksController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/affiliate_link",
     *      operationId="backstage.affiliate_link.show",
     *      tags={"Backstage-代理"},
     *      summary="Aff Link",
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[platform]", in="query", description="平台", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[start_at]", in="query", description="开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateLink"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $links = QueryBuilder::for(AffiliateLink::query())
            ->allowedFilters([
                Filter::exact('type'),
                Filter::exact('platform'),
                Filter::scope('start_at'),
                Filter::scope('end_at'),
                Filter::exact('status'),
            ])
            ->orderByDesc('sort')
            ->paginate($request->per_page);
        return $this->response->paginator($links, new AffiliateLinkTransformer());
    }

    /**
     * @OA\Post(
     *     path="/backstage/affiliate_link",
     *     operationId="backstage.affiliate_link.store",
     *     tags={"Backstage-代理"},
     *     summary="添加AFF Link",
     *     description="添加AFF Link",
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="integer", description="类型"),
     *                  @OA\Property(property="platform", type="integer", description="平台"),
     *                  @OA\Property(property="link", type="string", description="连接"),
     *                  @OA\Property(property="sort", type="string", description="排序"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items()),
     *                  @OA\Property(property="langauges", type="array", description="多语言",
     *                      @OA\Items(
     *                          @OA\Property(property="language", type="string", description="语言"),
     *                          @OA\Property(property="title", type="string", description="标题"),
     *                      )
     *                  ),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateLink"),
     *          ),
     *      ),
     * )
     */
    public function store(AffiliateLinkRequest $request)
    {
        $data = remove_null($request->all());

        $link = AffiliateLink::query()->create($data);

        return $this->response->item($link->refresh(), new AffiliateLinkTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Post(
     *     path="/backstage/affiliate_link/{affiliateLink}",
     *     operationId="backstage.affiliate_link.upadte",
     *     tags={"Backstage-代理"},
     *     summary="更新AFF Link",
     *     description="更新AFF Link",
     *     @OA\Parameter(
     *         name="affiliateLink",
     *         in="path",
     *         description="ID",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *     @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="integer", description="类型"),
     *                  @OA\Property(property="platform", type="integer", description="平台"),
     *                  @OA\Property(property="link", type="string", description="连接"),
     *                  @OA\Property(property="sort", type="string", description="排序"),
     *                  @OA\Property(property="status", type="integer", description="状态"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items()),
     *                  @OA\Property(property="langauges", type="array", description="多语言",
     *                      @OA\Items(
     *                          @OA\Property(property="language", type="string", description="语言"),
     *                          @OA\Property(property="title", type="string", description="标题"),
     *                      )
     *                  ),
     *              )
     *          )
     *     ),
     *     @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateLink"),
     *          ),
     *      ),
     * )
     */
    public function update(AffiliateLink $affiliateLink, AffiliateLinkRequest $request)
    {
        $data = remove_null($request->all());

        $data['admin_name'] = $this->user->name;

        $affiliateLink->update($data);

        return $this->response->item($affiliateLink->refresh(), new AffiliateLinkTransformer());
    }
}
