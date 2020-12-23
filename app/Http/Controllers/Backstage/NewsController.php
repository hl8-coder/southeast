<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\NewsRequest;
use App\Models\News;
use App\Transformers\NewsSimpleTransformer;
use App\Transformers\NewsTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\QueryBuilder;
use Spatie\QueryBuilder\Filter;

class NewsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/news",
     *      operationId="backstage.news.index",
     *      tags={"Backstage-资讯"},
     *      summary="获取新闻列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/News"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function index(Request $request)
    {
        $news = QueryBuilder::for(News::class)
            ->allowedFilters(Filter::exact('currency'))
            ->sortByDesc()
            ->latest()
            ->paginate($request->per_page);

        return $this->response->paginator($news, new NewsTransformer('index'));
    }

    /**
     * @OA\Get(
     *      path="/backstage/news/{news}",
     *      operationId="backstage.news.show",
     *      tags={"Backstage-资讯"},
     *      summary="获取新闻详情",
     *      @OA\Parameter(
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
     *      ),
     *      @OA\Response(response=403, description="新闻不存在"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function show(News $news)
    {
        return $this->response->item($news, new NewsTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/news",
     *      operationId="backstage.news.store",
     *      tags={"Backstage-资讯"},
     *      summary="添加新闻",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="currency", type="string", description="国家名称"),
     *                  @OA\Property(property="title", type="string", description="标题"),
     *                  @OA\Property(property="content", type="string", description="内容"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"country_name", "title", "content"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/News"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(NewsRequest $request)
    {
        $data = remove_null($request->all());

        $news = News::query()->create($data);

        return $this->response->item(News::find($news->id), new NewsTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/news/{news}",
     *      operationId="backstage.news.update",
     *      tags={"Backstage-新闻"},
     *      summary="更新新闻",
     *      @OA\Parameter(
     *         name="news",
     *         in="path",
     *         description="新闻id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="country_name", type="string", description="国家名称"),
     *                  @OA\Property(property="title", type="string", description="标题"),
     *                  @OA\Property(property="content", type="string", description="内容"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/News"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function update(News $news, NewsRequest $request)
    {
        $data = remove_null($request->all());

        $news->update($data);

        return $this->response->item($news, new NewsTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/news/{news}",
     *      operationId="api.news.delete",
     *      tags={"Backstage-新闻"},
     *      summary="删除新闻",
     *      @OA\Parameter(
     *         name="news",
     *         in="path",
     *         description="新闻id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function destroy(News $news)
    {
        $news->delete();
        return $this->response->noContent();
    }
}
