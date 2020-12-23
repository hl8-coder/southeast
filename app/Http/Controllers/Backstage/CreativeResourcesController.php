<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Requests\Backstage\CreativeResourceRequest;
use App\Models\Image;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use App\Models\CreativeResource;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Transformers\CreativeResourceTransformer;

class CreativeResourcesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/affiliate/creative_resources",
     *      operationId="backstage.affiliates.creative_resources.index",
     *      tags={"Backstage-代理"},
     *      summary="代理资源链接",
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tracking_name]", in="query", description="tracking_name", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[group]", in="query", description="组别", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[size]", in="query", description="尺寸", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[type]", in="query", description="类型", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CreativeResource"),
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
        $resources = QueryBuilder::for(CreativeResource::class)
            ->allowedFilters(
                Filter::exact('type'),
                Filter::scope('currency'),
                Filter::exact('size'),
                Filter::exact('group')
            )
            ->orderByDesc('created_at')
            ->paginate($request->per_page);

        return $this->response->paginator($resources, new CreativeResourceTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/affiliate/creative_resources",
     *      operationId="backstage.affiliates.creative_resources.store",
     *      tags={"Backstage-代理"},
     *      summary="添加代理资源链接",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="type", type="integer", description="类型"),
     *                  @OA\Property(property="group", type="integer", description="分组"),
     *                  @OA\Property(property="size", type="integer", description="尺寸大小"),
     *                  @OA\Property(property="tracking_id", type="integer", description="tracking_id"),
     *                  @OA\Property(property="banner_id", type="integer", description="图片ID"),
     *                  @OA\Property(property="banner_url", type="string", description="资源链接"),
     *                  @OA\Property(property="currency", type="string", description="币别"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CreativeResource"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(CreativeResourceRequest $request)
    {
        $data = remove_null($request->all());

        $data = $this->getBannerPath($data);

        $resource = CreativeResource::query()->create($data);

        return $this->response->item($resource, new CreativeResourceTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/affiliate/creative_resources/{resource}",
     *      operationId="backstage.affiliates.creative_resources.update",
     *      tags={"Backstage-代理"},
     *      summary="更新代理资源链接",
     *      @OA\Parameter(
     *         name="resource",
     *         in="path",
     *         description="代理资源id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CreativeResource"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function update(CreativeResource $resource,CreativeResourceRequest $request)
    {
        $user = $this->user();
        $data = remove_null($request->all());

        $data = $this->getBannerPath($data);

        $data['last_update_by'] = $user->name;

        $resource->update($data);

        return $this->response->item($resource, new CreativeResourceTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/affiliate/creative_resources/{resource}",
     *      operationId="backstage.affiliates.creative_resources.update",
     *      tags={"Backstage-代理"},
     *      summary="删除代理资源链接",
     *      @OA\Parameter(
     *         name="resource",
     *         in="path",
     *         description="代理资源id",
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
     *  )
     */
    public function destroy(CreativeResource $resource)
    {
        $resource->delete();
        return $this->response->noContent();
    }

    public function getBannerPath($data)
    {
        if (!empty($data['banner_id'])) {
            $data['banner_path'] = Image::find($data['banner_id'])->path;
        }

        return $data;
    }
}
