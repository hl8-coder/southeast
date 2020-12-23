<?php

namespace App\Models;

use App\Models\Traits\TurnoverRequirementTrait;

class TransferDetail extends Model
{
    use TurnoverRequirementTrait;

    public static $reportMappingType = Report::TYPE_CLOSE_DEPOSIT_BET;

    protected $guarded = [];

    protected $casts = [
        'from_before_balance'   => 'float',
        'from_after_balance'    => 'float',
        'amount'                => 'float',
        'is_turnover_closed'     => 'boolean',
        'turnover_closed_value'  => 'float',
        'turnover_current_value' => 'float',
    ];

    protected $dates = [
        'turnover_closed_at', 'verified_at',
    ];

    const STATUS_CREATED = 1;
    const STATUS_SUCCESS = 2;
    const STATUS_FAIL    = 3;

    public static $statuses = [
        self::STATUS_CREATED    => 'Created',
        self::STATUS_SUCCESS    => 'Successful',
        self::STATUS_FAIL       => 'Fail',
    ];

    public static $checkStatuses = [
        self::STATUS_CREATED, self::STATUS_SUCCESS,
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function turnoverRequirement()
    {
        return $this->morphOne(TurnoverRequirement::class, 'requireable');
    }

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            $model->update([
                'order_no' => static::findCreatedOrderNo(static::TXN_ID_TOPUP, $model->id)
            ]);
        });
    }


    public static function add(User $fromUser, User $toUser, $amount)
    {
        $detail = new TransferDetail();

        $detail->user_id        = $fromUser->id;
        $detail->user_name      = $fromUser->name;
        $detail->to_user_id     = $toUser->id;
        $detail->to_user_name   = $toUser->name;
        $detail->amount         = $amount;
        $detail->status         = static::STATUS_CREATED;

        # 流水要求
        $detail->is_turnover_closed    = false;
        $detail->turnover_closed_value = $amount;

        $detail->save();

        return $detail;
    }

    public function success()
    {
        return $this->update([
            'status' => static::STATUS_SUCCESS,
        ]);
    }

    public function fail($remark)
    {
        return $this->update([
            'status' => static::STATUS_FAIL,
            'remark' => $remark,
        ]);
    }

    /**
     * 根据会员id统计在某段时间内红利总数或者未关闭的转账
     *
     * @param $userId
     * @param $endAt
     * @param null $startAt
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function getByUserIdAndTime($userId, $endAt, $startAt=null)
    {
        $builder = static::query()->where('to_user_id', $userId)
            ->whereIn('status', static::$checkStatuses)
            ->where('created_at', '<=', $endAt);

        if (!is_null($startAt)) {
            $builder->where('created_at', '>', $startAt);
        }

        return $builder->get();
    }

    public function toUser()
    {
        return $this->belongsTo(User::class, 'to_user_id');
    }

    # 作用域 start
    public function scopeStartAt($query, $date)
    {
        return $query->where('created_at', '>=', $date);
    }

    public function scopeEndAt($query, $date)
    {
        return $query->where('created_at', '<=', $date);
    }

    public function scopeIsAgent($query, $value)
    {
        return $query->whereHas('toUser', function ($query) use ($value) {
            return $query->where("is_agent", $value);
        });
    }

    public function scopeCode($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            return $query->where("affiliate_code", $value);
        });
    }

    public function scopeCurrency($query, $value)
    {
        return $query->whereHas('user', function ($query) use ($value) {
            return $query->where('currency', $value);
        });
    }
    # 作用域 end
}
