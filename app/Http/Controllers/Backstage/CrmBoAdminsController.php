<?php

namespace App\Http\Controllers\Backstage;

use App\Models\Admin;
use App\Models\CrmBoAdmin;
use App\Services\CrmService;
use App\Transformers\AuditTransformer;
use OwenIt\Auditing\Models\Audit;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Requests\Backstage\CrmBoAdminRequest;
use App\Transformers\CrmBoAdminTransformer;
use App\Http\Controllers\BackstageController;

class CrmBoAdminsController extends BackstageController
{

    /**
     * @OA\Get(
     *      path="/backstage/crm_bo_admins",
     *      operationId="backstage.crm_bo_admins.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM BO 管理员列表",
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="crm_bo_admin 名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="是否在职", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[on_duty]", in="query", description="是否上班", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmBoAdmin"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(CrmBoAdminRequest $request)
    {
        $admins = QueryBuilder::for(CrmBoAdmin::class)
            ->allowedFilters(
                Filter::exact('admin_name'),
                Filter::exact('status'),
                Filter::exact('on_duty')
            )
            ->orderByDesc('sort')
            ->paginate($request->per_page);

        return $this->response->paginator($admins, new CrmBoAdminTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/crm_bo_admins",
     *      operationId="backstage.crm_bo_admins.store",
     *      tags={"Backstage-CRM"},
     *      summary="创建BO管理员",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/json",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="admin_name", type="integer", description="管理者ID"),
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="在职状态"),
     *                  @OA\Property(property="on_duty", type="boolean", description="上班状态"),
     *                  @OA\Property(property="start_worked_at", type="string", description="上班时间, 丢日期格式 H:i:s"),
     *                  @OA\Property(property="end_worked_at", type="string", description="下班时间, 丢日期格式 H:i:s"),
     *                  required={"admin_name"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=201,
     *          description="创建成功",
     *          @OA\JsonContent(ref="#/components/schemas/CrmBoAdmin"),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *          security={
     *              {"bearer": {}}
     *          }
     *     )
     */
    public function store(CrmBoAdminRequest $request, CrmBoAdmin $crmBoAdmin)
    {
        $data = remove_null($request->all($crmBoAdmin->getFillable()));

        $admin                  = Admin::where('name', $request->admin_name)->first();
        $data['admin_id']       = $admin->id;
        $adminTag               = auth('admin')->user();
        $data['tag_admin_id']   = $adminTag->id;
        $data['tag_admin_name'] = $adminTag->name;

        try {
            $crmBoAdmin = $crmBoAdmin->create($data);
        } catch (\Exception $exception) {
            return $this->response()->error('Create CRM Bo User Fail', 422);
        }

        return $this->response->item(CrmBoAdmin::find($crmBoAdmin->id), new CrmBoAdminTransformer());
    }

    /**
     * @OA\Delete(
     *      path="/backstage/crm_bo_admins/{crm_bo_admin}",
     *      operationId="backstage.crm_bo_admins.delete",
     *      tags={"Backstage-CRM"},
     *      summary="删除BO管理员",
     *      @OA\Parameter(
     *         name="admin_id",
     *         in="path",
     *         description="Bo管理员id",
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
    public function destroy(CrmBoAdmin $crmBoAdmin)
    {

        $crmBoAdmin->delete();

        return $this->response->noContent();
    }

    /**
     * @OA\Patch(
     *      path="/backstage/crm_bo_admins/{crm_bo_admin}",
     *      operationId="backstage.crm_bo_admins.update",
     *      tags={"Backstage-CRM"},
     *      summary="更新BO管理员角色",
     *      @OA\Parameter(
     *         name="vip",
     *         in="path",
     *         description="BO 管理者ID",
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
     *                  @OA\Property(property="sort", type="integer", description="排序"),
     *                  @OA\Property(property="status", type="boolean", description="在职状态"),
     *                  @OA\Property(property="on_duty", type="boolean", description="上班状态"),
     *                  @OA\Property(property="start_worked_at", type="string", description="上班时间, 丢日期格式 H:i:s"),
     *                  @OA\Property(property="end_worked_at", type="string", description="下班时间, 丢日期格式 H:i:s"),
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(ref="#/components/schemas/Vip"),
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(CrmBoAdmin $crmBoAdmin, CrmBoAdminRequest $request)
    {
        $data                   = remove_null($request->all(['sort', 'status', 'on_duty', 'start_worked_at', 'end_worked_at']));
        $adminTag               = auth('admin')->user();
        $data['tag_admin_id']   = $adminTag->id;
        $data['tag_admin_name'] = $adminTag->name;

        $crmBoAdmin->update($data);

        return $this->response->item($crmBoAdmin, new CrmBoAdminTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_bo_admins/audits",
     *      operationId="backstage.users.audits.index",
     *      tags={"Backstage-CRM"},
     *      summary="Crm Bo Admins 修改记录",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/Audit"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=422, description="验证错误"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function audits(CrmBoAdminRequest $request)
    {
        $audits = Audit::query()
            ->where('auditable_type', 'App\Models\CrmBoAdmin')
            ->orderByDesc('created_at')
            ->paginate($request->per_page);
        foreach ($audits as $audit){
            $audit->old_value = app(CrmService::class)->transferAudit($audit->old_values);
            $audit->new_value = app(CrmService::class)->transferAudit($audit->new_values);
        }

        return $this->response->paginator($audits, new AuditTransformer('crm_bo_admin'));
    }

}
