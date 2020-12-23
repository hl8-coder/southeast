<?php

namespace App\Http\Controllers\Backstage;

use App\Exports\CrmDailyReportExport;
use App\Exports\CrmWeeklyReportExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\CrmReportsRequest;
use App\Models\CrmDailyReport;
use App\Models\CrmWeeklyReport;
use App\Transformers\CrmDailyReportTransformer;
use App\Transformers\CrmWeeklyReportTransformer;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class CrmReportsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/crm_report/weekly",
     *      operationId="backstage.crm_orders.weekly",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 周报表",
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[type]", in="query", description="订单类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[week]", in="query", description="第几周", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmWeeklyReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    // 周报表：第几周与日期区间，周总通话次数，类型通话周总数【welcome】，个人周通话总数，成功通话次数和其他通话状态统计数 通话后首充笔数，通话后首充金额，成功通话与充值比，订单类型，手动调额总额【四大分类总数，需要写入到数据库】
    public function weeklyReport(CrmReportsRequest $request)
    {
        // 根据电话号码关联，查询注册人数
        // adjustment 添加一个分类用来标记电销充值的分类，方便后续统计
        // 作为统计补充，需要将页面上的数据进行加权总和，即每个页面统计数据结果进行二次统计
        $data = QueryBuilder::for(CrmWeeklyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->orderBy('week_start_at', 'desc')
            ->paginate($request->per_page);

        $totalOrders = QueryBuilder::for(CrmWeeklyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('person_total_type_orders');

        $totalCalls = QueryBuilder::for(CrmWeeklyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('person_total_type_calls');

        $successful = QueryBuilder::for(CrmWeeklyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('successful');

        $fail = QueryBuilder::for(CrmWeeklyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('fail');

        // 总分配订单，总呼叫量，呼叫成功总量
        $info = [
            ['key' => 'Total assigned', 'value' => $totalOrders],
            ['key' => 'Total called', 'value' => $totalCalls],
            ['key' => 'Total successful', 'value' => $successful],
        ];

        return $this->response()->paginator($data, new CrmWeeklyReportTransformer())->setMeta(['info' => $info]);
    }


    /**
     * @OA\Get(
     *      path="/backstage/crm_report/weekly/export_excel",
     *      operationId="backstage.crm_orders.weekly.excel",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 周报表下载",
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[type]", in="query", description="订单类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[week]", in="query", description="第几周", @OA\Schema(type="integer")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmDailyReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      @OA\Response(response=404, description="Not Found"),
     *      security={
     *         {"bearer": {}}
     *      }
     *  )
     */
    public function weeklyReportExcelExport(CrmReportsRequest $request)
    {
        return Excel::download(new CrmWeeklyReportExport($request), 'crm_weekly.xlsx');
    }


    /**
     * @OA\Get(
     *      path="/backstage/crm_report/daily",
     *      operationId="backstage.crm_orders.daily",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 日报表",
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[type]", in="query", description="订单类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[date]", in="query", description="日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[week]", in="query", description="第几周", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmDailyReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    // total_call_welcome, total_call_daily_retention, total_call_retention, total_call_of_no_deposit,
    // week, date, total_call, crm_order_type, call_status_successful, call_status_hand_up, ..., admin_id, admin_name
    // 天报表:通话总数，成功通话总数，通话订单类型
    public function dailyReport(CrmReportsRequest $request)
    {
        $data = QueryBuilder::for(CrmDailyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('date'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->orderBy('date', 'desc')
            ->paginate($request->per_page);

        $totalOrders = QueryBuilder::for(CrmDailyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('date'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('person_total_type_orders');

        $totalCalls = QueryBuilder::for(CrmDailyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('date'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('person_total_type_calls');

        $successful = QueryBuilder::for(CrmDailyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('date'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('successful');

        $fail = QueryBuilder::for(CrmDailyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('date'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->sum('fail');

        // 总分配订单，总呼叫量，呼叫成功总量
        $info = [
            ['key' => 'Total assigned', 'value' => $totalOrders],
            ['key' => 'Total called', 'value' => $totalCalls],
            ['key' => 'Total successful', 'value' => $successful],
        ];
        return $this->response()->paginator($data, new CrmDailyReportTransformer())->setMeta(['info' => $info]);
    }


    /**
     * @OA\Get(
     *      path="/backstage/crm_report/daily/export_excel",
     *      operationId="backstage.crm_orders.daily.excel",
     *      tags={"Backstage-CRM"},
     *      summary="CRM 日报表下载",
     *      @OA\Parameter(name="filter[admin_name]", in="query", description="管理员名称", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[type]", in="query", description="订单类型", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="filter[date]", in="query", description="日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[week]", in="query", description="第几周", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/CrmDailyReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function dailyReportExcelExport(CrmReportsRequest $request)
    {
        return Excel::download(new CrmDailyReportExport($request), 'crm_daily.xlsx');
    }
}
