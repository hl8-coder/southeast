<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\AnnouncementRequest;
use App\Models\Announcement;
use App\Models\Image;
use App\Transformers\AnnouncementTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class AnnouncementsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/announcements",
     *      operationId="backstage.announcements.index",
     *      tags={"Backstage-资讯"},
     *      summary="获取公告列表",
     *      @OA\Parameter(name="filter[category]", in="query", description="分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[pop_up]", in="query", description="置顶时是否弹窗", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[is_agent]", in="query", description="是否为代理", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Announcement")
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
        $announcements = QueryBuilder::for(Announcement::class)
                        ->allowedFilters(
                            Filter::exact('category'),
                            Filter::exact('is_agent'),
                            Filter::exact('status'),
                            Filter::exact('pop_up'),
                            Filter::scope('currency')
                        )
                        ->sortByDesc()
                        // ->latest()
                        ->paginate($request->per_page);

        return $this->response->paginator($announcements, new AnnouncementTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/announcements",
     *      operationId="backstage.announcements.store",
     *      tags={"Backstage-资讯"},
     *      summary="添加公告",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="标题"),
     *                  @OA\Property(property="is_agent", type="boolean", description="是否是代理"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items()),
     *                  @OA\Property(property="content_type",type="integer",description="公告类型 文字 还是图片"),
     *                  @OA\Property(property="content", type="array", @OA\Items(
     *                      @OA\Property(property="language", type="string", description="币别"),
     *                      @OA\Property(property="message", type="string", description="内容"),
     *                      @OA\Property(property="mobile_img_id", type="integer", description="当content_type=2时 必须 对应语言的mobile弹窗图片"),
     *                      @OA\Property(property="web_img_id", type="integer", description="当content_type=2时 必须 对应语言的pc弹窗图片"),
     *                  ),description="多语言内容"),
     *                  @OA\Property(property="pop_up_setting", type="array", @OA\Items(
     *                      @OA\Property(property="mobile_redirect_url", type="string", description="当content_type=2时 必须 mobile弹窗点击跳转地址"),
     *                      @OA\Property(property="web_redirect_url", type="string", description="当content_type=2时 必须 web端弹窗点击跳转地址"),
     *                      @OA\Property(property="frequency", type="integer", description="当pop_up=1时 必须 弹窗频率 单位:分钟 比如 60 代表 60分钟弹窗一次后不会再弹"),
     *                      @OA\Property(property="delay_sec", type="integer", description="当pop_up=1时 必须 弹窗延时 单位:秒 当用户在具体的弹窗页面停留多少秒 才会执行弹窗"),
     *                  ),description="弹窗设置"),
     *                  @OA\Property(property="access_pop_mobile_urls", type="array", description="mobile端允许弹窗地址,前端相对路径,为空代表不限制弹窗的页面", @OA\Items()),
     *                  @OA\Property(property="access_pop_pc_urls", type="array", description="web端允许弹窗地址,前端相对路径,为空代表不限制弹窗页面", @OA\Items()),
     *                  @OA\Property(property="is_login_pop_up", type="boolean", description="是否为登录成功立马触发的弹窗类型 默认为false"),
     *                  @OA\Property(property="is_game", type="boolean", description="是否按照游戏方式跳转 默认为false"),
     *                  @OA\Property(property="show_type", type="integer", description="公告会员组别类型(all/group/vip)"),
     *                  @OA\Property(property="payment_group_ids", type="array", @OA\Items(), description="会员支付组别id数组"),
     *                  @OA\Property(property="vip_ids", type="array", @OA\Items(), description="vip id数组"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="start_at", type="string", description="显示开始时间", format="date-time"),
     *                  @OA\Property(property="end_at", type="string", description="显示结束时间", format="date-time"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="pop_up", type="boolean", description="置顶时是否弹窗"),
     *                  required={"currency", "name", "content", "show_type", "display_id", "category"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Announcement"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(AnnouncementRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $announcement = Announcement::query()->create($data);

        return $this->response->item($announcement, new AnnouncementTransformer())->setStatusCode(201);
    }

    private function getImagePath($data)
    {
        if (!empty($data['content']) && is_array($data['content'])) {
            foreach ($data['content'] as &$info) {
                if (!empty($info['web_img_id'])) {
                    $info['web_img_path'] = Image::find($info['web_img_id'])->path;
                    unset($info['web_img_id']);
                }

                if (!empty($info['mobile_img_id'])) {
                    $info['mobile_img_path'] = Image::find($info['mobile_img_id'])->path;
                    unset($info['mobile_img_id']);

                }
            }
        }

        return $data;
    }

    /**
     * @OA\Patch(
     *      path="/backstage/announcements/{announcement}",
     *      operationId="backstage.announcements.update",
     *      tags={"Backstage-资讯"},
     *      summary="更新公告",
     *      @OA\Parameter(
     *         name="announcement",
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
     *                  @OA\Property(property="is_agent", type="string", description="是否为代理"),
     *                  @OA\Property(property="currencies", type="array", description="币别", @OA\Items()),
     *                  @OA\Property(property="content", type="array", @OA\Items(
     *                      @OA\Property(property="language", type="string", description="语言"),
     *                      @OA\Property(property="message", type="string", description="内容"),
     *                  ),description="多语言内容"),
     *                  @OA\Property(property="show_type", type="integer", description="公告会员组别类型(payment/vip)"),
     *                  @OA\Property(property="payment_group_ids", type="array", @OA\Items(), description="会员支付组别id数组"),
     *                  @OA\Property(property="vip_ids", type="array", @OA\Items(), description="vip id数组"),
     *                  @OA\Property(property="category", type="integer", description="分类"),
     *                  @OA\Property(property="start_at", type="string", description="显示开始时间", format="date-time"),
     *                  @OA\Property(property="end_at", type="string", description="显示结束时间", format="date-time"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="pop_up", type="boolean", description="置顶时是否弹窗"),
     *                  @OA\Property(property="is_game", type="boolean", description="是否按照游戏方式跳转 默认为false"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Announcement"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function update(Announcement $announcement, AnnouncementRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getImagePath($data);

        $data['admin_name'] = $this->user->name;

        $announcement->update($data);

        return $this->response->item($announcement, new AnnouncementTransformer());
    }
}
