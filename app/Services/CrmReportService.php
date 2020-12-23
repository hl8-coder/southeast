<?php


namespace App\Services;


use App\Models\CrmCallLog;
use App\Models\CrmDailyReport;
use App\Models\CrmOrder;
use App\Models\CrmResource;
use App\Models\CrmResourceCallLog;
use App\Models\CrmWeeklyReport;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use phpDocumentor\Reflection\DocBlock\Description;

class CrmReportService
{
    /**
     * 新建记录进来之后，统计到日报表和周报表
     *
     * @param CrmCallLog|CrmResourceCallLog $callLog
     * @return bool
     *
     * @author  Martin
     * @date    2020/8/3 9:50 上午
     */
    public function addCallLog($callLog)
    {
        if (empty($callLog)) {
            return false;
        }

        switch ($callLog) {
            case $callLog instanceof CrmResourceCallLog:
                $type      = CrmDailyReport::TYPE_RESOURCE;
                $adminName = $callLog->crmResource->admin_name;
                break;
            case $callLog instanceof CrmCallLog:
                $order     = $callLog->crmOrder;
                $type      = $order->type;
                $adminName = $order->admin_name;
                break;
            default:
                return false;
                break;
        }

        $whereDailyCondition  = [
            'date'       => $callLog->created_at->toDateString(),
            'week'       => $callLog->created_at->weekOfYear,
            'admin_id'   => $callLog->admin_id,
            'admin_name' => $adminName,
            'type'       => $type
        ];
        $whereWeeklyCondition = [
            'week_start_at' => $callLog->created_at->startOfWeek()->toDateString(),
            'week_end_at'   => $callLog->created_at->endOfWeek()->toDateString(),
            'admin_id'      => $callLog->admin_id,
            'week'          => $callLog->created_at->weekOfYear,
            'admin_name'    => $adminName,
            'type'          => $type
        ];
        $updateColumns        = $this->switchCrmReportColumns($callLog->call_status);

        $daily = CrmDailyReport::query()->where($whereDailyCondition)->firstOrCreate($whereDailyCondition);
        $day   = $daily->update($updateColumns);

        $weekly = CrmWeeklyReport::query()->where($whereWeeklyCondition)->firstOrCreate($whereWeeklyCondition);
        $week   = $weekly->update($updateColumns);

        return $day && $week;
    }


    /**
     * CRM 派单系统发生数据变更时，触发当周派单统计相关数据重新统计，这里假设周数据在 5k 左右
     * note：之所以没有用 当日/单个订单 统计，是因为有可能将昨日的订单在今天重新派发，并且允许批量派发
     *
     * Logic:
     * 1、根据传入的日期确定查询事件范围，获取 crm_orders 与 crm_resource 的全部订单
     * 2、将 上述两个数据集合转化成数组，并组合在一起形成一个所有数据的集合
     * 3、筛选集合中包含条件【admin_id, type, date】的全部类型
     * 4、根据类型遍历循环跟新 日 与 周 报表
     *
     * @param string|null $date
     *
     * @author  Martin
     * @date    2020/8/3 3:32 下午
     * @version viet-214
     */
    public function modifyOrders(string $date = null)
    {
        if ($date == null) {
            $date = now();
        }
        $date          = Carbon::parse($date)->toDateString();
        $dateWeekStart = Carbon::parse($date)->startOfWeek()->toDateString();
        $dateWeekEnd   = Carbon::parse($date)->endOfWeek()->toDateString();
        $week          = Carbon::parse($date)->weekOfYear;


        $orders = CrmOrder::query()->where('tag_at', '>=', $dateWeekStart)
            ->where('tag_at', '<=', $dateWeekEnd)
            ->whereNotNull('admin_id')
            ->get(['tag_at', 'type', 'admin_id', 'admin_name'])->toArray();

        $resources = CrmResource::query()->where('tag_at', '>=', $dateWeekStart)
            ->where('tag_at', '<=', $dateWeekEnd)
            ->whereNotNull('admin_id')
            ->get(['tag_at', 'admin_id', 'admin_name'])->toArray();


        foreach ($orders as &$order) {
            $order['date']          = Carbon::parse($order['tag_at'])->toDateString();
            $order['week']          = $week;
            $order['week_start_at'] = $dateWeekStart;
            $order['week_end_at']   = $dateWeekEnd;
            unset($order['tag_at']);
        }

        foreach ($resources as $resource) {
            $orders[] = [
                'date'          => Carbon::parse($resource['tag_at'])->toDateString(),
                'week'          => $week,
                'week_start_at' => $dateWeekStart,
                'week_end_at'   => $dateWeekEnd,
                'admin_id'      => $resource['admin_id'],
                'admin_name'    => $resource['admin_name'],
                'type'          => CrmDailyReport::TYPE_RESOURCE
            ];
        }

        $adminIds = collect($orders)->pluck(['admin_id'])->unique()->toArray();
        $types    = collect($orders)->pluck(['type'])->unique()->toArray();
        $dates    = collect($orders)->pluck(['date'])->unique()->toArray();

        # cycle: max = 5 * 7 * admins
        foreach ($adminIds as $adminId) { // max: no limit
            $boUser = collect($orders)->where('admin_id', $adminId)->first();
            foreach ($types as $type) {     // max:5
                foreach ($dates as $date) { // max:7
                    $countDate = collect($orders)->where('admin_id', $adminId)
                        ->where('type', $type)
                        ->where('date', $date)
                        ->count();

                    $whereDaily = [
                        'date'       => $date,
                        'week'       => $week,
                        'admin_id'   => $adminId,
                        'admin_name' => $boUser['admin_name'],
                        'type'       => $type
                    ];

                    $dailyReport = CrmDailyReport::query()->where($whereDaily)->firstOrCreate($whereDaily);
                    $dailyReport->update(['person_total_type_orders' => $countDate]);
                }
                $countWeek = collect($orders)->where('admin_id', $adminId)
                    ->where('type', $type)
                    ->count();

                $whereWeekly = [
                    'week_start_at' => $dateWeekStart,
                    'week_end_at'   => $dateWeekEnd,
                    'admin_id'      => $adminId,
                    'week'          => $week,
                    'admin_name'    => $boUser['admin_name'],
                    'type'          => $type
                ];

                $weeklyReport = CrmWeeklyReport::query()->where($whereWeekly)->firstOrCreate($whereWeekly);
                $weeklyReport->update(['person_total_type_orders' => $countWeek]);
            }
        }
    }


