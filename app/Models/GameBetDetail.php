<?php

namespace App\Models;

use Illuminate\Support\Facades\DB;

class GameBetDetail extends Model
{
    const JOB_NUM = 3;

    protected $guarded = [];

    protected $dates = [
        'bet_at', 'finished_at', 'payout_at',
    ];

    protected $casts = [
        'is_close'              => 'boolean',
        'is_check_open'         => 'boolean',
        'trace_logs'            => 'array',
        'stake'                 => 'float',
        'bet'                   => 'float',
        'prize'                 => 'float',
        'profit'                => 'float',
        'after_balance'         => 'float',
        'user_bet'              => 'float',
        'user_stake'            => 'float',
        'user_prize'            => 'float',
        'user_profit'           => 'float',
        'platform_profit'       => 'float',
        'jpc'                   => 'float',
        'jpw'                   => 'float',
        'jpw_jpc'               => 'float',
        'available_bet'         => 'float',
        'available_profit'      => 'float',
        'available_rebate_bet'  => 'float',
    ];

    # 所有字段名称.
    protected $fields = array(
        'id',
        'platform_code',
        'product_code',
        'platform_currency',
        'order_id',
        'game_type',
        'game_code',
        'game_name',
        'user_id',
        'user_name',
        'issue',
        'stake',
        'bet',
        'prize',
        'profit',
        'odds',
        'after_balance',
        'bet_at',
        'payout_at',
        'user_currency',
        'user_bet',
        'user_stake',
        'user_prize',
        'user_profit',
        'platform_profit',
        'multiple',
        'money_unit',
        'bet_info',
        'win_info',
        'user_prize_group',
        'available_bet',
        'available_profit',
        'available_rebate_bet',
        'jpc',
        'jpw',
        'jpw_jpc',
        'is_close',
        'platform_status',
        'status',
        'finished_at',
        'remark',
        'trace_logs',
        'created_at',
        'updated_at'
    );

    # status
    const STATUS_CREATED = 1; //未处理
    const STATUS_PROCESS = 2; //处理中
    const STATUS_SUCCESS = 3; //处理成功
    const STATUS_FAIL    = 4; //处理失败

    public static $statuses = [
        self::STATUS_CREATED => 'created',
        self::STATUS_PROCESS => 'process',
        self::STATUS_SUCCESS => 'success',
        self::STATUS_FAIL    => 'fail',
    ];

    # platform status
    const PLATFORM_STATUS_BET_SUCCESS   = 1;
    const PLATFORM_STATUS_BET_FAIL      = 2;
    const PLATFORM_STATUS_WAITING       = 3;
    const PLATFORM_STATUS_CANCEL        = 4;

    public static $platformStatuses = [
        self::PLATFORM_STATUS_BET_SUCCESS => 'success',
        self::PLATFORM_STATUS_BET_FAIL    => 'fail',
        self::PLATFORM_STATUS_WAITING     => 'waiting',
        self::PLATFORM_STATUS_CANCEL      => 'cancel',
    ];

    # 模型关联 start
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function game()
    {
        return $this->belongsTo(Game::class, 'game_code', 'code');
    }
    # 模型关联 end

    # 查询作用域 start
    public function scopeStartAt($query, $value)
    {
        return $query->where('bet_at', '>=', $value);
    }

    public function scopeEndAt($query, $value)
    {
        return $query->where('bet_at', '<=', $value);
    }
    public function scopePayoutStartAt($query, $value)
    {
        return $query->where('payout_at', '>=', $value);
    }

    public function scopePayoutEndAt($query, $value)
    {
        return $query->where('payout_at', '<=', $value);
    }
    # 查询作用域 end

    # 方法 start
    public function isClose()
    {
        return $this->is_close;
    }

    public function isProcess()
    {
        return $this->status == self::STATUS_PROCESS;
    }

    public function isCheckOpen()
    {
        return $this->is_check_open;
    }

    public function start()
    {
        return $this->update([
            'status' => self::STATUS_PROCESS
        ]);
    }

    public function success()
    {
        $this->setPrimaryKeyQuery()
            ->where('status', static::STATUS_PROCESS)
            ->update([
                'status' => self::STATUS_SUCCESS,
                'finished_at' => now(),
            ]);
    }

    public function fail($remark)
    {
        $this->setPrimaryKeyQuery()
            ->where('status', self::STATUS_PROCESS)
            ->update([
                'status' => self::STATUS_FAIL,
                'remark' => $remark,
            ]);
    }

    public function close()
    {
        return $this->setPrimaryKeyQuery()
            ->where('is_close', false)
            ->update([
                'is_close'      => true,
                'available_bet' => 0
            ]);
    }

    /**
     * 是否是未开奖注单
     *
     * @return bool
     */
    public function isBetWaiting()
    {
        return static::PLATFORM_STATUS_WAITING == $this->platform_status;
    }

