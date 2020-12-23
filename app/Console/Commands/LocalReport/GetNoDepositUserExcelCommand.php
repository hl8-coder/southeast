<?php

namespace App\Console\Commands\LocalReport;

use App\Exports\ExcelTemplateExport;
use App\Models\Deposit;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Facades\Excel;
use Spatie\QueryBuilder\QueryBuilder;
use Illuminate\Console\Command;

class GetNoDepositUserExcelCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast-local-report:get-no-deposit-user-excel-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '获取30天及以上未充值的用户名单 及未充值的天数';

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
        $headings = [
            'Username',
            'Gender',
            'Email',
            'Last Deposit Time',
            'NO Deposit Days',
            'Is Deposited'
        ];

        // 获取所有用户的存款记录
        $depositLogs = QueryBuilder::for(Deposit::query())
            ->where('status','=',3)
            ->orderBy('id','asc')
            ->pluck('created_at','user_id')
            ->toArray();

        $data = [];

        // 获取所有注册超过30天的用户
        QueryBuilder::for(User::query())
            ->where('users.created_at','<=',date('Y-m-d H:i:s',strtotime("-30 days")))
            ->leftJoin('user_info','user_info.user_id','=','users.id')
            ->select(['users.id as user_id','users.name as user_name','users.created_at as reg_at','user_info.gender','user_info.email'])
            ->chunk(1000,function ($list) use ($depositLogs,&$data) {
               foreach($list as $info) {
                   $user_id = $info['user_id'];
                   $reg_at  = $info['reg_at'];
                   $is_had_deposited = 0;
                   $lastDepositTime = "";
                   // 判断用户是否有存款
                   if (!empty($depositLogs[$user_id])) {
                       // 有存款记录  计算上次存款的时间 和当前差
                       $lastDepositTime = date("Y-m-d H:i:s", strtotime($depositLogs[$user_id]));
                       $noDepositDays = ceil((time() - strtotime($depositLogs[$user_id])) / (3600 * 24)); // 向上取整
                       $is_had_deposited = 1;
                   } else { // 计算注册时间 和当前的时间差
                       $noDepositDays = ceil((time() - strtotime($reg_at)) / (3600 * 24));
                   }

                   $data[] = array(
                       $info['user_name'],
                       $info['gender'],
                       $info['email'],
                       $lastDepositTime,
                       $noDepositDays,
                       $is_had_deposited ? 'Yes':"No"
                   );
               }
            });


        Excel::store(new ExcelTemplateExport($data, $headings), 'no_deposit_users.xlsx');
    }
}
