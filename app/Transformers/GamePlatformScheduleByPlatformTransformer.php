<?php


namespace App\Transformers;


use App\Models\GamePlatformPullReportSchedule;

/**
 * @OA\Schema(
 *   schema="GamePlatformScheduleByPlatform",
 *   type="object",
 *   @OA\Property(property="platform_code", type="string", description="游戏平台code"),
 *   @OA\Property(property="fail", type="integer", description="失败条数"),
 *   @OA\Property(property="has_created", type="integer", description="是否有Created状态的数据，有则显示绿色，反之红色"),
 *   @OA\Property(property="is_error", type="boolean", description="Created的End At是否与现在的时间相差一个小时，1 => 显示红色"),
 * )
 */
class GamePlatformScheduleByPlatformTransformer extends Transformer
{
    public function transform($data)
    {
        $isError = 0;
        if ($data['has_created']) {
            $schedule = GamePlatformPullReportSchedule::query()
                ->where([
                    ['platform_code', $data['platform_code']],
                    ['status', GamePlatformPullReportSchedule::STATUS_CREATED],
                ])
                ->orderBy('end_at')
                ->first();
            // 时间间隔大于60证明是错的
            if ($schedule->end_at->diffInMinutes(now()) > 60) {
                $isError = 1;
            }
        }

        $info                  = [];
        $info['platform_code'] = $data['platform_code'];
        $info['fail']          = $data['fail'];
        $info['has_created']   = $data['has_created'];
        $info['is_error']      = $isError;

        return $info;
    }
}