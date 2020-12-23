<?php

namespace App\Http\Controllers\Backstage;

use App\Models\CrmCallLog;
use App\Models\CrmOrder;
use App\Models\User;
use App\Services\CrmReportService;
use App\Services\CrmService;
use Carbon\Carbon;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;
use App\Transformers\CrmCallLogTransformer;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\CrmCallLogsRequest;

class CrmCallLogsController extends BackstageController
{

    /**
     * @OA\Get(
     *      path="/backstage/crm_orders/crm_call_logs?include=crmOrder",
     *      operationId="backstage.crm_orders.crm_call_logs.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM Call Logs 列表",
     *      @OA\Parameter(name="filter[channel]", in="query", description="联络渠道", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[crm_order_id]", in="query", description="Crm Oder ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[call_status]", in="query", description="联络状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[purpose]", in="query", description="联络目的", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[prefer_product]", in="query", description="客户偏爱产品", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[prefer_bank]", in="query", description="客户偏爱银行", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[source]", in="query", description="客户来源", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[user_name]", in="query", description="客户名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[reason]", in="query", description="客户说明的原因", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[comment]", in="query", description="备注（模糊）", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmCallLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(CrmCallLogsRequest $request)
    {
        $logs = QueryBuilder::for(CrmCallLog::class)
            ->allowedFilters(
                Filter::exact('channel'),
                Filter::exact('crm_order_id'),
                Filter::exact('call_status'),
                Filter::exact('purpose'),
                Filter::exact('prefer_product'),
                'prefer_bank',
                Filter::exact('source'),
                Filter::exact('reason'),
                Filter::scope('user_name'),
                'comment'
            )
            ->paginate($request->per_page);
        return $this->response()->paginator($logs, new CrmCallLogTransformer());
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_orders/{user}/crm_call_logs?include=crmOrder",
     *      operationId="backstage.crm_orders.crm_call_logs.user",
     *      tags={"Backstage-CRM"},
     *      summary="CRM Call Logs 列表",
     *      @OA\Parameter(name="filter[channel]", in="query", description="联络渠道", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[crm_order_id]", in="query", description="Crm Oder ID", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[call_status]", in="query", description="联络状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[purpose]", in="query", description="联络目的", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[prefer_product]", in="query", description="客户偏爱产品", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[prefer_bank]", in="query", description="客户偏爱银行", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[source]", in="query", description="客户来源", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[reason]", in="query", description="客户说明的原因", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[comment]", in="query", description="备注（模糊）", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmCallLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function userCrmCallLogs(User $user, CrmCallLogsRequest $request)
    {
        $logs = QueryBuilder::for(CrmCallLog::class)
            ->whereHas('crmOrder', function ($query) use ($user) {
                return $query->where('user_id', $user->id);
            })
            ->allowedFilters(
                Filter::exact('channel'),
                Filter::exact('crm_order_id'),
                Filter::exact('call_status'),
                Filter::exact('purpose'),
                Filter::exact('prefer_product'),
                'prefer_bank',
                Filter::exact('source'),
                Filter::exact('reason'),
                Filter::scope('user_name'),
                'comment'
            )
            ->paginate($request->per_page);
        return $this->response()->paginator($logs, new CrmCallLogTransformer());
    }


    /**
     * @OA\Get(
     *      path="/backstage/crm_orders/{crmOrder}/call_logs?include=crmOrder",
     *      operationId="backstage.crm_orders.crm_call_logs.show",
     *      tags={"Backstage-CRM"},
     *      summary="CRM Order的呼叫记录",
     *      @OA\Parameter(name="filter[channel]", in="query", description="联络渠道", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[call_status]", in="query", description="联络状态", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[purpose]", in="query", description="联络目的", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[prefer_product]", in="query", description="客户偏爱产品", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[prefer_bank]", in="query", description="客户偏爱银行", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[source]", in="query", description="客户来源", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[reason]", in="query", description="客户说明的原因", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[comment]", in="query", description="备注（模糊）", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmCallLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function crmOrderCallLogs(CrmOrder $crmOrder, CrmCallLogsRequest $request)
    {
        $logs = QueryBuilder::for(CrmCallLog::class)
            ->where('crm_order_id', $crmOrder->id)
            ->allowedFilters(
                Filter::exact('channel'),
                Filter::exact('crm_order_id'),
                Filter::exact('call_status'),
                Filter::exact('purpose'),
                Filter::exact('prefer_product'),
                'prefer_bank',
                Filter::exact('source'),
                Filter::exact('reason'),
                Filter::scope('user_name'),
                'comment'
            )
            ->latest()
            ->paginate($request->per_page);
        return $this->response()->paginator($logs, new CrmCallLogTransformer());
    }


    /**
     * @OA\Post(
     *      path="/backstage/crm_orders/crm_call_logs",
     *      operationId="backstage.crm_orders.crm_call_logs.store",
     *      tags={"Backstage-CRM"},
     *      summary="生成Crm Call Logs",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="channel", type="string", description="联络渠道"),
     *                  @OA\Property(property="crm_order_id", type="integer", description="Crm Oder ID"),
     *                  @OA\Property(property="call_status", type="integer", description="联络状态"),
     *                  @OA\Property(property="purpose", type="string", description="联络目的"),
     *                  @OA\Property(property="prefer_product", type="string", description="客户偏爱产品"),
     *                  @OA\Property(property="prefer_bank", type="string", description="客户偏爱银行"),
     *                  @OA\Property(property="source", type="integer", description="客户来源"),
     *                  @OA\Property(property="reason", type="string", description="客户说明的原因"),
     *                  @OA\Property(property="comment", type="string", description="备注"),
     *                  required={"channel", "crm_order_id", "call_status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmCallLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function store(CrmCallLogsRequest $request, CrmCallLog $crmCallLog)
    {
        $admin      = auth('admin')->user();
        $createData = $request->all($crmCallLog->fillable);

        $crmOrder = CrmOrder::query()->find($request->crm_order_id);
        $check    = app(CrmService::class)->checkCrmOrderCanCreateCrmCallLog($crmOrder);
        if ($check !== true) {
            return $this->response()->error($check, 422);
        }

        $createData['admin_id'] = $admin->id;

        $log = $crmCallLog->create($createData);

        try {
            $status = CrmCallLog::$callStatusToStatus[$request->call_status];
            /** @var CrmReportService $crmReportService */
            $crmReportService = app(CrmReportService::class);

            # tag_at 为 null 意味着这是没有被派发的订单，这里需要出发天与周的订单派发统计
            if ($crmOrder->tag_at == null) {
                $crmOrder->makeCall($status);
                $crmReportService->modifyOrders(now());
            } else {
                $crmOrder->makeCall($status);
            }

            $crmReportService->addCallLog($log);

            return $this->response()->item($log, new CrmCallLogTransformer());
        } catch (\Exception $e) {
            return $this->response()->error('Creating Call Log Fail!', 422);
        }
    }
}
