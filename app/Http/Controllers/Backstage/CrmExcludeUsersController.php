<?php

namespace App\Http\Controllers\Backstage;

use App\Models\User;
use App\Models\CrmExcludeUser;
use App\Services\CrmService;
use App\Transformers\CrmExcludeUserTransformer;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\CrmExcludeUsersRequest;

class CrmExcludeUsersController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/crm_exclude_users",
     *      operationId="backstage.crm_exclude_users.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 黑名单列表",
     *      @OA\Parameter(name="filter[admin_id]", in="query", description="添加名单管理员ID", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="添加名单管理员用户名【模糊查找】", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="被添加用户的名称【模糊查找】", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[review_by]", in="query", description="名单审核者【模糊查找】", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[is_affiliate]", in="query", description="用户是否为代理", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[status]", in="query", description="是否审核通过", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[review_by]", in="query", description="名单审核者【模糊查找】", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmExcludeUser"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(CrmExcludeUsersRequest $request)
    {
        $data = QueryBuilder::for(CrmExcludeUser::class)
            ->allowedFilters(
                Filter::exact('admin_id'),
                Filter::exact('is_affiliate'),
                Filter::exact('status'),
                'admin_name',
                'user_name',
                'review_by'
            )->latest()
            ->paginate($request->per_page);
        return $this->response()->paginator($data, new CrmExcludeUserTransformer());
    }


    /**
     * @OA\Post(
     *      path="/backstage/crm_exclude_users",
     *      operationId="backstage.crm_exclude_users.store",
     *      tags={"Backstage-CRM"},
     *      summary="添加crm黑名单成员",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="user_name", type="string", description="用户名称"),
     *                  @OA\Property(property="is_affiliate", type="boolean", description="是否为代理"),
     *                  required={"user_name", "is_affiliate"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    // 只需要 user_name is_affiliate
    public function store(CrmExcludeUsersRequest $request, CrmExcludeUser $crmExcludeUser)
    {
        $admin = auth('admin')->user();

        $user = QueryBuilder::for(User::class)
            ->where('name', $request->user_name)
            ->where('is_agent', $request->is_affiliate)
            ->first();

        if (empty($user)){
            $this->response()->error('The Member Code Is Not Exists!', 422);
        }

        $crmExcludeUser->user_id           = $user->id;
        $crmExcludeUser->user_name         = $user->name;
        $crmExcludeUser->is_affiliate      = $request->is_affiliate;
        $crmExcludeUser->affiliate_code    = $user->affiliate_code;
        $crmExcludeUser->affiliated_code   = $user->affiliated_code;
        $crmExcludeUser->action_admin_id   = $admin->id;
        $crmExcludeUser->action_admin_name = $admin->name;

        try {
            $crmExcludeUser->save();
            return $this->response()->created();
        } catch (\Exception $exception) {
            return $this->response()->error('Create Exclude User Fail!', 422);
        }
    }


    /**
     * @OA\Patch(
     *      path="/backstage/crm_exclude_users/{crmExcludeUser}",
     *      operationId="backstage.crm_exclude_users.update",
     *      tags={"Backstage-CRM"},
     *      summary="审核/改变提交的单黑名单",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="status", type="boolean", description="黑名单状态"),
     *                  required={"id","status"},
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=204,description="No Content"),
     *      @OA\Response(response=400, description="部分更新失败"),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(CrmExcludeUser $crmExcludeUser, CrmExcludeUsersRequest $request)
    {
        $admin  = auth('admin')->user();
        $update = [
            'review_at' => now(),
            'review_by' => $admin->name,
            'status'    => (boolean)$request->status,
        ];
        try {
            $crmExcludeUser->update($update);
            return $this->response()->noContent();
        } catch (\Exception $exception) {
            return $this->response()->error('Update Data Fail!', 422);
        }
    }


    /**
     * @OA\Delete(
     *      path="/backstage/crm_exclude_users/{CrmExcludeUser}",
     *      operationId="api.backstage.cr m_exclude_users.delete",
     *      tags={"Backstage-CRM"},
     *      summary="删除黑名单",
     *      @OA\Parameter(name="id", in="path", description="名单ID", required=true, @OA\Schema(type="integer")),
     *      @OA\Response(response=204, description="No Content"),
     *      @OA\Response(response=404, description="Not Found"),
     *      @OA\Response(response=422, description="验证失败"),
     *      @OA\Response(response=403, description="没有权限"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function delete(CrmExcludeUser $crmExcludeUser)
    {
        $admin = auth('admin')->user();
        $can   = app(CrmService::class)->checkAdminCanDeleteExcludeUser($crmExcludeUser, $admin);
        if ($can) {
            $crmExcludeUser->delete();
            return $this->response()->accepted();
        }
        return $this->response()->errorForbidden('You are not allowed to delete this data!');
    }
}
