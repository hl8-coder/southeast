<?php

namespace App\Services;

use App\Models\Deposit;
use App\Models\Withdrawal;
use DB;
use Carbon\Carbon;

class HistoryService
{
    public function depositWithdrawal($userId, $filter)
    {
        $dateFrom = isset($filter["date_from"]) ? Carbon::parse($filter["date_from"])->startOfDay() : Carbon::today()->subDays(7);
        $dateTo   = isset($filter["date_to"]) ? Carbon::parse($filter["date_to"])->endOfDay() : Carbon::today()->addDay();
        $type     = isset($filter["type"]) ? $filter["type"] : null;
        $status   = isset($filter["status"]) ? $filter["status"] : null;

        $dbSelect           = "id, created_at, order_no, '%s' as type, amount, %s";
        $dbStatus           = "case when status = %s then 1 when status = %s then 2 else 3 end as status";
        $dbWithdrawalStatus = "case when status = " . Withdrawal::STATUS_SUCCESSFUL . " then 1 "
            . "when status in (" . Withdrawal::STATUS_REJECTED . "," . Withdrawal::STATUS_FAIL . ") then 2 "
            . "when status = " . Withdrawal::STATUS_PENDING . " and claim_admin_name is not null then 5 "
            . "when status = " . Withdrawal::STATUS_PENDING . " then 3 "
            . "when status = " . Withdrawal::STATUS_CANCELED . " then 4 "
            . "else 5 end as status";

        $depositStatus = [
            1 => [Deposit::STATUS_RECHARGE_SUCCESS], // successful
            2 => [Deposit::STATUS_RECHARGE_FAIL], // failed
            3 => [Deposit::STATUS_CREATED, Deposit::STATUS_HOLD], // pending
        ];

        $withdrawalStatus = [
            1 => [Withdrawal::STATUS_SUCCESSFUL], // successful
            2 => [Withdrawal::STATUS_REJECTED, Withdrawal::STATUS_FAIL], // failed
            3 => [Withdrawal::STATUS_PENDING], // pending
            4 => [Withdrawal::STATUS_CANCELED], // cancel
            5 => [
                Withdrawal::STATUS_HOLD,
                Withdrawal::STATUS_ESCALATED,
                Withdrawal::STATUS_REVIEWED,
                Withdrawal::STATUS_PROCESS,
                Withdrawal::STATUS_DEFERRED,
                Withdrawal::STATUS_APPROVED,
            ], // processing
        ];

        $deposit = Deposit::select(DB::raw(sprintf($dbSelect, 'deposit', sprintf($dbStatus, Deposit::STATUS_RECHARGE_SUCCESS, Deposit::STATUS_RECHARGE_FAIL))))
            ->whereRaw($type != "withdrawal" ? "true" : "false")
            ->where("user_id", $userId)
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<', $dateTo);


        if ($status) {
            isset($depositStatus[$status]) ? $deposit->whereIn("status", $depositStatus[$status]) : $deposit->where("status", null);
        }

        $withdrawal = Withdrawal::select(DB::raw(sprintf($dbSelect, 'withdrawal', $dbWithdrawalStatus)))
            ->whereRaw($type != "deposit" ? "true" : "false")
            ->where("user_id", $userId)
            ->where('created_at', '>=', $dateFrom)
            ->where('created_at', '<', $dateTo);

        if ($status && isset($withdrawalStatus[$status])) {
            $withdrawal->whereIn("status", $withdrawalStatus[$status]);
        }

        $result = $deposit->union($withdrawal);
        $result = $result->orderBy("created_at", "desc");


        return $result;
    }

    public function getDropList()
    {
        $data = [];

        $data['display_type'] = ['withdrawal' => __('history.withdrawal'), 'deposit' => __('history.deposit')];

        $data['display_status'] = [
            1 => __('history.successful'),
            2 => __('history.failed'),
            3 => __('history.pending'),
            4 => __('history.cancel'),
            5 => __('history.processing'),
        ];
        return $data;
    }
}
