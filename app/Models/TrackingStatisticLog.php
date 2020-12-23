<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TrackingStatisticLog extends Model
{
    protected $fillable = [
        'tracking_id', 'ip', 'affiliate_code', 'type', 'url'
    ];

    # type
    const TYPE_RESOURCE = 1;    # Banner资源
    const TYPE_PROMOTION = 2;   # 下级注册

    # 模型关联 start
    public function trackingStatistic()
    {
        return $this->belongsTo(TrackingStatistic::class, 'tracking_id', 'id');
    }
    # 模型关联 end

    # 作用域 start
    public function scopeUserName($query, $value)
    {
        return $query->whereHas('trackingStatistic', function($query) use ($value) {
            $query->whereHas('user', function($query) use ($value) {
                $query->where('name', $value);
            });
        });
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('trackingStatistic', function($query) use ($value) {
            $query->whereHas('user', function($query) use ($value) {
                $query->where('currency', $value);
            });
        });
    }

    public function scopeCode($query, $value)
    {
        return $query->whereHas('trackingStatistic', function($query) use ($value) {
            $query->whereHas('user', function($query) use ($value) {
                $query->where('affiliate_code', $value);
            });
        });
    }

    public function scopeTrackingName($query, $value)
    {
        return $query->whereHas('trackingStatistic', function($query) use ($value) {
            $query->where('tracking_name', $value);
        });
    }

    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }
    # 作用域 end
}
