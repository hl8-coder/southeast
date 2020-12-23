<?php

namespace App\Console\Commands;

use App\Models\GamePlatform;
use App\Models\GamePlatformPullReportSchedule;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class GenerateGamePlatformPullReportSchedulesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:generate-game-platform-pull-report-schedules {--start_at=} {--days=6} {--platform_code=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate game platform pull report schedules';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $startAt        = $this->option('start_at');
        $days           = $this->option('days');
        $platformCode   = $this->option('platform_code');

        if ($platformCode) {
            $platforms = GamePlatform::getAll()->where('status', true)->where('code', $platformCode);
        } else {
            $platforms = GamePlatform::getAll()->where('status', true);
        }

        # 生成拉取时间表
        foreach ($platforms as $platform) {
            $this->createSchedules($platform, $startAt, $days);
        }
        return;
    }

    public function createSchedules(GamePlatform $platform, $startAt, $days)
    {
        if (!$interval = $platform->interval) {
            return;
        }
        # 如果未填开始时间，默认是是从最后一条记录开始生成
        if (!$startAt) {
            if ($lastSchedule = $platform->findLastSchedule()) {
                $startAt = $lastSchedule->start_at->addMinutes($interval);
            } else {
                $startAt = now()->startOfDay();
            }
        } else {
            $startAt = Carbon::parse($startAt)->startOfMinute();
            # 获取平台拉取成功最后一期schedule
            if ($lastSuccessSchedule = $platform->findLastSuccessSchedule()) {
                $startAt = $startAt <= $lastSuccessSchedule->start_at
                    ? $lastSuccessSchedule->start_at->addMinutes($interval)
                    : $startAt;
            }
            # 删除未拉取状态的时间表
            $platform->deleteSchedule($startAt);

        }
        $endAt = $startAt->copy()->addDays($days)->endOfDay();

        $schedules = [];
        # 生成schedule
        while ($startAt < $endAt) {
            $schedules[] = [
                'platform_code' => $platform->code,
                'start_at'      => $startAt->toDateTimeString(),
                'end_at'        => $startAt->copy()->addMinutes($interval)->toDateTimeString(),
                'status'        => GamePlatformPullReportSchedule::STATUS_CREATED,
            ];
            $startAt = $startAt->addMinutes($interval);
        }

        # 将数据分块，避免一次插入太大数据量
        foreach (array_chunk($schedules, 1000) as $chunkSchedules) {
            DB::table('game_platform_pull_report_schedules')->insert($chunkSchedules);
        }

        Log::stack(['game_platform_report_schedules'])->info($platform->name . ' 平台生成拉取时间表成功！');

    }
}
