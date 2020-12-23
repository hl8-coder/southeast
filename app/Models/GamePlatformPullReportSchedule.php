<?php

namespace App\Models;

class GamePlatformPullReportSchedule extends Model
{
    protected $guarded = [];

    protected $dates = [
        'start_at', 'end_at', 'pulled_at',
    ];

    # 状态
    const STATUS_CREATED    = 1;
    const STATUS_PROCESSING = 2;
    const STATUS_SUCCESS    = 3;
    const STATUS_FAIL       = 4;

    public static $statuses = [
        self::STATUS_CREATED    => 'Created',
        self::STATUS_PROCESSING => 'Processing',
        self::STATUS_SUCCESS    => 'Successful',
        self::STATUS_FAIL       => 'Fail',
    ];

    public function platform()
    {
        return $this->belongsTo(GamePlatform::class, 'platform_code', 'code');
    }

    public function start()
    {
        return $this->update([
            'status' => static::STATUS_PROCESSING,
        ]);
    }

    public function success($originTotal, $transferTotal)
    {
        $this->update([
            'status'         => static::STATUS_SUCCESS,
            'pulled_at'      => now(),
            'origin_total'   => $originTotal,
            'transfer_total' => $transferTotal,
            'times'          => $this->times + 1,
        ]);
    }

    public function fail($remarks='')
    {
        $this->update([
            'status'   => static::STATUS_FAIL,
            'remarks'  => $remarks,
            'times'    => $this->times + 1,
        ]);

    }

    public function scopeMissionScopeStart($query, $start)
    {
        return $query->where('start_at', '>=', $start);
    }

    public function scopeMissionScopeEnd($query, $end)
    {
        return $query->where('start_at', '<=', $end);
    }
}
