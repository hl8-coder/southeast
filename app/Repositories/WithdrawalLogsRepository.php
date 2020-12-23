<?php


namespace App\Repositories;

use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Spatie\QueryBuilder\QueryBuilder;
use OwenIt\Auditing\Models\Audit;

class WithdrawalLogsRepository
{
    # 获取有效的日志记录。（有效日志：除去后台创建的Created类型和访问记录Access类型）
    public static function getAllWithdrawalLogs()
    {
        $select = 'admins.name, withdrawals.order_no, withdrawals.currency, withdrawals.user_name, withdrawals.created_at as t_date, withdrawals.records, withdrawals.account_no, withdrawals.amount, withdrawals.status, admins.id as a_id, audits.id as id, audits.old_values, audits.new_values, audits.created_at, withdrawals.id as w_id';
        return QueryBuilder::for(Audit::query())
            ->where('auditable_type', 'App\Models\Withdrawal')
            ->where('user_type', 'App\Models\Admin')
            ->leftJoin('admins', 'audits.user_id', '=', 'admins.id')
            ->leftJoin('withdrawals', 'audits.auditable_id', '=', 'withdrawals.id')
            ->select(DB::raw($select))
            ->orderByDesc('audits.auditable_id')
            ->orderBy('audits.created_at')
            ->get();
    }

    public static function calculateLogTime($logs, $withdrawalID, $admin, $withdrawalCreatedTime)
    {
        $allLogs       = $logs;
        $logs          = $allLogs
            ->where('auditable_id', $withdrawalID)
            ->where('user_id', $admin)
            ->sortBy('created_at')
            ->toArray();
        $holdingTime   = 0;
        $escalatedTime = '';
        $lastTime = '';
        foreach ($logs as $log) {
            if (array_key_exists('status', $log['new_values'])) {
                $status    = $log['new_values']['status'];
                if ($status === Withdrawal::STATUS_HOLD) {
                    # 获取下一次状态时间
                    $nextLog     = $allLogs
                        ->sortBy('id')
                        ->where('auditable_id', $withdrawalID)
                        ->where('id', '>', $log['id'])
                        ->first();
                    if ($nextLog) {
                        $holdingTime += Carbon::parse($log['created_at'])->diffInSeconds($nextLog->created_at);
                    }
                    continue;
                }
                if ($status == Withdrawal::STATUS_ESCALATED) {
                    $escalatedTime = $log['created_at'];
                    break;
                }
                $lastTime = $log['created_at'];
            }
        }
        $processingTime = 0;
        if ($lastTime) {
            $processingTime = Carbon::parse($lastTime)->diffInSeconds($withdrawalCreatedTime);
        }

        $processingTime -= $holdingTime;
        $escalatingTime = 0;
        if ($escalatedTime) {
            $escalatingTime = Carbon::parse(array_pop($logs)['created_at'])->diffInSeconds($escalatedTime);
        }
        $data                    = [];
        $data['holding_time']    = $holdingTime;
        $data['processing_time'] = $processingTime;
        $data['escalating_time'] = $escalatingTime;
        return $data;
    }

    /**
     *
     * 计算每一笔订单的操作时间.
     *
     * @param array $allLogs 该笔订单所有的操作日志(可能包含多个操作人员)
     *
     * @param integer $adminId 操作该笔订单该后台人员的id
     *
     * @param string $withdrawalCreatedTime 提款订单创建的时间.
     * @return array
     */
    public static function calculateTime($allLogs,$adminId,$withdrawalCreatedTime)
    {
        $logs = collect($allLogs)
            ->where('user_id',$adminId)
            ->sortBy('created_at')
            ->toArray();

        $holdingTime   = 0;
        $escalatedTime = '';
        $lastTime = '';
        foreach ($logs as $log) {
            if (array_key_exists('status', $log['new_values'])) {
                $status    = $log['new_values']['status'];
                if ($status === Withdrawal::STATUS_HOLD) {
                    # 获取下一次状态时间
                    $nextLog     = collect($allLogs)
                        ->where('id', '>', $log['id'])
                        ->sortBy('id')
                        ->first();
                    if ($nextLog) {
                        $holdingTime += Carbon::parse($log['created_at'])->diffInSeconds($nextLog['created_at']);
                    }
                    continue;
                }
                if ($status == Withdrawal::STATUS_ESCALATED) {
                    $escalatedTime = $log['created_at'];
                    break;
                }
                $lastTime = $log['created_at'];
            }
        }
        $processingTime = 0;
        if ($lastTime) {
            $processingTime = Carbon::parse($lastTime)->diffInSeconds($withdrawalCreatedTime);
        }

        $processingTime -= $holdingTime;
        $escalatingTime = 0;
        if ($escalatedTime) {
            $escalatingTime = Carbon::parse(array_pop($logs)['created_at'])->diffInSeconds($escalatedTime);
        }
        $data                    = [];
        $data['holding_time']    = $holdingTime;
        $data['processing_time'] = $processingTime;
        $data['escalating_time'] = $escalatingTime;
        return $data;
    }
}