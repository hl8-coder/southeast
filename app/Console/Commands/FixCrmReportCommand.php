<?php

namespace App\Console\Commands;

use App\Models\Admin;
use App\Models\CrmCallLog;
use App\Models\CrmDailyReport;
use App\Models\CrmOrder;
use App\Models\CrmResourceCallLog;
use App\Models\CrmWeeklyReport;
use App\Services\CrmReportService;
use App\Services\CrmService;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class FixCrmReportCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'southeast:fix-crm-report-command';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crm Report 部分数据不准确，该指令用于修正新功能之前的错误数据，
                                对当前数据没有影响，本指令会占用大量IO，不得在服务器繁忙的时候使用';

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
        # 本指令原则上只在 viet-214 上线后执行一次
        $this->recountAllCallLogs();
        $this->rebuildAllAssignOrders();
    }

    protected function rebuildAllAssignOrders()
    {
        $crmReportService = new CrmReportService();
        $oldest           = CrmOrder::query()->whereNotNull('created_at')->orderBy('created_at')->first(['created_at']);
        $latest           = CrmOrder::query()->whereNotNull('created_at')->orderBy('created_at', 'desc')->first(['created_at']);
        $startDate        = Carbon::parse($oldest->created_at);
        $endDate          = Carbon::parse($latest->created_at);
        $do = true;
        while ($do){
            $crmReportService->modifyOrders($startDate);
            if ($startDate->addWeek() > $endDate){
                $do = false;
            }
        }
    }


    protected function recountAllCallLogs()
    {
        CrmCallLog::query()->with('crmOrder')->chunk(10, function ($orderCallLogs) use (&$needToRecountBuildAll) {
            foreach ($orderCallLogs as $orderCallLog) {
                $order = $orderCallLog->crmOrder;

                # 修补其中一些有通话记录，但是订单却没有派发的异常数据，正常情况不会存在这种数据
                # 但在测试服中发现来该类数据，特此增加逻辑修正
                if (!empty($order) && $order->admin_name == null) {
                    if ($order->tag_at == null) {
                        $order->tag_at = $order->updated_at;
                    }
                    $order->admin_id   = $orderCallLog->admin_id;
                    $admin             = Admin::query()->find($orderCallLog->admin_id);
                    $order->admin_name = $admin->name;
                    $result            = $order->save();
                }

                if (empty($order)){
                    $orderCallLog->delete();
                }else{
                    /** @var CrmReportService $crmReportService */
                    $crmReportService = app(CrmReportService::class);
                    $crmReportService->addCallLog($orderCallLog);
                }
            }
        });

        CrmResourceCallLog::query()->chunk(10, function ($resourceLogs) use ($needToRecountBuildAll) {
            foreach ($resourceLogs as $resourceLog) {
                $resource = $resourceLog->crmResource;

                # 理由同上
                if (!empty($resource) && $resource->admin_name == null) {
                    if ($resource->tag_at == null) {
                        $resource->tag_at = $resource->updated_at;
                    }
                    $resource->admin_id   = $resourceLog->admin_id;
                    $admin                = Admin::query()->find($resourceLog->admin_id);
                    $resource->admin_name = $admin->name;
                    $result               = $resource->save();
                }

                if (empty($resource)){
                    $resourceLog->delete();
                }else{
                    /** @var CrmReportService $crmReportService */
                    $crmReportService = app(CrmReportService::class);
                    $crmReportService->addCallLog($resourceLog);
                }
            }
        });
    }
}
