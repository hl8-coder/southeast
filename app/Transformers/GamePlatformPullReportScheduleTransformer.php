<?php

namespace App\Transformers;

use App\Models\GamePlatformPullReportSchedule;

/**
 * @OA\Schema(
 *   schema="GamePlatformPullReportSchedule",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="id"),
 *   @OA\Property(property="platform_code", type="string", description="平台code"),
 *   @OA\Property(property="start_at", type="string", description="拉取开始时间", format="date-time"),
 *   @OA\Property(property="end_at", type="string", description="拉取结束时间", format="date-time"),
 *   @OA\Property(property="origin_total", type="string", description="原始记录条数"),
 *   @OA\Property(property="transfer_total", type="string", description="转换记录条数"),
 *   @OA\Property(property="pulled_at", type="string", description="拉取时间", format="date-time"),
 *   @OA\Property(property="remarks", type="string", description="备注"),
 *   @OA\Property(property="times", type="integer", description="拉取次数"),
 *   @OA\Property(property="status", type="integer", description="状态"),
 *   @OA\Property(property="display_status", type="string", description="状态显示"),
 * )
 */
class GamePlatformPullReportScheduleTransformer extends Transformer
{
    public function transform(GamePlatformPullReportSchedule $schedule)
    {
        return [
            'id'             => $schedule->id,
            'platform_code'  => $schedule->platform_code,
            'start_at'       => convert_time($schedule->start_at),
            'end_at'         => convert_time($schedule->end_at),
            'pulled_at'      => convert_time($schedule->pulled_at),
            'remarks'        => $schedule->remarks,
            'origin_total'   => $schedule->origin_total,
            'transfer_total' => $schedule->transfer_total,
            'display_status' => transfer_show_value($schedule->status, GamePlatformPullReportSchedule::$statuses),
            'times'          => $schedule->times,
            'status'         => $schedule->status,
        ];
    }
}
