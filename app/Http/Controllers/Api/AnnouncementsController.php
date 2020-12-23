<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Announcement;
use App\Transformers\AnnouncementTransformer;
use Illuminate\Http\Request;

class AnnouncementsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/announcements",
     *      operationId="api.announcements.index",
     *      tags={"Api-资讯"},
     *      summary="获取公告列表",
     *      @OA\Parameter(name="is_agent", in="query", description="是否为代理", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="is_login_pop_up", in="query", description="是否是获取登录弹窗", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="category", in="query", description="分类", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="pop_up", in="query", description="置顶时是否弹窗", @OA\Schema(type="integer")),
     *      @OA\Parameter(
     *         name="currency",
     *         in="header",
     *         description="币别",
     *         required=true,
     *         @OA\Schema(
     *             type="string"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Announcement")
     *       )
     *     )
     */
    public function index(Request $request)
    {
        $currency = $request->header('currency');
        $isAgent  = $request->input('is_agent', false);

        # 是否是登录时的公告
        $isLoginPopUp = $request->input('is_login_pop_up', false);

        $now = now()->toDateTimeString();

        $announcements = Announcement::getAll()
            ->where('start_at', '<=', $now)
            ->where('end_at', '>=', $now)
            ->where('status', true)
            ->where('is_agent', $isAgent)
            ->where('is_login_pop_up',$isLoginPopUp)
            ->sortByDesc('sort');

        if ($category = $request->input('category')) {
            $announcements = $announcements->where('category', $category);
        }

        if ($popUp = $request->input('pop_up')) {
            $announcements = $announcements->where('pop_up', $popUp);
        }

        $announcements = $announcements->filter(function($value) use ($currency, $now)  {
            # 币别
            if (!$value->checkCurrencySet($currency)) {
                return false;
            }

            if ($user = $this->user) {
                if ($value->show_type == Announcement::SHOW_TYPE_PAYMENT) {
                    return in_array($user->payment_group_id, $value->payment_group_ids);
                } elseif ($value->show_type == Announcement::SHOW_TYPE_VIP) {
                    return in_array($user->vip_id, $value->vips);
                }
            }

            return $value->show_type == Announcement::SHOW_TYPE_ALL;
        });


        return $this->response->collection($announcements, new AnnouncementTransformer('front_index'));
    }
}
