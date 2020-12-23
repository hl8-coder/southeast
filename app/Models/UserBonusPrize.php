<?php

namespace App\Models;
use App\Models\Traits\TurnoverRequirementTrait;

class UserBonusPrize extends Model
{
    use TurnoverRequirementTrait;

    public static $reportMappingType = Report::TYPE_CLOSE_BONUS_BET;

    protected $casts = [
        'is_max_prize'              => 'boolean',
        'deposit_amount'            => 'float',
        'prize'                     => 'float',
        'set'                       => 'array',
        'is_turnover_closed'        => 'boolean',
        'turnover_closed_value'     => 'float',
        'turnover_current_value'    => 'float',
    ];

    protected $dates = [
        'turnover_closed_at',
    ];

    # 状态
    const STATUS_CREATED    = 1;
    const STATUS_SUCCESS    = 2;
    const STATUS_FAIL       = 3;

    public static $statuses = [
        self::STATUS_CREATED => 'STATUS_CREATED',
        self::STATUS_SUCCESS => 'STATUS_SUCCESS',
        self::STATUS_FAIL    => 'STATUS_FAIL',
    ];

    public static $checkStatuses = [
        self::STATUS_CREATED,
        self::STATUS_SUCCESS,
    ];

    public function bonus()
    {
        return $this->belongsTo(Bonus::class);
    }

    public function remark()
    {
        return $this->belongsTo(Remark::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function turnoverRequirement()
    {
        return $this->morphOne(TurnoverRequirement::class, 'requireable');
    }

    public function scopeStartAt($query, $date)
    {
        return $query->whereHas('user', function ($query) use ($date) {
            $query->where('created_at', '>=', $date);
        });
    }

    public function scopeEndAt($query, $date)
    {
        return $query->whereHas('user', function ($query) use ($date) {
            $query->where('created_at', '<=', $date);
        });
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            $model->update([
                'order_no' => static::findCreatedOrderNo(static::TXN_ID_BONUS, $model->id)
            ]);
        });
    }

    # 方法 start
    public function fail()
    {
        $result = $this->setPrimaryKeyQuery()
                ->where('status', static::STATUS_CREATED)
                ->update([
                    'status' => static::STATUS_FAIL,
                ]);

        if ($result && !empty($this->turnoverRequirement)) {
            $this->turnoverRequirement->close();
        }

        return $result;
    }

    
    public function success()
    {
        return $this->setPrimaryKeyQuery()
            ->where('status', static::STATUS_CREATED)
            ->update([
                'status' => static::STATUS_SUCCESS,
            ]);
    }
    # 方法 end
}
