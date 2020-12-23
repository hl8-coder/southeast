<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Models\Route;
use App\Transformers\RouteTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;
use Spatie\QueryBuilder\QueryBuilder;

class RoutesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/routes",
     *      operationId="api.backstage.routes.index",
     *      tags={"Backstage-管理员操作"},
     *      summary="路由列表",
     *      @OA\Parameter(name="filter[url]", in="query", description="url", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[name]", in="query", description="路由名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[remark]", in="query", description="代码路径", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[action]", in="query", description="路由别名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[method]", in="query", description="请求方式", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Route"),
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
        $data = QueryBuilder::for(Route::class)
            ->allowedFilters(
                'url',
                'name',
                'remark',
                'action',
                'method'
                )
            ->paginate($request->per_page);
        return $this->response()->paginator($data, new RouteTransformer());

    }
    /**
     * @OA\Get(
     *      path="/backstage/routes/{route}",
     *      operationId="api.backstage.routes.show",
     *      tags={"Backstage-管理员操作"},
     *      summary="路由详情",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Route"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show(Route $route, Request $request)
    {
        return $this->response()->item($route, new RouteTransformer());

    }


    /**
     * @OA\Get(
     *      path="/backstage/routes/list",
     *      operationId="api.backstage.routes.routeList",
     *      tags={"Backstage-管理员操作"},
     *      summary="管理员操作列表",
     *      @OA\Parameter(name="filter[url]", in="query", description="url", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[name]", in="query", description="路由名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[remark]", in="query", description="代码路径", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[action]", in="query", description="路由别名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[method]", in="query", description="请求方式", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Route"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function routeList(Request $request)
    {
        $content = QueryBuilder::for(Route::class)
            ->allowedFilters(
                'url',
                'remark',
                'name',
                'action',
                'method'
            )
            ->get();
        return $this->response()->collection($content, new RouteTransformer());
    }


    /**
     * @OA\Patch(
     *      path="/backstage/routes/update",
     *      operationId="api.backstage.routes.routeUpdate",
     *      tags={"Backstage-管理员操作"},
     *      summary="管理员操作列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function routeUpdate(Request $request)
    {
        Artisan::call('southeast:update-routes');
        return $this->response()->noContent();
    }
}
