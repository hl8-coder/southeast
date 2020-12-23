<?php

namespace App\Http\Controllers\Backstage;

use Illuminate\Http\Request;
use App\Http\Requests\Backstage\AffiliateAnnouncementRequest;
use App\Models\AffiliateAnnouncement;
use App\Transformers\AffiliateAnnouncementTransformer;
use App\Http\Controllers\BackstageController;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class AffiliateAnnouncementsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/affiliate_announcements",
     *      operationId="backstage.affiliate_announcements.index",
     *      tags={"Backstage-代理公告"},
     *      summary="获取公告列表",
     *      @OA\Parameter(name="filter[category]", in="query", description="分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[pop_up]", in="query", description="弹窗", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AffiliateAnnouncement")
     *          )
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function index(Request $request)
    {
        $announcements = QueryBuilder::for(AffiliateAnnouncement::class)
                        ->allowedFilters(
                            Filter::exact('category'),
                            Filter::exact('status'),
                            Filter::exact('pop_up')
                        )
                        ->sortByDesc()
                        ->latest()
                        ->paginate($request->per_page);

        return $this->response->paginator($announcements, new AffiliateAnnouncementTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/affiliate_announcements/{affiliate_announcement}",
     *      operationId="backstage.affiliate_announcements.show",
     *      tags={"Backstage-代理公告"},
     *      summary="获取公告详情",
     *      @OA\Parameter(
     *         name="affiliate_announcements",
     *         in="path",
     *         description="公告id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/AffiliateAnnouncement")
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="公告不存在"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function show(AffiliateAnnouncement $affiliateAnnouncement)
    {
        return $this->response->item($affiliateAnnouncement, new AffiliateAnnouncementTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/affiliate_announcements",
     *      operationId="backstage.affiliate_announcements.store",
     *      tags={"Backstage-代理公告"},
     *      summary="添加公告",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="标题"),
     *                  @OA\Property(property="currencies", type="array", @OA\Items(),description="币别"),
     *                  @OA\Property(property="content", type="array", @OA\Items(
     *                      @OA\Property(property="language", type="string", description="语言"),
     *                      @OA\Property(property="message", type="string", description="内容"),
     *                  ),description="多语言内容"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="start_at", type="string", description="显示开始时间", format="date-time"),
     *                  @OA\Property(property="end_at", type="string", description="显示结束时间", format="date-time"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"currency", "name", "content", "display_type", "display_id", "category"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/AffiliateAnnouncement"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(AffiliateAnnouncementRequest $request)
    {
        $data = remove_null($request->all());

        $data['admin_name'] = $this->user->name;

        $announcement = AffiliateAnnouncement::query()->create($data);

        return $this->response->item($announcement->refresh(), new AffiliateAnnouncementTransformer())->setStatusCode(201);
    }

     /**
     * @OA\Put(
     *      path="/backstage/affiliate_announcements/{affiliate_announcement}",
     *      operationId="backstage.affiliate_announcements.update",
     *      tags={"Backstage-代理公告"},
     *      summary="更新公告",
     *      @OA\Parameter(
     *         name="affiliate_announcement",
     *         in="path",
     *         description="公告id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="标题"),
     *                  @OA\Property(property="currencies", type="array", @OA\Items(), description="币别"),
      *                  @OA\Property(property="content", type="array", @OA\Items(
      *                      @OA\Property(property="language", type="string", description="语言"),
      *                      @OA\Property(property="message", type="string", description="内容"),
      *                  ),description="多语言内容"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="start_at", type="string", description="显示开始时间", format="date-time"),
     *                  @OA\Property(property="end_at", type="string", description="显示结束时间", format="date-time"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/AffiliateAnnouncement"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function update(AffiliateAnnouncement $affiliateAnnouncement, AffiliateAnnouncementRequest $request)
    {
        $data = remove_null($request->all());

        $data['admin_name'] = $this->user->name;

        $affiliateAnnouncement->update($data);

        return $this->response->item($affiliateAnnouncement, new AffiliateAnnouncementTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/affiliate_announcements/{affiliate_announcement}",
     *      operationId="backstage.affiliate_announcements.delete",
     *      tags={"Backstage-代理公告"},
     *      summary="删除公告",
     *      @OA\Parameter(
     *         name="affiliate_announcement",
     *         in="path",
     *         description="公告id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function destroy(AffiliateAnnouncement $affiliateAnnouncement)
    {
        $affiliateAnnouncement->delete();
        return $this->response->noContent();
    }
}
