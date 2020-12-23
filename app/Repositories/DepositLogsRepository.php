<?php


namespace App\Repositories;


use App\Models\Deposit;
use App\Models\DepositLog;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class DepositLogsRepository
{
    # 获取有效的日志记录。（有效日志：除去后台创建的Created类型和访问记录Access类型）
    public static function getEffectiveLogs(Request $request)
    {
        return DepositLog::query()
            ->where('type', '<>', DepositLog::TYPE_ACCESS)
            ->where('type', '<>', DepositLog::TYPE_CREATED)
            ->leftJoin(DB::Raw("(select `deposits`.`currency`, `deposits`.`id`, `deposits`.`user_id`, `deposits`.`order_no`, `deposits`.`created_at` as 't_date', `deposits`.`payment_type`, `deposits`.`status`, `users`.`name` from `deposits` left join users on users.id = deposits.user_id) as `relation`"), 'deposit_logs.deposit_id', '=', 'relation.id')
            ->select('admin_name', 'deposit_id', 'currency', 'name', 'order_no', 't_date', 'payment_type', 'status')
            ->where(function ($query) use ($request) {
                if (isset($request->filter['start_at']) && !empty($request->filter['start_at'])) {
                    $query->where('t_date', '>=', $request->filter['start_at']);
                }
                if (isset($request->filter['end_at']) && !empty($request->filter['end_at'])) {
                    $query->where('t_date', '<=', $request->filter['end_at']);
                }
                if (isset($request->filter['currency']) && !empty($request->filter['currency'])) {
                    $query->where('currency', $request->filter['currency']);
                }
            })
            ->groupBy('deposit_id', 'admin_name')
            ->orderByDesc('deposit_id')
            ->get();
    }

    /**
     * 计算 admin 的 processing_time 和 holding_time
     * 公式: Processing Time = admin最后一次操作时间 - admin第一次访问时间 - Holding Time
     *      Holding Time = admin hold 操作 到下一次release hold 的间隔时间
     *      *** hold time 特殊情况: 某张充值单A在00:00:00执行hold，B在00:00:10执行了release hold，则A和B的Holding Time都为10
     * @param $depositID
     * @param $admin
     * @param $depositCreatedTime
     * @return array
     */
    public static function calculateLogTime($depositID, $admin, $depositCreatedTime)
    {
        $logs           = DepositLog::query()
            ->where([
                ['type', '<>', DepositLog::TYPE_CREATED],
                ['deposit_id', $depositID],
                ['admin_name', $admin],
            ])
            ->orderBy('created_at', 'asc')
            ->get();
        $lastTime       = '';
        $holdingTime    = 0;
        $holdTime       = '';
        $matchTime      = '';
        $unMatchTime    = 0;
        $processingTime = 0;
        foreach ($logs as $log) {
            if ($log->type == DepositLog::TYPE_HOLD) {
                $holdTime = $log->created_at;
                # 检查接下来执行release hold的admin, 如果是该admin自己执行的，则跳过，如果不是，则计算holding time，并清除hold time
                $nextReleaseHold = DepositLog::query()
                    ->where([
                        ['type', DepositLog::TYPE_RELEASE_HOLD],
                        ['deposit_id', $depositID],
                        ['id', '>', $log->id],
                    ])
                    ->orderBy('id', 'asc')
                    ->first();
                if ($nextReleaseHold && $nextReleaseHold->admin_name != $admin) {
                    $holdingTime += $log->created_at->diffInSeconds($nextReleaseHold->created_at);
                    $holdTime    = '';
                }
                continue;
            }
            if ($log->type == DepositLog::TYPE_RELEASE_HOLD) {
                # 判断是否存在hold time，不存在则证明上次hold不是该admin操作，需要获取时间之后再计算holding time
                if ($holdTime) {
                    $holdingTime += $log->created_at->diffInSeconds($holdTime);
                    $holdTime    = '';
                } else {
                    $preHold = DepositLog::query()
                        ->where([
                            ['type', DepositLog::TYPE_HOLD],
                            ['deposit_id', $depositID],
                            ['id', '<', $log->id],
                        ])
                        ->orderByDesc('id')
                        ->first();
                    if ($preHold && $preHold->admin_name != $admin) {
                        $holdingTime += $log->created_at->diffInSeconds($preHold->created_at);
                    }
                }
                continue;
            }
            if ($log->type == DepositLog::TYPE_MATCH) {
                $lastTime = $log->created_at;
                $matchTime = $log->created_at;
                continue;
            }
            if ($log->type == DepositLog::TYPE_UNMATCH) {
                if ($matchTime) {
                    $unMatchTime += $log->created_at->diffInSeconds($matchTime);
                    $matchTime   = '';
                } else {
                    $preMatch = DepositLog::query()
                        ->where([
                            ['type', DepositLog::TYPE_MATCH],
                            ['deposit_id', $depositID],
                            ['id', '<', $log->id],
                        ])
                        ->orderByDesc('id')
                        ->first();
                    if ($preMatch && $preMatch->admin_name != $admin) {
                        $unMatchTime += $log->created_at->diffInSeconds($preMatch->created_at);
                    }
                }
                continue;
            }
            if ($log->type != DepositLog::TYPE_ACCESS) {
                $lastTime = $log->created_at;
            }
        }
        if ($lastTime) {
            $wholeTime      = Carbon::parse($lastTime)->diffInSeconds($depositCreatedTime);
            $processingTime = (int)$wholeTime - (int)$holdingTime;
        }

        $data                    = [];
        $data['holding_time']    = $holdingTime;
        $data['processing_time'] = $processingTime;
        $data['un_match_time']   = $unMatchTime;
        return $data;
    }

    public static function calculateTime($allLogs,$adminName,$depositCreatedTime)
    {
        $selfLogs = collect($allLogs)
            ->where('type',"!=",DepositLog::TYPE_CREATED)
            ->where('admin_name',"=",$adminName)
            ->toArray();


        $lastTime       = '';
        $holdingTime    = 0;
        $holdTime       = '';
        $matchTime      = '';
        $unMatchTime    = 0;
        $processingTime = 0;

        foreach ($selfLogs as $log) {

            if ($log['admin_name'] != $adminName) {
                continue;
            }

            $type = $log['type'];

            if (in_array($type, array(DepositLog::TYPE_ACCESS, DepositLog::TYPE_CREATED))) { // DepositLog::TYPE_CREATED的记录 和 DepositLog::TYPE_ACCESS 的记录不导出 但是计算时间时 涉及DepositLog::TYPE_ACCESS的记录
                continue;
            }

                if ($type == DepositLog::TYPE_HOLD) {
                    $holdTime = $log['created_at'];

                    # 检查接下来执行release hold的admin, 如果是该admin自己执行的，则跳过，如果不是，则计算holding time，并清除hold time
                    $nextReleaseHold = collect($allLogs)
                        ->where('type','=',DepositLog::TYPE_RELEASE_HOLD)
                        ->where('id','>',$log['id'])
                        ->sortBy('id')
                        ->first();

                    if (!empty($nextReleaseHold) && $nextReleaseHold['admin_name'] != $adminName) {
                        $holdingTime += Carbon::parse($log['created_at'])->diffInSeconds($nextReleaseHold['created_at']);
                        $holdTime    = '';
                    }
                    continue;
                }

                if ($type == DepositLog::TYPE_RELEASE_HOLD) {
                    # 判断是否存在hold time，不存在则证明上次hold不是该admin操作，需要获取时间之后再计算holding time
                    if ($holdTime) {
                        $holdingTime += Carbon::parse($log['created_at'])->diffInSeconds($holdTime);
                        $holdTime    = '';
                    } else {
                        $preHold = collect($allLogs)
                            ->where('type','=',DepositLog::TYPE_HOLD)
                            ->where('id','<',$log['id'])
                            ->sortByDesc('id')
                            ->first();
                        if (!empty($preHold) && $preHold['admin_name'] != $adminName) {
                            $holdingTime += Carbon::parse($log['created_at'])->diffInSeconds($preHold['created_at']);
                        }
                    }
                    continue;
                }
            if ($type == DepositLog::TYPE_MATCH) {
                $lastTime = $log['created_at'];
                $matchTime = $log['created_at'];
                continue;
            }

                if ($type == DepositLog::TYPE_UNMATCH) {
                    if ($matchTime) {
                        $unMatchTime += Carbon::parse($log['created_at'])->diffInSeconds($matchTime);
                        $matchTime   = '';
                    } else {
                        $preMatch = collect($allLogs)
                            ->where('type','=',DepositLog::TYPE_MATCH)
                            ->where('id','<',$log['id'])
                            ->sortByDesc('id')
                            ->first();
                        if (!empty($preMatch) && $preMatch['admin_name'] != $adminName) {
                            $unMatchTime +=  Carbon::parse($log['created_at'])->diffInSeconds($preMatch['created_at']);
                        }
                    }
                    continue;
                }

            if ($type != DepositLog::TYPE_ACCESS) {
                $lastTime = $log['created_at'];
            }
        }

        if ($lastTime) {
            $wholeTime      = Carbon::parse($lastTime)->diffInSeconds($depositCreatedTime);
            $processingTime = (int)$wholeTime - (int)$holdingTime;
        }

        $data                    = [];
        $data['holding_time']    = $holdingTime;
        $data['processing_time'] = $processingTime;
        $data['un_match_time']   = $unMatchTime;
        return $data;

    }
}