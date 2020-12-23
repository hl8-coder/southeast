<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\CrmCallLogsExport;
use App\Exports\CrmOrderExport;
use App\Exports\ExcelTemplateExport;
use App\Imports\CrmOrderImport;
use App\Models\CrmBoAdmin;
use App\Models\CrmOrder;
use App\Models\User;
use App\Services\CrmReportService;
use App\Services\CrmService;
use Illuminate\Support\Arr;
use Maatwebsite\Excel\Facades\Excel;
use App\Http\Controllers\BackstageController;
use App\Transformers\CrmOrderTransformer;
use App\Http\Requests\Backstage\CrmOrderRequest;


class CrmOrdersController extends BackstageController
{
    protected $crmService;
    protected $crmReportService;
    protected $crmBoAdmin;

    public function __construct(CrmService $service, CrmBoAdmin $crmBoAdmin, CrmReportService $crmReportService)
    {
        $this->crmService       = $service;
        $this->crmBoAdmin       = $crmBoAdmin;
        $this->crmReportService = $crmReportService;
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_orders",
     *      operationId="backstage.crm_orders.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM Welcome列表",
     *      @OA\Parameter(name="filter[name]", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="BO User名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_admin_name]", in="query", description="派单管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[full_name]", in="query", description="用户全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[email]", in="query", description="邮箱", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_status]", in="query", description="会员状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="风控组ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[payment_group_id]", in="query", description="支付组ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[affiliated_code]", in="query", description="代理代号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_start]", in="query", description="注册查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_end]", in="query", description="注册查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_start]", in="query", description="最后登陆查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_end]", in="query", description="最后登陆查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_deposit_start]", in="query", description="最后充值查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_deposit_end]", in="query", description="最后充值查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_start]", in="query", description="Tag查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_end]", in="query", description="Tag查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_start]", in="query", description="最后修改查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_end]", in="query", description="最后修改查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="order状态", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[call_status]", in="query", description="呼叫状态", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[register_ip]", in="query", description="注册IP", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[deposit]", in="query", description="是否有充值", @OA\Schema(type="boolean")),
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
    public function index(CrmOrderRequest $request)
    {
        $paginateData = $this->crmService->getCrmOrdersPaginate($request);
        return $this->response->paginator($paginateData, new CrmOrderTransformer());
    }


