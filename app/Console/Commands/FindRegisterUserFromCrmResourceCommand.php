<?php

namespace App\Console\Commands;

use App\Models\CrmResource;
use App\Models\CrmWeeklyReport;
use App\Models\User;
use App\Models\UserInfo;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FindRegisterUserFromCrmResourceCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:find-register-user-from-crm-resource';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '从已经拨打电话的 resource 统计注册认识，统计规则为号码的后九位相同即视为同一个人';


    /**
     * 新注册用户与 CRM resource 通话有效关联最长天数
     *
     * @var int
     */
    private $days = 7;

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
        # 预防用户在脚本执行后注册，统计上周的信息
        $weekStart = now()->subWeek(5)->startOfWeek();
        $weekEnd   = now()->subWeek()->endOfWeek();
        $week      = now()->weekOfYear;
        $type = CrmWeeklyReport::TYPE_RESOURCE;
        $updateData = [];

            # 更新注册信息到资源列表，无论是否为有效统计，都要尽量更新用户注册信息到资源表
        CrmResource::query()->tagStart($weekStart)->tagEnd($weekEnd)->whereNotNull('admin_id')->chunk(100, function ($resources) {
            $resourceInfo = $resources->pluck('phone', 'id')->map(function ($value) {
                return $key = substr($value, -9);
            })->toArray();
            $phones       = array_values($resourceInfo);
            $userInfo     = UserInfo::query()->with('user')->whereIn('phone', $phones)->get();
            $update       = [];
            foreach ($userInfo as $info) {
                $user     = $info->user;
                $update[] = [
                    'id'       => $resources->where('phone', $info->phone)->first()->id,
                    'register' => $user->name,
                ];
            }
            CrmResource::updateBatch($update);
        });

        # 统计注册人数，条件：已经分配，有注册
        $weeklyRegisterInfo = CrmResource::query()
            ->select(DB::raw("admin_id, count(*) as total_register"))
            ->tagStart($weekStart)
            ->tagEnd($weekEnd)
            ->whereNotNull('register')
            ->whereNotNull('admin_id')
            ->groupBy('admin_id')
            ->get();

        $adminIds = $weeklyRegisterInfo->pluck('admin_id')->toArray();
        $needToUpdate = CrmWeeklyReport::query()->where('week', $week)->where('type', $type)->whereIn('admin_id', $adminIds)->get();
        foreach ($needToUpdate as $update){
            $updateData[] = [
                'id' => $update->id,
                'register' => $weeklyRegisterInfo->where('admin_id', $update->admin_id)->first()->total_register
            ];
        }

        # 更新注册人数到 crm 周报表
        $result = CrmWeeklyReport::updateBatch($updateData);
        Log::channel('crm_log')->info(now() . " 更新CRM周报表注册人数，更新结果：$result");
    }
}
