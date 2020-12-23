<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Action;
use App\Models\AdminRole;
use App\Models\Route;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use App\Http\Requests\ActionRequest;
use Spatie\QueryBuilder\QueryBuilder;
use App\Transformers\ActionTransformer;
use App\Http\Controllers\BackstageController;

class ActionsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/actions?include=menu",
     *      operationId="api.backstage.actions.index",
     *      tags={"Backstage-管理员操作"},
     *      summary="管理员操作列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Action"),
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
        $actions = QueryBuilder::for(Action::class)
            ->allowedFilters(
                Filter::exact('method'),
                Filter::exact('menu_id'),
                'name'
            )->paginate($request->per_page);
        return $this->response->paginator($actions, new ActionTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/actions?include=menu",
     *      operationId="backstage.actions.store",
     *      tags={"Backstage-管理员操作"},
     *      summary="创建管理员操作",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="menu_id", type="integer", description="菜单id"),
     *                  @OA\Property(property="name", type="string", description="操作名称"),
     *                  @OA\Property(property="action", type="string", description="操作"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  required={"menu_id", "name", "action"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Action"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function store(ActionRequest $request)
    {
        $data = remove_null($request->all());

        $action = Action::query()->create($data);

        return $this->response->item(Action::find($action->id), new ActionTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/actions/{action}?include=menu",
     *      operationId="backstage.actions.update",
     *      tags={"Backstage-管理员操作"},
     *      summary="创建管理员操作",
     *      @OA\Parameter(
     *         name="action",
     *         in="path",
     *         description="管理员操作id",
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
     *                  @OA\Property(property="menu_id", type="integer", description="菜单id"),
     *                  @OA\Property(property="name", type="string", description="操作名称"),
     *                  @OA\Property(property="action", type="string", description="操作"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Action"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function update(Action $action, ActionRequest $request)
    {
        $acceptable = array_keys($request->rules());
        $data       = remove_null($request->all($acceptable));
        try{
            $action->update($data);
        }catch (\Exception $exception){
            return $this->response()->errorBadRequest('Menu and Route relation is exists!');
        }

        return $this->response->item($action, new ActionTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/actions/{action}",
     *      operationId="backstage.actions.delete",
     *      tags={"Backstage-管理员操作"},
     *      summary="管理员操作",
     *      @OA\Parameter(
     *         name="action",
     *         in="path",
     *         description="管理员操作id",
     *         @OA\Schema(
     *             type="integer"
     *         )
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *           {"bearer": {}}
     *      }
     * )
     */
    public function destroy(Action $action)
    {
        $action->delete();

        return $this->response->noContent();
    }


    /**
     * @OA\Get(
     *      path="/backstage/{admin_role}/action/list",
     *      operationId="backstage.action.list",
     *      tags={"Backstage-管理员操作"},
     *      summary="可用权限列表",
     *      @OA\Response(
     *          response=200,
     *          description="successful operation",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AdminMenu"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *           {"bearer": {}}
     *       }
     * )
     */
    public function actionList(AdminRole $adminRole, Request $request)
    {
        # 取得角色id清单
        $menu = app(AdminService::class)->menu([$adminRole->id], true);
        return $this->response->array($menu);
    }


    /**
     * @OA\Post(
     *      path="/backstage/actions/menu_route?include=menu",
     *      operationId="backstage.actions.store.menu_route",
     *      tags={"Backstage-管理员操作"},
     *      summary="通过匹配路由创建管理员操作",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="menu_id", type="integer", description="菜单id"),
     *                  @OA\Property(property="route_id", type="integer", description="路由ID"),
     *                  required={"menu_id", "route_id"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Action"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function storeAction(ActionRequest $request, Action $action)
    {
        $menuId  = $request->input('menu_id');
        $routeId = $request->input('route_id');
        $route   = Route::query()->find($routeId);

        $action->menu_id = $menuId;
        $action->name    = $route->name;
        $action->method  = $route->method;
        $action->action  = $route->action;
        $action->remark  = $route->remark;
        $action->url     = $route->url;

        try{
            $action->save();
        }catch (\Exception $exception){
            return $this->response()->errorBadRequest('Menu and Route relation is exists!');
        }
        $action->refresh();
        return $this->response()->item($action, new ActionTransformer());
    }

}
