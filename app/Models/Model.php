<?php

namespace App\Models;

use App\Services\DepositService;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Watson\Rememberable\Rememberable;

class Model extends EloquentModel
{
    use Rememberable;

    protected $perPage = 50;

    protected $guarded = [];

    # order no
    const TXN_ID_DEPOSIT        = 1;
    const TXN_ID_WITHDRAWAL     = 2;
    const TXN_ID_ADJUSTMENT     = 3;
    const TXN_ID_AFF_TOP_UP     = 4;
    const TXN_ID_STATEMENT      = 5;
    const TXN_ID_TRANSFER       = 6;
    const TXN_ID_AFF_DEPOSIT    = 7;
    const TXN_ID_BONUS          = 8;
    const TXN_ID_TOPUP          = 9;

    public static $booleanDropList = [
        '1' => 'YES',
        '0' => 'NO',
    ];

    public static $booleanStatusesDropList = [
        '1' => 'active',
        '0' => 'inactive',
    ];

    public static $booleanStatusesDropListSuccessFailed = [
        '1' => 'Pending',
        '2' => 'Hold',
        '3' => 'Successful',
        '4' => 'Failed',
    ];

    public static $deviceDropList = [
        '1' => 'PC',
        '2' => 'Mobile Browser',
        '3' => 'Mobile App',
    ];

    public static $languageToCurrency = [
        'zh-CN' => "CNY",
        'en-US' => "USD",
        'vi-VN' => "VND",
        'th'    => "THB",
    ];

    public function scopeEnable($query)
    {
        return $query->where('status', true);
    }

    public function scopeSort($query)
    {
        return $query->orderBy('sort');
    }

    public function scopeSortByDesc($query)
    {
        return $query->orderBy('sort', 'desc');
    }

    public function scopeFindInSet($query, $groupField, $search)
    {
        return $query->whereRaw("FIND_IN_SET(?, {$groupField})", $search);
    }

    public function scopeStartAt($query, $value)
    {
        return $query->where('created_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('created_at', '<=', $value);
    }

    /**
     * 设定主键查询构造器
     *
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function setPrimaryKeyQuery()
    {
        return $this->newQuery()->where($this->getKeyName(), $this->getKey());
    }

    /**
     * 获取订单号
     *
     * @param $prefix
     * @param $modelId
     * @return string
     */
    public static function findCreatedOrderNo($prefix, $modelId)
    {
        return $prefix . str_pad($modelId, 10, '0', STR_PAD_LEFT);
    }

    /**
     * 根据订单号获取订单
     *
     * @param $orderNo
     * @return \Illuminate\Database\Eloquent\Builder|EloquentModel|null|object
     */
    public static function findByOrderNo($orderNo)
    {
        return static::query()->where('order_no', $orderNo)->first();
    }


    /**
     * 批量更新数据，但是批量更新并不会触发 saved 与 updated 事件
     *
     * @param array $multipleData
     * @return bool|int
     */
    public static function updateBatch($multipleData = [])
    {
        $classNameSpace = get_called_class();
        $model          = new $classNameSpace;
        if (!$model instanceof \Illuminate\Database\Eloquent\Model) {
            return false;
        }
        try {
            if (empty($multipleData)) {
                return false;
            }
            $tableName = DB::getTablePrefix() . $model->getTable(); // 表名
            $firstRow  = current($multipleData);

            $updateColumn = array_keys($firstRow);
            // 默认以id为条件更新，如果没有ID则以第一个字段为条件
            $referenceColumn = isset($firstRow['id']) ? 'id' : current($updateColumn);
            unset($updateColumn[0]);
            // 拼接sql语句
            $updateSql = "UPDATE " . $tableName . " SET ";
            $sets      = [];
            $bindings  = [];
            foreach ($updateColumn as $uColumn) {
                $setSql = "`" . $uColumn . "` = CASE ";
                foreach ($multipleData as $data) {
                    $setSql     .= "WHEN `" . $referenceColumn . "` = ? THEN ? ";
                    $bindings[] = $data[$referenceColumn];
                    $bindings[] = $data[$uColumn];
                }
                $setSql .= "ELSE `" . $uColumn . "` END ";
                $sets[] = $setSql;
            }
            $updateSql .= implode(', ', $sets);
            $whereIn   = collect($multipleData)->pluck($referenceColumn)->values()->all();
            $bindings  = array_merge($bindings, $whereIn);
            $whereIn   = rtrim(str_repeat('?,', count($whereIn)), ',');
            $updateSql = rtrim($updateSql, ", ") . " WHERE `" . $referenceColumn . "` IN (" . $whereIn . ")";
            // 传入预处理sql语句和对应绑定数据
            return DB::update($updateSql, $bindings);
        } catch (\Exception $e) {
            Log::error($e);
            return false;
        }
    }
}
