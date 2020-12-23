<?php

namespace App\Models;

use OwenIt\Auditing\Contracts\Auditable;

class BankTransaction extends Model implements Auditable
{
    use \OwenIt\Auditing\Auditable;

    protected $auditInclude = [
        'deleted_at', 'deposit_id', 'amount',
    ];

    protected $casts = [
        'debit'     => 'float',
        'credit'    => 'float',
        'balance'   => 'float',
    ];

    protected $dates = [
        'transaction_at', 'deleted_at'
    ];

    const STATUS_NOT_MATCH    = 1;
    const STATUS_MATCH        = 2;


    public static $statuses = [
        self::STATUS_NOT_MATCH  => 'not match',
        self::STATUS_MATCH      => 'match',
    ];

    const HOUSEKEEP_ALL = 1;
    const HOUSEKEEP_YES = 2;
    const HOUSEKEEP_NO  = 3;

    public static $housekeeps = [
        self::HOUSEKEEP_ALL => 'ALL',
        self::HOUSEKEEP_YES => 'YES',
        self::HOUSEKEEP_NO  => 'NO',
    ];

    public static function boot()
    {
        parent::boot();

        static::created(function($model) {
            $model->update([
                'order_no' => static::findCreatedOrderNo(static::TXN_ID_STATEMENT, $model->id)
            ]);
        });
    }

    public function generateTags() : array
    {
        $data = $this->getUpdatedEventAttributes();
        return array_keys($data[1]);
    }

    public function scopeTransactionStartAt($query, $startAt)
    {
        return $query->where('transaction_date', '>=', $startAt);
    }

    public function scopeTransactionEndAt($query, $endAt)
    {
        return $query->where('transaction_date', '<=', $endAt);
    }

    public function scopeAmount($query, $value)
    {
        $value = remove_thousands_number($value);

        return $query->where(function($query) use ($value) {
            $query->where('debit', $value)->orWhere('credit', $value)->orWhere('balance', $value);
        });
    }

    public function scopeStatus($query, $value)
    {
        if($value) {
            if($value == 'all') {
                return $query;
            }
            return $query->where('status', $value);
        }
        else {
            return $query->where('status',  self::STATUS_NOT_MATCH);
        }
    }

    public function scopeHousekeep($query, $value)
    {
        switch ($value) {
            case static::HOUSEKEEP_YES:
                return $query->whereNotNull('deleted_at');
                break;
            case static::HOUSEKEEP_NO:
                return $query->whereNull('deleted_at');
                break;
            default:
                return $query;
                break;
        }
    }

    /**
     * 是否已经match
     *
     * @return bool
     */
    public function isMatched()
    {
        return static::STATUS_MATCH == $this->status;
    }

    public function isDeleted()
    {
        return !is_null($this->deleted_at);
    }

    /**
     * 获取最后一条交易记录
     *
     * @param $fundInAccount
     * @return \Illuminate\Database\Eloquent\Builder|\Illuminate\Database\Eloquent\Model|null|object
     */
    public static function findLastTransaction($fundInAccount)
    {
        return static::query()->where('fund_in_account', $fundInAccount)->orderByDesc('id')->first();
    }
}
