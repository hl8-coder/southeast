<?php

namespace App\Http\Controllers\Api\Affiliate;

use App\Http\Controllers\ApiController;
use App\Http\Requests\Affiliate\TrackingStatisticRequest;
use App\Models\TrackingStatistic;
use App\Models\TrackingStatisticLog;
use App\Transformers\TrackingTransformer;
use App\Transformers\TrackingStatisticTransformer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class TrackingStatisticsController extends ApiController
{
    /**
     * @OA\Get(
     *      path="/affiliate/tracking_statistic_logs",
     *      operationId="api.affiliate.tracking_statistic_logs",
     *      tags={"Affiliate-代理"},
     *      summary="资源点击",
     *      @OA\Parameter(name="filter[id]", in="query", description="tracking_id", @OA\Schema(type="integer")),
     *      @OA\Parameter(name="start_at", in="query", description="组别", @OA\Schema(type="string")),
     *      @OA\Parameter(name="end_at", in="query", description="尺寸", @OA\Schema(type="string")),
     *      @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/TrackingStatistic"),
     *          ),
     *       ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     *  )
     */
    public function index(TrackingStatisticRequest $request)
    {
        $user     = $this->user();
        $tracks = QueryBuilder::for(TrackingStatistic::class)
            ->where('user_id', $user->id)
            ->allowedFilters([
                Filter::exact('id'),
            ])
            ->get();

        $data = [];
        foreach ($tracks as $value) {
            $logs = TrackingStatisticLog::query()
                ->where('tracking_id', $value->id)
                ->where(function ($query) use ($request) {
                    if ($request->start_at) {
                        $query->where('created_at', '>=', Carbon::parse($request->start_at)->startOfDay()->toDateTimeString());
                    }
                    if ($request->end_at) {
                        $query->where('created_at', '<=', Carbon::parse($request->end_at)->endOfDay()->toDateTimeString());
                    }
                })
                ->selectRaw("DATE_FORMAT(`created_at`, '%Y-%m-%d') as date, count(`ip`) as click, count(distinct `ip`) as unique_click")
                ->groupBy('date')
                ->get()
                ->toArray();
            $name = $value->tracking_name;
            if ($user->affiliate_code == $value->tracking_name) {
                $name = 'default';
            }
            if ($logs) {
                foreach ($logs as &$log) {
                    $log['date'] = convert_time($this->getDate($value->id, $log));
                    $data[]      = [
                        'tracking_name' => $name,
                        'date'          => $log['date'],
                        'total_click'   => $log['click'],
                        'unique_click'  => $log['unique_click'],
                    ];
                }
            } else {
                $data[] = [
                    'tracking_name' => $name,
                    'date'          => '',
                    'total_click'   => 0,
                    'unique_click'  => 0,
                ];
            }

        }
        return $this->response->array(['data' => $data]);
    }

    public function getDate($id, $data)
    {
        $start = Carbon::parse($data['date'])->startOfDay()->toDateTimeString();
        $end   = Carbon::parse($data['date'])->endOfDay()->toDateTimeString();
        $dateT = TrackingStatisticLog::query()
            ->where('tracking_id', $id)
            ->where(function ($query) use ($start, $end) {
                if ($start) {
                    $query->where('created_at', '>=', $start);
                }
                if ($end) {
                    $query->where('created_at', '<=', $end);
                }
            })
            ->orderBy('created_at', 'asc')
            ->first();
        return $dateT->created_at;
    }
}
