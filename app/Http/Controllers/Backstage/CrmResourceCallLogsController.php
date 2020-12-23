<?php

namespace App\Http\Controllers\Backstage;

use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\CrmResourceCallLogsRequest;
use App\Models\CrmDailyReport;
use App\Models\CrmResource;
use App\Models\CrmResourceCallLog;
use App\Services\CrmReportService;
use App\Services\CrmService;
use App\Transformers\CrmResourceCallLogTransformer;
use App\Transformers\CrmResourceTransformer;
use Carbon\Carbon;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class CrmResourceCallLogsController extends BackstageController
{
    private $crmResourceCallLog;

    public function __construct(CrmResourceCallLog $crmResourceCallLog)
    {
        $this->crmResourceCallLog = $crmResourceCallLog;
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_resource_call_logs",
     *      operationId="backstage.crm_resource_call_logs.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 呼叫资源列表",
     *      @OA\Parameter(name="filter[full_name]", in="query", description="用户全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmResourceCallLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(CrmResourceCallLogsRequest $request)
    {
        $data = QueryBuilder::for(CrmResourceCallLog::class)
            ->allowedFilters(
                Filter::scope('full_name'),
                Filter::scope('phone')
            )
            ->latest()
            ->paginate($request->per_page);
        return $this->response()->paginator($data, new CrmResourceCallLogTransformer());
    }

    /**
     * @OA\Post(
     *      path="/backstage/crm_resource_call_logs",
     *      operationId="backstage.crm_resource_call_logs.store",
     *      tags={"Backstage-CRM"},
     *      summary="生成通话日志",
     *      @OA\RequestBody(
     *          required=true,
     *          @OA\MediaType(
     *              mediaType="application/x-www-form-urlencoded",
     *              @OA\Schema(
     *                  type="object",
     *                  @OA\Property(property="crm_resource_id", type="integer", description="通讯频道"),
     *                  @OA\Property(property="channel", type="integer", description="通讯频道"),
     *                  @OA\Property(property="call_status", type="integer", description="通话状态"),
     *                  @OA\Property(property="comment", type="string", description="备注"),
     *                  required={"crm_resource_id", "channel", "call_status"}
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
    public function store(CrmResourceCallLogsRequest $request)
    {
        $admin                         = auth('admin')->user();
        $insertData                    = $request->only($this->crmResourceCallLog->fillable);
        $insertData['admin_id']        = $admin->id;
        $insertData['crm_resource_id'] = $request->crm_resource_id;

        $crmResource = CrmResource::query()->find($request->crm_resource_id);
        if ($crmResource->status == true) {
            return $this->response()->error('This Order Can Not be Called!', 422);
        }

        $log = $this->crmResourceCallLog->create($insertData);


        try {
            $status = CrmResourceCallLog::$callStatusToStatus[$request->call_status];
            /** @var CrmReportService $crmReportService */
            $crmReportService = app(CrmReportService::class);

            # tag_at 为 null 意味着这是没有被派发的订单，这里需要重新统计订单
            if ($crmResource->tag_at == null) {
                $crmResource->makeCall($status);
                $crmReportService->modifyOrders(now());
            } else {
                $crmResource->makeCall($status);
            }
            $crmReportService->addCallLog($log);

            return $this->response()->item($log, new CrmResourceCallLogTransformer());
        } catch (\Exception $e) {
            return $this->response()->error('Creating Call Log Fail!', 422);
        }
    }

    /**
     * @OA\Get(
     *      path="/backstage/crm_resources/{crmResource}/crm_resource_call_logs",
     *      operationId="backstage.crm_resources.crm_resource_call_logs.index",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 呼叫资源列表",
     *      @OA\Parameter(name="filter[full_name]", in="query", description="用户全名", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[phone]", in="query", description="电话号码", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmResourceCallLog"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function show(CrmResource $crmResource, CrmResourceCallLogsRequest $request)
    {
        return $this->response()->collection($crmResource->crmResourceCallLog, new CrmResourceCallLogTransformer());
    }
}
