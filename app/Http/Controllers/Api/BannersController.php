<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\Banner;
use App\Transformers\BannerTransformer;
use Illuminate\Http\Request;

class BannersController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/banners",
     *      operationId="backstage.banners.index",
     *      tags={"Api-资讯"},
     *      summary="轮播图列表",
     *      @OA\Parameter(name="currency", in="header", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="show_type", in="query", description="显示类型 1:捕鱼 2:老虎机 3:真人 4:体育 10:首页", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="is_agent", in="query", description="会员/代理显示", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Banner"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *  )
     */
    public function index(Request $request)
    {
        $now      = now();
        $banners  = Banner::getAll()
            ->where('currency', $request->header('currency'))
            ->where('show_type', $request->show_type)
            ->where('is_agent', $request->input('is_agent', false))
            ->where('display_start_at', '<=', $now)
            ->where('display_end_at', '>=', $now)
            ->sortByDesc('sort')
            ->where('status', true);

        return $this->response->collection($banners, new BannerTransformer('front_index'));
    }
}