    /**
     * 根据通话状态，获取需要更新的字段名称，更新通话记录相关的字段
     *
     * @param int $callStatus 通话记录中具体的通话状态
     * @return array e.g [person_total_type_calls, voice_mail, successful]
     *
     * @author  Martin
     * @date    2020/8/3 3:45 下午
     * @version viet-214
     */
    private function switchCrmReportColumns(int $callStatus): array
    {
        $update = ['person_total_type_calls'];
        switch ($callStatus) {
            case CrmCallLog::CALL_STATUS_VOICE_MAIL:
            case CrmResourceCallLog::CALL_STATUS_VOICE_MAIL:
                $update[] = 'voice_mail';
                break;
            case CrmCallLog::CALL_STATUS_HAND_UP:
            case CrmResourceCallLog::CALL_STATUS_HAND_UP:
                $update[] = 'hand_up';
                break;
            case CrmCallLog::CALL_STATUS_NO_PICK_UP:
            case CrmResourceCallLog::CALL_STATUS_NO_PICK_UP:
                $update[] = 'no_pick_up';
                break;
            case CrmCallLog::CALL_STATUS_INVALID_NUMBER:
            case CrmResourceCallLog::CALL_STATUS_INVALID_NUMBER:
                $update[] = 'invalid_number';
                break;
            case CrmCallLog::CALL_STATUS_NOT_OWN_NUMBER:
            case CrmResourceCallLog::CALL_STATUS_NOT_OWN_NUMBER:
                $update[] = 'not_own_number';
                break;
            case CrmCallLog::CALL_STATUS_CALL_BACK:
            case CrmResourceCallLog::CALL_STATUS_CALL_BACK:
                $update[] = 'call_back';
                break;
            case CrmCallLog::CALL_STATUS_NO_ANSWER:
            case CrmResourceCallLog::CALL_STATUS_NO_ANSWER:
                $update[] = 'not_answer';
                break;
            case CrmCallLog::CALL_STATUS_NOT_INTERESTED_IN:
            case CrmResourceCallLog::CALL_STATUS_NOT_INTERESTED_IN:
                $update[] = 'not_interested_in';
                break;
            case CrmCallLog::CALL_STATUS_SUCCESS:
            case CrmResourceCallLog::CALL_STATUS_SUCCESS:
                $update[] = 'success';
                break;
            default:
                $update[] = 'other';
                break;
        }

        if (CrmCallLog::$callStatusToStatus[$callStatus] || CrmResourceCallLog::$callStatusToStatus[$callStatus]) {
            $update[] = 'successful';
        } else {
            $update[] = 'fail';
        }

        return $this->makeColumnsIncrementForDB($update);
    }

    /**
     * 将数组字段修改成 对象更新 结构
     *
     * @param array $columns [admin, type]
     * @param int $increment 递增幅度
     * @return array [`admin` => object(`admin` +1), type => object(type+1)]
     *
     * @author  Martin
     */
    private function makeColumnsIncrementForDB(array $columns, int $increment = 1): array
    {
        $updateData = [];
        foreach ($columns as $column) {
            $updateData[$column] = DB::raw("{$column} + $increment");
        }
        return $updateData;
    }
}