    /**
     * 是否投注成功
     *
     * @return bool
     */
    public function isBetSuccess()
    {
        return static::PLATFORM_STATUS_BET_SUCCESS == $this->platform_status;
    }

    /**
     * 更新可用投注金额
     *
     * @param $availableBet
     * @return bool
     */
    public function updateAvailableBet($availableBet)
    {
        return $this->update([
            'available_bet' => $availableBet,
        ]);
    }

    /**
     * 添加投注明细追踪日志
     *
     * @param   object         $model         理由
     * @param   float          $usedAmount    使用金额
     * @return  bool
     */
    public function addTraceLog($model, $usedAmount)
    {
        $log = now()->toDateTimeString() . ' : ' . ' reason : ' . get_class($model) . ', used amount: ' . $usedAmount . ' , trace id: ' . $model->id;

        $traceLogs = !empty($this->trace_logs) ? $this->trace_logs : [];
        array_push($traceLogs, $log);
        $this->trace_logs = $traceLogs;

        return $this->save();
    }
    # 方法 end

    private function setUnionTable($wheres = [], $attributes = ['*'])
    {
        //约束条件
        $whereConditions = [];

        //涉及的表数组
        $tables = [];

        if (!empty($wheres)) {
            foreach ($wheres as $val) {
                //组装每个where条件

                if ($val[1] == "in") {
                    $whereIn = "(".implode(',',$val[2]).")";
                    $whereConditions[] = "and {$val[0]} in $whereIn";
                } else {
                    $whereConditions[] = " and {$val[0]} {$val[1]} '{$val[2]}'";
                }
            }
        }

        if ($attributes == ['*']) {
            $attributes = $this->fields;
        }


        //循环开始日期和结束日期计算跨越的表
        $tables = 'select ' . implode(',', $attributes) . ' from game_bet_details where 1 ' . implode('', $whereConditions);
//        $tables[] = 'select ' . implode(',', $attributes) . ' from game_bet_history_details where 1 ' . implode('', $whereConditions);

        return $this->setTable(DB::raw('(' . $tables . ') as game_data_table'));
    }

    /**
     * 获取单条数据.
     *
     * @param array $where   二位数组 [['field1','=|>=|<=|%',$value1],['field2','=|>=|<=|%',$value2]].
     * @param string $orderBy 排序字段 "id desc" 用空格分开
     * @param array $attributes 默认['*'] ex: ['id','user_id'....]
     *
     * @return array
     */
    public function getOne($where = array(), $orderBy = 'id desc', $attributes = ['*'])
    {
        $data = array();

        $query = $this->setUnionTable($where,$attributes);

        if (!empty($attributes) && $attributes[0] != "*") {
            $query->select(DB::raw(implode(',', $attributes)));
        }

        if (!empty($orderBy)) {
            $orderBy = explode(' ', $orderBy);

            if (!empty($orderBy[0]) && !empty($orderBy[1]) && in_array(strtolower($orderBy[1]),array('asc','desc'))) {
                $data = $query->orderBy($orderBy[0],$orderBy[1])->first();
            }
        } else {
            $data = $query->first();
        }

        return $data;
    }

    /**
     * 一次获取所有数据.
     *
     * @param array $where
     * @param string $orderBy
     * @param array $attributes
     *
     * @return array
     */
    public function getAll($where = array(), $orderBy = 'id desc', $attributes = ['*'],$limit=10000,$returnType='array')
    {
        $data = array();

        # 联表查询只获取设计主键或需要排序的字段, 提升查询效率
        $private_attributes = ['id'];

        if (!empty($orderBy)) {
            $orderBy = explode(' ', $orderBy);

            if (!empty($orderBy[0]) && !empty($orderBy[1]) && in_array(strtolower($orderBy[1]), array('asc', 'desc'))) {
                $private_attributes[] = $orderBy[0];
                $query = $this->setUnionTable($where, $private_attributes);

                $data = $query->orderBy($orderBy[0], $orderBy[1])->limit($limit)->get($private_attributes);
            }
        } else {
            $query = $this->setUnionTable($where, $private_attributes);

            $data = $query->limit($limit)->get($private_attributes);
        }

        // 获取数据集合的id.
        $idList = array();

        if (!empty($data)) {
            foreach ($data as $info) {
                $idList[] = $info->id;
            }
        }

        // 获取额外信息.
        if (!empty($idList)) {
            # 通过主键去获取需要字段.
            $ext_attribute_list =$this->setUnionTable(array(['id','in',$idList]))->get($attributes);

            $ext_attribute_list_by_id = array();
            if (!empty($ext_attribute_list)) {
                foreach ($ext_attribute_list as $ext_info) {
                    $ext_attribute_list_by_id[$ext_info['id']] = $ext_info;
                }
            }
            # 整合数据.
            if (!empty($data) && !empty($ext_attribute_list_by_id)) {

                foreach ($data as &$data_info) {
                    $id = $data_info['id'];
                    if ($ext_attribute_list_by_id[$id]) {
                        foreach ($attributes as  $attribute) {
                            $data_info->$attribute = $ext_attribute_list_by_id[$id][$attribute];
                        }
                    }
                }
            }
        }

        if (!empty($data) && $returnType == "array") {
            $data = $data->toArray();
        }

        return $data;
    }

