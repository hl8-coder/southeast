<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\ExcelTemplateExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\CrmResourcesRequest;
use App\Imports\CrmResourceImport;
use App\Models\CrmBoAdmin;
use App\Models\CrmResource;
use App\Services\CrmReportService;
use App\Services\CrmService;
use App\Services\SMSService;
use App\Transformers\CrmResourceTransformer;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class CrmResourcesController extends BackstageController
{
    private $crmService;
    private $crmReportService;

    public function __construct(CrmService $service, CrmReportService $crmReportService)
    {
        $this->crmService       = $service;
        $this->crmReportService = $crmReportService;
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_resources",
     *      operationId="backstage.crm_resources.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 呼叫资源列表",
     *      @OA\Parameter(name="filter[full_name]", in="query", description="用户全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[country_code]", in="query", description="国家代号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="电销人员", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_admin_name]", in="query", description="派单管理员", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_case_admin_name]", in="query", description="最后保存者", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_start]", in="query", description="Tag查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_end]", in="query", description="Tag查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_start]", in="query", description="最后修改查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_end]", in="query", description="最后修改查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="呼叫状态", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmOrder"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(CrmResourcesRequest $request, SMSService $sms)
    {
        $ORM = QueryBuilder::for(CrmResource::class)
            ->allowedFilters(
                'full_name',
                'phone',
                'country_code',
                'admin_name',
                'tag_admin_name',
                'last_save_case_admin_name',
                Filter::scope('tag_start'),
                Filter::scope('tag_end'),
                Filter::scope('last_save_start'),
                Filter::scope('last_save_end'),
                Filter::exact('status'),
                Filter::exact('call_status')

            )
            ->orderByDesc('id');

        # 检测当前用户，如果是 crm bo admin，则只显示自己名下的用户
        $admin  = $this->user;
        $exists = CrmBoAdmin::query()->where('admin_id', $admin->id)->exists();
        if ($exists) {
            $ORM = $ORM->where('admin_id', $admin->id);
        }
        $data = $ORM->paginate($request->per_page);

        return $this->response()->paginator($data, new CrmResourceTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_resources/excel_template",
     *      operationId="backstage.crm_resources.excel_template",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 呼叫资源列表上传模板",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmOrder"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function excelTemplate(CrmResourcesRequest $request)
    {
        $headers = ['country_code', 'full_name', 'phone'];
        return Excel::download(new ExcelTemplateExport([], $headers), 'template.xlsx');
    }

    /**
     * @OA\Post(
     *      path="/backstage/crm_resources",
     *      operationId="backstage.crm_resources.store",
     *      tags={"Backstage-CRM"},
     *      summary="通过表格生成CRM 呼叫资源列表",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="excel", type="file", description="资源文件"),
     *                  required={"excel"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(response=201,description="no content"),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证错误"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function store(CrmResourcesRequest $request)
    {
        $data   = Excel::toArray(new CrmResourceImport(), $request->file('excel'));
        $insert = [];
        collect($data[0])->each(function ($item) use (& $insert) {
            $temp['country_code'] = $item[0];
            $temp['full_name']    = $item[1] ?? '';
            $temp['phone']        = $item[2];
            $temp['updated_at']   = now();
            $temp['created_at']   = now();
            $temp['upload_by']    = $this->user->name;
            if (!empty($item[2])) {
                $insert[] = $temp;
            }
        });
        batch_insert(app(CrmResource::class)->getTable(), $insert);
        return $this->response()->created();
    }


    /**
     * @OA\Patch(
     *      path="/backstage/crm_resources",
     *      operationId="backstage.crm_resources.update",
     *      tags={"Backstage-CRM"},
     *      summary="批量派单或者批量取消派单",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="crm_resource_ids", type="array", description="更新数据的ID", @OA\Items()),
     *                  @OA\Property(property="admin_id", type="integer", description="crm bo admin 的 admin_id"),
     *                  @OA\Property(property="distribute", type="boolean", description="是否为分发任务"),
     *                  required={"crm_order_ids","distribute"},
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
    public function update(CrmResourcesRequest $request)
    {
        $updateResult = $this->crmService->batchUpdateCrmResources($request->crm_resource_ids, $request->admin_id, $request->distribute);

        $this->crmReportService->modifyOrders(now());

        if ($updateResult === true) {
            return $this->response()->noContent();
        }

        $this->response()->error($updateResult, 422);
    }

}
