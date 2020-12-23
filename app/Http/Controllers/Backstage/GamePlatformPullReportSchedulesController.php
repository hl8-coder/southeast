<?php

namespace App\Http\Controllers\Backstage;

use App\Transformers\GamePlatformScheduleByPlatformTransformer;
use Illuminate\Http\Request;
use Spatie\QueryBuilder\Filter;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use App\Http\Controllers\BackstageController;
use App\Models\GamePlatformPullReportSchedule;
use App\Transformers\GamePlatformPullReportScheduleTransformer;
use App\Http\Requests\Backstage\GamePlatformPullReportScheduleRequest;

class GamePlatformPullReportSchedulesController extends BackstageController
{
    /**
     * @OA\Get(
     *     path="/backstage/game_platform_pull_report_schedules",
     *     operationId="backstage.game_platform_pull_report_schedules.index",
     *     tags={"Backstage-游戏"},
     *     summary="获取拉取游戏时刻表",
     *     @OA\Parameter(name="filter[platform_code]", in="query", description="会员名称", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[status]", in="query", description="状态", @OA\Schema(type="integer")),
     *     @OA\Parameter(name="filter[start_at]", in="query", description="排程开始日期", @OA\Schema(type="string")),
     *     @OA\Parameter(name="filter[end_at]", in="query", description="排程结束日期", @OA\Schema(type="string")),
     *     @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformPullReportSchedule"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function index(Request $request)
    {
        $schedules = QueryBuilder::for(GamePlatformPullReportSchedule::class)
            ->allowedFilters(
                Filter::exact('status'),
                Filter::exact('platform_code'),
                Filter::scope('start_at', 'mission_scope_start'),
                Filter::scope('end_at', 'mission_scope_end')
            )
            ->paginate($request->per_page);

        return $this->response->paginator($schedules, new GamePlatformPullReportScheduleTransformer());
    }

    /**
     * @OA\Patch(
     *      path="/backstage/game_platform_pull_report_schedules/{schedule}",
     *      operationId="backstage.game_platform_pull_report_schedules.update",
     *      tags={"Backstage-游戏"},
     *      summary="更新拉取记录状态",
     *      @OA\Parameter(
     *         name="schedule",
     *         in="path",
     *         description="记录ID",
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
     *                  @OA\Property(property="status", type="string", description="记录状态"),
     *                  required={"status"}
     *              ),
     *          ),
     *      ),
     *      @OA\Response(
     *          response=204,
     *          description="No Content",
     *       ),
     *       @OA\Response(response=401, description="授权不通过"),
     *       @OA\Response(response=422, description="验证不通过"),
     *       @OA\Response(response=404, description="Not Found"),
     *       security={
     *           {"bearer": {}}
     *       }
     *     )
     */
    public function update(GamePlatformPullReportSchedule $schedule, GamePlatformPullReportScheduleRequest $request)
    {
        $data = collect($request->all())->only('status')->toArray();
        $data = remove_null($data);
        $schedule->update($data);
        return $this->response->noContent();
    }

    /**
     * @OA\Get(
     *     path="/backstage/game_platform_pull_report_schedules_by_platform",
     *     operationId="backstage.game_platform_pull_report_schedules_by_platform",
     *     tags={"Backstage-游戏"},
     *     summary="获取拉取游戏时刻表根据产品",
     *     @OA\Response(
     *          response=200,
     *          description="请求成功",
     *          @OA\JsonContent(
     *              type="array",
     *              @OA\Items(ref="#/components/schemas/GamePlatformScheduleByPlatform"),
     *          ),
     *      ),
     *      @OA\Response(response=401, description="授权不通过"),
     *      security={
     *          {"bearer": {}}
     *      }
     * )
     */
    public function gamePlatformSchedule(Request $request)
    {
        $schedules = GamePlatformPullReportSchedule::query()
            ->select([
                'platform_code',
                DB::raw('SUM(CASE WHEN status = '.GamePlatformPullReportSchedule::STATUS_FAIL.' THEN  1 ELSE 0 END) fail'),
                DB::raw('SUM(CASE WHEN status = '.GamePlatformPullReportSchedule::STATUS_CREATED.' THEN  1 ELSE 0 END) has_created')
            ])
            ->groupBy('platform_code')
            ->get();

        return $this->response->collection($schedules, new GamePlatformScheduleByPlatformTransformer());
    }
}
