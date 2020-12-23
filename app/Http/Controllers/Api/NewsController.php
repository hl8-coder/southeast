<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\ApiController;
use App\Models\News;
use App\Transformers\NewsTransformer;
use Illuminate\Http\Request;

class NewsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/news",
     *      operationId="api.news.index",
     *      tags={"Api-资讯"},
     *      summary="获取新闻列表",
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
     *          @OA\JsonContent(ref="#/components/schemas/News")
     *       )
     *     )
     */
    public function index(Request $request)
    {
        $news = News::query()->where('currency', $request->header('currency'))
            ->enable()
            ->sortByDesc()
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($news, new NewsTransformer());
    }

    /**
     * @OA\Get(
     *      path="/news/{news}",
     *      operationId="api.news.show",
     *      tags={"Api-资讯"},
     *      summary="获取新闻详情",
     *       @OA\Parameter(
     *         name="news",
     *         in="path",
     *         description="新闻id",
     *         required=true,
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/News")
     *       ),
     *       @OA\Response(response=404, description="Not Found")
     *     )
     */
    public function show(News $news)
    {
        return $this->response->item($news, new NewsTransformer());
    }
}
