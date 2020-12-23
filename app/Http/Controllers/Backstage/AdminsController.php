<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\AdminRequest;
use App\Http\Requests\Backstage\AddAdminRolesRequest;
use App\Models\Admin;
use Auth;
use App\Transformers\AdminTransformer;
use App\Services\AdminService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AdminsController extends BackstageController
{
    protected $service;

    public function __construct(AdminService $service)
    {
        $this->service = $service;
    }
    /**
     * @OA\Get(
     *      path="/backstage/admins",
     *      operationId="backstage.admins.index",
     *      tags={"Backstage-管理员"},
     *      summary="管理员列表",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Admin"),
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
        $admins = QueryBuilder::for(Admin::class)
            ->allowedFilters(Filter::exact('name'))
            ->orderBy('sort', 'desc')
            ->paginate($request->per_page);

        return $this->response->paginator($admins, new AdminTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/admins?include=roles",
     *      operationId="backstage.admins.store",
     *      tags={"Backstage-管理员"},
     *      summary="创建管理员",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="name", type="string", description="账号"),
     *                  @OA\Property(property="nick_name", type="string", description="昵称"),
     *                  @OA\Property(property="password", type="string", format="password", description="密码"),
     *                  @OA\Property(property="operate_password", type="string", format="password", description="操作密码"),
     *                  @OA\Property(property="description", type="string", format="password", description="描述"),
     *                  @OA\Property(property="language", type="string", description="语言 中文:zh-CN,英文:en"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="状态"),
     *                  @OA\Property(property="admin_role_ids", type="array", @OA\Items(), description="角色id"),
     *                  required={"name", "password", "language"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/Admin"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function store(AdminRequest $request, Admin $admin)
    {
        $data = remove_null($request->all());

        if (empty($data['operate_password'])) {
            $data['operate_password'] = bcrypt($data['password']);
        }
        empty($data['nick_name']) ? $data['nick_name'] = $data['name'] : null;
        $data['password'] = bcrypt($data['password']);

        $adminRoles = $request->only(['admin_role_ids']);

        $admin = DB::transaction(function() use($admin, $data, $adminRoles) {

            $admin = $admin->create($data);

            $admin->roles()->sync($adminRoles['admin_role_ids'], true);

            return $admin;
        });


        return $this->response->item(Admin::find($admin->id), new AdminTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Patch(
     *      path="/backstage/admins/change/password",
     *      operationId="backstage.admins.change.password",
     *      tags={"Backstage-管理员"},
     *      summary="修改自己的登陆密码",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="password", type="string", description="新密码"),
     *                  @OA\Property(property="password_confirmation", type="string", description="确认新密码"),
     *                  required={"password","password_confirmation"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="Fail"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function changePassword(AdminRequest $request)
    {
        $password = $request->password;
        $this->user->password = bcrypt($password);
        $result = $this->user->update();
        if ($result){
            return $this->response->noContent();
        }
        return $this->response->error('Change Password Fail', 422);

    }


    /**
     * @OA\Patch(
     *      path="/backstage/admins/password/{admin}",
     *      operationId="backstage.admins.password.update",
     *      tags={"Backstage-管理员"},
     *      summary="修改管理员登陆密码",
     *     @OA\Parameter(name="admin",in="path",description="管理员id",@OA\Schema(type="integer")),
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="password", type="string", description="新密码"),
     *                  @OA\Property(property="password_confirmation", type="string", description="确认新密码"),
     *                  required={"password","password_confirmation"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="Fail"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function updatePassword(Admin $admin, AdminRequest $request)
    {
        $password = $request->password;
        $admin->password = bcrypt($password);
        $result = $admin->update();
        if ($result){
            return $this->response->noContent();
        }
        return $this->response->error('Change Password Fail', 422);

    }

    /**
     * @OA\Get(
     *      path="/backstage/admin?include=roles",
     *      operationId="backstage.admins.me",
     *      tags={"Backstage-管理员"},
     *      summary="获取登录管理员信息",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Admin"),
     *       ),
     *       @OA\Response(response=404, description="管理员不存在"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function me()
    {
        return $this->response->item($this->user(), new AdminTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/admins/{admin}",
     *      operationId="backstage.admins.delete",
     *      tags={"Backstage-管理员"},
     *      summary="删除管理员",
     *      @OA\Parameter(
     *         name="admin",
     *         in="path",
     *         description="管理员id",
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
    public function destroy(Admin $admin)
    {
        try{
            $admin->delete();
        }catch (\Exception $e){
            Log::info('admin logout' . $e->getMessage());
        }


        return $this->response->noContent();
    }

    /**
     * @OA\Post(
     *      path="/backstage/admins/{admin}/admin_roles?include=roles",
     *      operationId="backstage.admins.admin_roles.add",
     *      tags={"Backstage-管理员"},
     *      summary="更新管理员角色",
     *      @OA\Parameter(
     *          name="admin",
     *          in="path",
     *          description="管理员id",
     *          @OA\Schema(type="integer")
     *      ),
     *       @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="admin_role_ids", type="array", @OA\Items(), description="角色id"),
     *                  required={"admin_role_ids"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="更新成功",
     *          @OA\JsonContent(ref="#/components/schemas/Admin"),
     *       ),
     *       @OA\Response(response=404, description="管理员不存在"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function addAdminRoles(Admin $admin, AddAdminRolesRequest $request)
    {
        $admin->roles()->sync($request->admin_role_ids, true);

        return $this->response->item($admin, new AdminTransformer())->setStatusCode(201);
    }

    /**
     * @OA\Schema(
     *   schema="AdminMenu",
     *   type="object",
     *   @OA\Property(property="name", type="string", description="名称"),
     *   @OA\Property(property="code", type="string", description="唯一识别码"),
     *   @OA\Property(property="description", type="string", description="描述"),
     *   @OA\Property(property="is_show", type="string", description="是否在页面显示"),
     *   @OA\Property(property="actions", type="array", @OA\Items(ref="#/components/schemas/MenuAction"), description="功能权限"),
     *   @OA\Property(property="sub_menu", type="array",@OA\Items(ref="#/components/schemas/AdminMenu"), description="下层菜单(同菜单格式)"),
     * )
     */
    /**
     * @OA\Schema(
     *   schema="MenuAction",
     *   type="object",
     *   @OA\Property(property="id", type="string", description="id"),
     *   @OA\Property(property="name", type="string", description="名称"),
     *   @OA\Property(property="url", type="string", description="功能连结"),
     *   @OA\Property(property="drop_list_url", type="string", description="下拉列表连结"),
     *   @OA\Property(property="method", type="string", description="请求方式"),
     *   @OA\Property(property="action", type="string", description="功能代码"),
     *   @OA\Property(property="valid", type="string", description="是否有权限"),
     * )
     */
    /**
     * @OA\Get(
     *      path="/backstage/admins/menu",
     *      operationId="bo.authorizations.store",
     *      tags={"Backstage-管理员"},
     *      summary="管理员后台菜單",
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
    public function menu(Admin $admin)
    {
        # 取得管理员角色id清单
        $adminRoleIds = $this->user()->roles->pluck('id')->toArray();
        if ($this->user->is_super_admin){
            $isFull = true;
        }else{
            $isFull = false;
        }

        # 取得用户菜单内容
        $menu = $this->service->menu($adminRoleIds, $isFull);

        return $this->response->array($menu);
    }
}
