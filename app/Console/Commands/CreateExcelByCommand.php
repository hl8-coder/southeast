<?php

namespace App\Console\Commands;

use App\Exports\BetHistoryExport;
use App\Exports\ExcelTemplateExport;
use App\Repositories\WithdrawalLogsRepository;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use OwenIt\Auditing\Models\Audit;
use Spatie\QueryBuilder\QueryBuilder;

class CreateExcelByCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:create_excel_by_command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $type = "withdraw";
        switch($type) {
            case "withdraw":
                $this->withdraw_excel();
        }
    }

    private function withdraw_excel()
    {
        $headings = [
            'Admin',
            'Transaction ID',
            'Currency',
            'Member Code',
            'Transaction Date',
            'Fund out Account',    # 公司银行账户
            'Member Account No. ', # 会员提现账户
            'Amount',
            'Status',              # 提现单最后的状态
            'Processing Time',     # Admin操作的总时间 - Holding Time - Escalating Time  （Admin操作的总时间是有效记录中，Admin第一次Access到最后一次不是Hold或者Release Hold或者Escalate的时间）
            'Holding Time',        # Hold到Release Hold的时间，如果不是同一个人，两个都计算
            'Escalating Time',     # 从Escalate到最后的时间
        ];

        $select = 'admins.name, withdrawals.order_no, withdrawals.currency, withdrawals.user_name, withdrawals.created_at as t_date, withdrawals.records, withdrawals.account_no, withdrawals.amount, withdrawals.status, admins.id as a_id, withdrawals.id as w_id';
        $logs   = QueryBuilder::for(Audit::query())
            ->where('auditable_type', 'App\Models\Withdrawal')
            ->where('user_type', 'App\Models\Admin')
            ->leftJoin('admins', 'audits.user_id', '=', 'admins.id')
            ->leftJoin('withdrawals', 'audits.auditable_id', '=', 'withdrawals.id')
            ->select(DB::raw($select))
            ->orderByDesc('w_id')
            ->groupBy('name', 'w_id')
            ->get();
        $allLogs = QueryBuilder::for(Audit::query())
            ->where('auditable_type', 'App\Models\Withdrawal')
            ->where('user_type', 'App\Models\Admin')
            ->orderByDesc('auditable_id')
            ->orderBy('created_at')
            ->get();
        $data = [];
        foreach ($logs as $index => $log) {
            $data[$index]['admin']           = $log->name;
            $data[$index]['order_no']        = $log->order_no;
            $data[$index]['currency']        = $log->currency;
            $data[$index]['name']            = $log->user_name;
            $data[$index]['t_date']          = $log->t_date;
            $data[$index]['fund']            = $log->records;
            $data[$index]['account_no']      = $log->account_no;
            $data[$index]['amount']          = thousands_number($log->amount);
            $data[$index]['status']          = $log->status;
            $time                            = WithdrawalLogsRepository::calculateLogTime($allLogs, $log->w_id, $log->a_id, $log->t_date);
            $data[$index]['processing_time'] = $time['processing_time'];
            $data[$index]['holding_time']    = $time['holding_time'];
            $data[$index]['escalating_time'] = $time['escalating_time'];
        }

        Excel::store(new ExcelTemplateExport($data, $headings), 'message_withdraw.xlsx');
    }

}