    /**
     * @OA\Patch(
     *      path="/backstage/crm_orders",
     *      operationId="backstage.crm_orders.update",
     *      tags={"Backstage-CRM"},
     *      summary="更新CRM order 资料",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="crm_order_ids", type="array", description="更新数据的ID", @OA\Items()),
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
    public function updateBatch(CrmOrderRequest $request)
    {
        $updateResult = $this->crmService->batchUpdateCrmOrders($request->crm_order_ids, $request->admin_id, $request->distribute);
        $this->crmReportService->modifyOrders(now());
        if ($updateResult === true) {
            return $this->response()->noContent();
        }

        $this->response()->error($updateResult, 422);
    }


    /**
     * @OA\Get(
     *      path="/backstage/crm_orders/excel_report",
     *      operationId="backstage.crm_orders.excel_report",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 报表",
     *      @OA\Parameter(name="filter[name]", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="BO User名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_admin_name]", in="query", description="派单管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[full_name]", in="query", description="用户全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[email]", in="query", description="邮箱", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_status]", in="query", description="会员状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="风控组ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[payment_group_id]", in="query", description="支付组ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[affiliated_code]", in="query", description="代理代号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_start]", in="query", description="注册查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_end]", in="query", description="注册查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_start]", in="query", description="最后登陆查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_end]", in="query", description="最后登陆查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_deposit_start]", in="query", description="最后充值查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_deposit_end]", in="query", description="最后充值查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_start]", in="query", description="Tag查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_end]", in="query", description="Tag查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_start]", in="query", description="最后修改查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_end]", in="query", description="最后修改查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="order状态", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[call_status]", in="query", description="呼叫状态", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[register_ip]", in="query", description="注册IP", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[deposit]", in="query", description="是否有充值", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *              @OA\Items(ref="#/components/schemas/CrmOrderReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function excelReport(CrmOrderRequest $request)
    {
        return Excel::download(new CrmOrderExport($request), 'report.xlsx');
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_orders/crm_call_logs",
     *      operationId="backstage.crm_orders.crm_call_logs",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 报表",
     *      @OA\Parameter(name="filter[name]", in="query", description="用户名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="BO User名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_admin_name]", in="query", description="派单管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[full_name]", in="query", description="用户全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[email]", in="query", description="邮箱", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[user_status]", in="query", description="会员状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[risk_group_id]", in="query", description="风控组ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[payment_group_id]", in="query", description="支付组ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[affiliated_code]", in="query", description="代理代号", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_start]", in="query", description="注册查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[register_end]", in="query", description="注册查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_start]", in="query", description="最后登陆查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_login_end]", in="query", description="最后登陆查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_deposit_start]", in="query", description="最后充值查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_deposit_end]", in="query", description="最后充值查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_start]", in="query", description="Tag查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[tag_end]", in="query", description="Tag查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_start]", in="query", description="最后修改查询开始时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[last_save_end]", in="query", description="最后修改查询结束时间", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[status]", in="query", description="order状态", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[call_status]", in="query", description="呼叫状态", @OA\Schema(type="boolean")),
     *      @OA\Parameter(name="filter[register_ip]", in="query", description="注册IP", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[deposit]", in="query", description="是否有充值", @OA\Schema(type="boolean")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *              @OA\Items(ref="#/components/schemas/CrmOrderCallLogs"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function crmCallLogs(CrmOrderRequest $request)
    {
        return Excel::download(new CrmCallLogsExport($request), 'report.xlsx');
    }


    /**
     * @OA\Get(
     *      path="/backstage/crm_orders/import_template",
     *      operationId="backstage.crm_orders.import_template",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 上传会员信息模版",
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function template()
    {
        return Excel::download(new ExcelTemplateExport([], ['member code']), 'crm_order_template.xlsx');
    }


    /**
     * @OA\Post(
     *      path="/backstage/crm_orders",
     *      operationId="backstage.crm_orders.store",
     *      tags={"Backstage-CRM"},
     *      summary="通过表格生成CRM Orders",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="excel", type="file", description="资源文件"),
     *                  @OA\Property(property="type", type="string", description="上传名单类型，与index中type的含义一致"),
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
    public function store(CrmOrderRequest $request)
    {
        $data  = Excel::toArray(new CrmOrderImport(), $request->file('excel'));
        $type  = $request->type;
        $names = Arr::flatten($data);
        $names = array_unique($names);

        $users = User::query()->whereIn('name', $names)
            ->where('is_agent', User::AGENT_0)
            ->get(['id', 'name', 'affiliated_code']);

        if (count($names) !== $users->count()) {
            $nameNon = array_diff($names, $users->pluck('name')->toArray());
            $this->response()->error('Invalid Member Code : ' . implode(', ', $nameNon), 422);
        }

        $userIdExists = CrmOrder::query()->where('type', $type)
            ->whereIn('user_id', $users->pluck('id')->toArray())
            ->where('status', CrmOrder::STATUS_OPEN)
            ->pluck('user_id')->toArray();

        $users = $users->whereNotIn('id', $userIdExists);
        if ($users->isEmpty()) {
            return $this->response()->created();
        }


        $insert = [];
        $users->each(function ($item) use (& $insert, $type) {
            $temp['type']                      = $type;
            $temp['user_id']                   = $item->id;
            $temp['affiliated_code']           = $item->affiliated_code;
            $temp['updated_at']                = now();
            $temp['created_at']                = now();
            $temp['last_save_case_admin_id']   = $this->user->id;
            $temp['last_save_case_admin_name'] = $this->user->name;
            $temp['last_save_case_at']         = now();
            $insert[]                          = $temp;
        });

        batch_insert(app(CrmOrder::class)->getTable(), $insert);
        return $this->response()->created();
    }
}
