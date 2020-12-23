<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\UrlRequest;
use App\Models\Url;
use App\Transformers\UrlTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class UrlsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/domain_management",
     *      operationId="backstage.domain_management.index",
     *      tags={"Backstage-平台"},
     *      summary="域名管理",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Urls"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(Request $request)
    {
        $urls = QueryBuilder::for(Url::class)
            ->paginate($request->per_page);

        return $this->response->paginator($urls, new UrlTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/domain_management",
     *      operationId="backstage.domain_management.store",
     *      tags={"Backstage-平台"},
     *      summary="添加域名",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type'", type="string", description="类型"),
     *                  @OA\Property(property="address", type="string", description="域名"),
     *                  @OA\Property(property="status", type="string", description="状态"),
     *                  @OA\Property(property="remark", type="integer", description="备注"),
     *                  required={"type", "address", "status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Urls"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function store(UrlRequest $request)
    {
        $data = remove_null($request->all());

        $url = Url::query()->create($data);

        return $this->response->item($url, new UrlTransformer())->setStatusCode(201);
    }


    /**
     * @OA\Post(
     *      path="/backstage/domain_management/{url}",
     *      operationId="backstage.domain_management.update",
     *      tags={"Backstage-平台"},
     *      summary="更新域名",
     *     @OA\Parameter(
     *         name="url",
     *         in="path",
     *         description="URL id",
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
     *                  @OA\Property(property="type'", type="string", description="类型"),
     *                  @OA\Property(property="address", type="string", description="域名"),
     *                  @OA\Property(property="status", type="string", description="状态"),
     *                  @OA\Property(property="remark", type="integer", description="备注"),
     *                  required={"type", "address", "status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/Urls"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function update(Url $url, UrlRequest $request)
    {
        $data = remove_null($request->all());
        $data['update_by'] = $this->user()->name;
        $url->update($data);

        return $this->response->item($url->refresh(), new UrlTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/domain_management/{url}",
     *      operationId="backstage.domain_management.update",
     *      tags={"Backstage-平台"},
     *      summary="删除域名",
     *     @OA\Parameter(
     *         name="url",
     *         in="path",
     *         description="URL id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function destroy(Url $url)
    {
        $url->delete();
        return $this->response->noContent();
    }
}
