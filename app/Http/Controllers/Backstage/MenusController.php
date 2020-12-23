<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\MenuRequest;
use App\Models\Menu;
use App\Services\AdminService;
use App\Transformers\MenuTransformer;
use Illuminate\Support\Facades\DB;

class MenusController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/menus?include=children,chilren.chilren",
     *      operationId="api.backstage.menus.index",
     *      tags={"Backstage-菜单"},
     *      summary="菜单列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Menu"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index()
    {
        $menus = app(AdminService::class)->menu([], true);

        return $this->response()->array($menus);
    }
    /**
     * @OA\Get(
     *      path="/backstage/menus/{menu}?include=children",
     *      operationId="api.backstage.menus.show",
     *      tags={"Backstage-菜单"},
     *      summary="菜单列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Menu"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show(Menu $menu)
    {
        return $this->response()->item($menu, new MenuTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/menus",
     *      operationId="backstage.menus.store",
     *      tags={"Backstage-菜单"},
     *      summary="添加菜单",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="菜单名称"),
     *                  @OA\Property(property="parent_id", type="integer", description="上级id"),
     *                  @OA\Property(property="path", type="string", description="地址"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="is_show", type="integer", description="是否在页面显示"),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Menu"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function store(MenuRequest $request)
    {
        $data = remove_null($request->all());

        $menu = Menu::query()->create($data);

        return $this->response->item(Menu::find($menu->id), new MenuTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/menus/{menu}",
     *      operationId="backstage.menus.update",
     *      tags={"Backstage-菜单"},
     *      summary="更新菜单",
     *      @OA\Parameter(
     *         name="menu",
     *         in="path",
     *         description="菜单id",
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
     *                  @OA\Property(property="name", type="string", description="菜单名称"),
     *                  @OA\Property(property="parent_id", type="integer", description="上级id"),
     *                  @OA\Property(property="path", type="string", description="地址"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="is_show", type="integer", description="是否在页面显示"),
     *                  @OA\Property(property="description", type="string", description="描述"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Menu"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function update(Menu $menu, MenuRequest $request)
    {
        $data = remove_null($request->all(array_keys($request->rules())));

        $menu->update($data);

        return $this->response->item($menu, new MenuTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/menus/{menu}",
     *      operationId="backstage.menus.delete",
     *      tags={"Backstage-菜单"},
     *      summary="删除菜单",
     *      @OA\Parameter(
     *         name="menu",
     *         in="path",
     *         description="菜单id",
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
     *     )
     */
    public function destroy(Menu $menu)
    {
        $menu->delete();

        return $this->response->noContent();
    }
}
