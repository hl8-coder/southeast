<?php


namespace App\Http\Controllers\Backstage;


use App\Exports\KpiReportExport;
use App\Http\Controllers\BackstageController;
use App\Http\Requests\Backstage\KpiReportRequest;
use App\Http\Requests\Request;
use App\Models\KpiReport;
use App\Transformers\KpiReportTransformer;
use Carbon\Carbon;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class KpiReportsController extends BackstageController
{
    /**
     * @OA\Get(
     *      path="/backstage/kpi_report",
     *      operationId="backstage.kpi_report.index",
     *      tags={"Backstage-平台"},
     *      summary="Kpi report",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/KpiReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(KpiReportRequest $request)
    {
        $query = QueryBuilder::for(KpiReport::class);

        if ($request->input('filter.start_at') != null) {
            $startDate = Carbon::parse($request->input('filter.start_at'))->toDateString();
            $query->where('date', '>=', $startDate);
        }

        if ($request->input('filter.end_at') != null) {
            $endDate = Carbon::parse($request->input('filter.end_at'))->toDateString();
            $query->where('date', '<=', $endDate);
        }

        if ($request->input('filter.currency') != null){
            $query->where('currency', $request->input('filter.currency'));
        }

        $report = $query->orderBy('date', 'desc')
            ->paginate($request->per_page);

        return $this->response->paginator($report, new KpiReportTransformer());
    }



    /**
     * @OA\Get(
     *      path="/backstage/kpi_report/excel_report",
     *      operationId="backstage.kpi_report.excel_report",
     *      tags={"Backstage-平台"},
     *      summary="Kpi report",
     *      @OA\Parameter(name="filter[start_at]", in="query", description="开始日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[end_at]", in="query", description="结束日期", @OA\Schema(type="string")),
     *      @OA\Parameter(name="filter[currency]", in="query", description="币别", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\MediaType(
     *              mediaType="application/vnd.ms-excel",
     *              @OA\Items(ref="#/components/schemas/KpiReport"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function excelReport(KpiReportRequest $request)
    {
        return Excel::download(new KpiReportExport($request), 'report.xlsx');
    }
}
