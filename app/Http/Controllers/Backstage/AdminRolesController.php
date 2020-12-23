<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\AdminRoleRequest;
use App\Http\Requests\Backstage\AddActionsRequest;
use App\Models\AdminRole;
use App\Transformers\AdminRoleTransformer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use function GuzzleHttp\Promise\iter_for;

class AdminRolesController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/admin_roles?include=actions",
     *      operationId="api.backstage.admin_roles.index",
     *      tags={"Backstage-管理员角色"},
     *      summary="管理员角色列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/AdminRole"),
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
        $adminRoles = AdminRole::query()->latest()->paginate($request->per_page);

        return $this->response->paginator($adminRoles, new AdminRoleTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/admin_roles",
     *      operationId="backstage.admin_roles.store",
     *      tags={"Backstage-管理员角色"},
     *      summary="创建管理员角色",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="description", type="string", format="password", description="描述"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/AdminRole"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function store(AdminRoleRequest $request)
    {
        $data = remove_null($request->all());

        $adminRole = AdminRole::query()->create($data);

        return $this->response->item($adminRole, new AdminRoleTransformer())
            ->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/admin_roles/{adminRole}",
     *      operationId="backstage.admin_roles.update",
     *      tags={"Backstage-管理员角色"},
     *      summary="更新管理员角色",
     *       @OA\Parameter(
     *         name="admin_role",
     *         in="path",
     *         description="管理员角色id",
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
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="description", type="string", format="password", description="描述"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  required={"name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/AdminRole"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function update(AdminRole $adminRole, AdminRoleRequest $request)
    {
        $data = remove_null($request->all());

        $adminRole->update($data);

        return $this->response->item($adminRole, new AdminRoleTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/admin_roles/{admin_role}",
     *      operationId="backstage.admin_role.delete",
     *      tags={"Backstage-管理员角色"},
     *      summary="管理员角色",
     *      @OA\Parameter(
     *         name="admin_role",
     *         in="path",
     *         description="管理员角色id",
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
    public function destroy(AdminRole $adminRole)
    {
        $adminRole->delete();

        return $this->response->noContent();
    }

    /**
     * @OA\Post(
     *      path="/backstage/admin_roles/{admin_role}/actions",
     *      operationId="backstage.admin_roles.actions.add",
     *      tags={"Backstage-管理员角色"},
     *      summary="管理员角色添加操作",
     *      @OA\Parameter(
     *          name="admin_role",
     *          in="path",
     *          description="管理员角色id",
     *          @OA\Schema(type="integer")
     *      ),
     *       @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="action_ids", type="array", @OA\Items(), description="操作id"),
     *                  required={"action_ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/AdminRole"),
     *       ),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function addActions(AdminRole $adminRole, AddActionsRequest $request)
    {
        $actionIds = $request->input('action_ids', []);
        $adminRole->actions()->sync($actionIds, true);

        return $this->response->item($adminRole, new AdminRoleTransformer());
    }
}