    /**
     * 采用laravel的paginate分页方式获取分页数据.
     *
     * @param array $where
     * @param int $size
     * @param string $orderBy
     * @param array $attributes
     *
     * @return mixed
     */
    public function getListWithPaginate($where=array(), $size = 10, $orderBy = '', $attributes = ['*'])
    {
        $list = array();

        $size >= 1 ? $size = (integer)$size : $size = 50;

        # 联表查询只获取设计主键或需要排序的字段, 提升查询效率
        $private_attributes = ['id'];

        if (!empty($orderBy)) {
            $orderBy = explode(' ', $orderBy);

            if (!empty($orderBy[0]) && !empty($orderBy[1]) && in_array(strtolower($orderBy[1]), array('asc', 'desc'))) {
                $private_attributes[] = $orderBy[0];
                $query = $this->setUnionTable($where, $private_attributes);

                $list = $query->orderBy($orderBy[0], $orderBy[1])->paginate($size,$private_attributes);
            }
        } else {
            $query = $this->setUnionTable($where, $private_attributes);

            $list = $query->paginate($size,$private_attributes);
        }

        // 获取数据集合的id.
        $idList = array();

        if (!$list->isEmpty()) {
            foreach ($list as $info) {
                $idList[] = $info->id;
            }
        }

        // 获取额外信息.
        if (!empty($idList)) {
            # 通过主键去获取需要字段.
            $ext_attribute_list =$this->setUnionTable(array(['id','in',$idList]))->get($attributes);

            # 整合数据.
            if (!$list->isEmpty() && !empty($ext_attribute_list)) {
                foreach ($ext_attribute_list as $ext_attribute_info) {
                    foreach ($list as &$list_info) {
                        $ext_attribute_info_id = $ext_attribute_info->id;
                        $list_info_id = $list_info->id;
                        if ($ext_attribute_info_id == $list_info_id) {
                            foreach ($attributes as  $attribute) {
                                $list_info->$attribute = $ext_attribute_info[$attribute];
                            }
                        }
                    }
                }
            }
        }

        return $list;
    }

    /**
     * 自定义分页方式获取分页数据.
     *
     * @param int $page
     * @param int $size
     * @param array $where
     * @param string $orderBy
     * @param array $attributes
     *
     * @return array
     */
    public function getListWithSelfPage($page=1, $size = 10, $where = array(), $orderBy = 'id desc', $attributes = ['*'])
    {
        $list = array();

        if ($page < 1) {
            $page = 1;
        }

        if ($size <= 0) {
            $size = 10;
        }

        $page = (int)$page;
        $size = (int)$size;

        $pageInfo = array(
            'total_cnt' => 0, // 总条数
            'page' => $page,  // 当前页
            'size' => $size,  // 每页条数
            'page_cnt' => 0,  // 总页数
        );

        $query = $this->setUnionTable($where, $attributes);

        $count = $query->count();


        // 数据为空.
        if (!$count) {
            return array('pageInfo' => $pageInfo, 'list' => $list);
        }

        $pageInfo['total_cnt'] = $count;

        $pageInfo['page_cnt'] = ceil($count / $size);

        $offset = ($page - 1) * $size;


        if (!empty($attributes) && $attributes[0] !== "*") {
            $query->select(DB::raw(implode(',', $attributes)));
        }

        if (!empty($orderBy)) {
            $orderBy = explode(' ', $orderBy);

            if (!empty($orderBy[0]) && !empty($orderBy[1]) && in_array(strtolower($orderBy[1]), array('asc', 'desc'))) {
                $list = $query->orderBy($orderBy[0], $orderBy[1])->offset($offset)->limit($size)->get();
            }
        } else {
            $list = $query->offset($offset)->limit($size)->get();
        }


        if (!empty($list)) {
            $list = $list->toArray();
        }

        return array('page_info' => $pageInfo, "list" => $list);
    }

    // 获取一个字段的和值.
    public function getSum($field, $where = array())
    {
        $query = $this->setUnionTable($where,[$field]);

        $sum = $query->sum($field);

        return $sum;
    }

    // 获取参与的游戏人数(去重)
    public function getUniqueMemberNum($where = array())
    {
        $field = "user_name";
        $query = $this->setUnionTable($where, [$field]);

        $info = $query->select(DB::raw('count(distinct user_name) as total_member'))->first();

        return $info->total_member;
    }
}
