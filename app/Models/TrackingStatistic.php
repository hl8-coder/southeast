<?php

namespace App\Models;

use App\Models\Traits\FlushCache;
use App\Models\Traits\RememberCache;

class TrackingStatistic extends Model
{
    use RememberCache, FlushCache;

    protected $rememberCacheTag = 'tracking_statistic';

    public static $cacheExpireInMinutes = 43200;

    protected $fillable = [
        'tracking_name', 'date', 'user_id', 'user_name'
    ];

    # 模型关联 start
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function trackingStatisticLogs()
    {
        return $this->hasMany(TrackingStatisticLog::class, 'tracking_id');
    }
    # 模型关联 end

    public static function getDropList($column1 = 'tracking_name', $column2 = 'id')
    {
        return static::getAll()->pluck($column1, $column2)->toArray();
    }

    # 作用域 start
    public function scopeName($query, $value)
    {
        return $query->whereHas('user', function($query) use ($value) {
            $query->where('name', $value);
        });
    }

    public function scopeCode($query, $value)
    {
        return $query->whereHas('user', function($query) use ($value) {
            $query->where('affiliate_code', $value);
        });
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function($query) use ($value) {
            $query->where('currency', $value);
        });
    }

    public function scopeStatus($query, $value)
    {
        return $query->whereHas('user', function($query) use ($value) {
            $query->where('status', $value);
        });
    }

    public function scopeStartAt($query, $value)
    {
        return $query->whereHas('trackingStatisticLogs', function($query) use ($value) {
            $query->where('created_at', '>=', $value);
        });
    }

    public function scopeEndAt($query, $value)
    {
        return $query->whereHas('trackingStatisticLogs', function($query) use ($value) {
            $query->where('created_at', '<=', $value);
        });
    }
    # 作用域 end
}
