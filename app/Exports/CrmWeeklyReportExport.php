<?php

namespace App\Exports;

use App\Models\CrmWeeklyReport;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Schema(
 *   schema="CrmWeeklyReportExport",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="week", type="integer", description="第几周"),
 *   @OA\Property(property="week_start_at", type="string", description="周起始日期"),
 *   @OA\Property(property="week_end_at", type="string", description="周结束日期"),
 *   @OA\Property(property="type", type="integer", description="统计的订单类型"),
 *   @OA\Property(property="total_orders", type="integer", description="周订单总量"),
 *   @OA\Property(property="total_calls", type="integer", description="周呼叫总量"),
 *   @OA\Property(property="total_type_orders", type="integer", description="周类型订单总量"),
 *   @OA\Property(property="total_type_calls", type="integer", description="周类型订单总量"),
 *   @OA\Property(property="personal_total_orders", type="integer", description="个人周订单总量"),
 *   @OA\Property(property="personal_total_calls", type="integer", description="个人周呼叫总量"),
 *   @OA\Property(property="personal_total_type_orders", type="integer", description="个人周订单类型总量"),
 *   @OA\Property(property="personal_total_type_calls", type="integer", description="个人周订单类型呼叫总量"),
 *   @OA\Property(property="successful", type="integer", description="成功"),
 *   @OA\Property(property="fail", type="integer", description="失败"),
 *   @OA\Property(property="voice_mail", type="integer", description="语音信箱"),
 *   @OA\Property(property="hand_up", type="integer", description="挂断"),
 *   @OA\Property(property="no_pick_up", type="integer", description="未接听"),
 *   @OA\Property(property="success", type="integer", description="营销成功"),
 *   @OA\Property(property="invalid_number", type="integer", description="无效号码"),
 *   @OA\Property(property="not_own_number", type="integer", description="非号机主"),
 *   @OA\Property(property="call_back", type="integer", description="回拨"),
 *   @OA\Property(property="not_answer", type="integer", description="无应答"),
 *   @OA\Property(property="not_interested_in", type="integer", description="没兴趣"),
 *   @OA\Property(property="other", type="integer", description="其他"),
 *   @OA\Property(property="register", type="integer", description="注册人数"),
 *   @OA\Property(property="ftd_member", type="integer", description="电销后首充人数"),
 *   @OA\Property(property="ftd_amount", type="string", description="电销后首充总金额"),
 *   @OA\Property(property="adjustment_amount", type="string", description="电销后调额总额"),
 *   @OA\Property(property="deposit_percent", type="string", description="充值人数百分比"),
 *   @OA\Property(property="admin_id", type="string", description="电销人员对应管理员ID"),
 *   @OA\Property(property="admin_name", type="string", description="电销人员对应管理员"),
 * )
 */
class CrmWeeklyReportExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    private $date;

    public function __construct($request)
    {
        $this->date = $request->all();
    }

    /**
     * @return Collection
     */
    public function collection()
    {
        return QueryBuilder::for(CrmWeeklyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->orderBy('week_start_at', 'desc')
            ->limit(10000)->get();
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->insertNewRowBefore(1);
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(1, 1, 'ID');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(2, 1, 'Week');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(3, 1, 'Date');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(4, 1, 'Type');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(5, 1, 'Total Assigned');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(6, 1, 'Total Called');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(7, 1, 'Total Type Orders');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(8, 1, 'Total Type Calls');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(9, 1, 'Personal Total Orders');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(10, 1, 'Personal Total Calls');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(11, 1, 'Personal Total Type Orders');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(12, 1, 'Personal Total Type Calls');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(7, 1, 'Successful');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(8, 1, 'Fail');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(9, 1, 'Voice Mail');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(10, 1, 'Success');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(11, 1, 'Hung Up');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(12, 1, 'No Pick Up');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(13, 1, 'Invalid Number');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(14, 1, 'Not Own Number');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(15, 1, 'Call Back');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(16, 1, 'Not Answer');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(17, 1, 'Not Interested In');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(18, 1, 'Successful call');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(24, 1, 'Other');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(19, 1, 'Register');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(20, 1, 'FTD Member');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(27, 1, 'FTD Amount');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(21, 1, 'Adjustment Amount');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(22, 1, 'Deposit Percent');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(30, 1, 'Admin ID');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(23, 1, 'Admin Name');
            },
        ];
    }

    /**
     * @param mixed $crmWeeklyReport
     *
     * @return array
     */
    public function map($crmWeeklyReport): array
    {
        # todo report system need design and rebuild
        return [
            'id'                       => $crmWeeklyReport->id,
            'week'                     => $crmWeeklyReport->week,
            'date'                     => $crmWeeklyReport->week_start_at . ' to ' . $crmWeeklyReport->week_end_at,
            'type'                     => CrmWeeklyReport::$type[$crmWeeklyReport->type],
            // 'total_orders'             => $crmWeeklyReport->total_orders,
            // 'total_calls'              => $crmWeeklyReport->successful + $crmWeeklyReport->fail, // for template $crmWeeklyReport->total_calls,
            // 'total_type_orders'        => $crmWeeklyReport->total_type_orders,
            // 'total_type_calls'         => $crmWeeklyReport->total_type_calls,
            // 'person_total_orders'      => $crmWeeklyReport->person_total_orders,
            // 'person_total_calls'       => $crmWeeklyReport->person_total_calls,
            'person_total_type_orders' => $crmWeeklyReport->person_total_type_orders,
            'person_total_type_calls'  => $crmWeeklyReport->person_total_type_calls,
            // 'successful'               => $crmWeeklyReport->successful,
            // 'fail'                     => $crmWeeklyReport->fail,
            'type_success'             => $crmWeeklyReport->success + $crmWeeklyReport->not_own_number
                                            + $crmWeeklyReport->call_back + $crmWeeklyReport->not_interested_in,
            'type_fail'                => $crmWeeklyReport->voice_mail + $crmWeeklyReport->hand_up + $crmWeeklyReport->no_pick_up
                                        + $crmWeeklyReport->invalid_number + $crmWeeklyReport->not_answer  + $crmWeeklyReport->other,
            'voice_mail'               => $crmWeeklyReport->voice_mail,
            'success'                  => $crmWeeklyReport->success,
            'hand_up'                  => $crmWeeklyReport->hand_up,
            'no_pick_up'               => $crmWeeklyReport->no_pick_up,
            'invalid_number'           => $crmWeeklyReport->invalid_number,
            'not_own_number'           => $crmWeeklyReport->not_own_number,
            'call_back'                => $crmWeeklyReport->call_back,
            'not_answer'               => $crmWeeklyReport->not_answer,
            'not_interested_in'        => $crmWeeklyReport->not_interested_in,
            // 'other'                    => $crmWeeklyReport->other,
            'register'                 => $crmWeeklyReport->register,
            'ftd_member'               => $crmWeeklyReport->ftd_member,
            // 'ftd_amount'               => $crmWeeklyReport->ftd_amount,
            'adjustment_amount'        => $crmWeeklyReport->adjustment_amount,
            'deposit_percent'          => $crmWeeklyReport->success > 0 ? format_number($crmWeeklyReport->ftd_member / $crmWeeklyReport->success, 2) . ' %' : 0,
            // 'admin_id'                 => $crmWeeklyReport->admin_id,
            'admin_name'               => $crmWeeklyReport->admin_name,
        ];
    }

}
