<?php

namespace App\Exports;

use App\Models\CrmCallLog;
use App\Models\CrmDailyReport;
use App\Models\CrmOrder;
use App\Models\CrmWeeklyReport;
use App\Models\User;
use Carbon\Carbon;
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
 *   schema="CrmDailyReportExport",
 *   type="object",
 *   @OA\Property(property="id", type="integer", description="ID"),
 *   @OA\Property(property="week", type="integer", description="第几周"),
 *   @OA\Property(property="date", type="string", description="日期"),
 *   @OA\Property(property="type", type="integer", description="统计的订单类型"),
 *   @OA\Property(property="total_orders", type="integer", description="日订单总量"),
 *   @OA\Property(property="total_calls", type="integer", description="日呼叫总量"),
 *   @OA\Property(property="total_type_orders", type="integer", description="日类型订单总量"),
 *   @OA\Property(property="total_type_calls", type="integer", description="日类型订单总量"),
 *   @OA\Property(property="personal_total_orders", type="integer", description="个人日订单总量"),
 *   @OA\Property(property="personal_total_calls", type="integer", description="个人日呼叫总量"),
 *   @OA\Property(property="personal_total_type_orders", type="integer", description="个人日订单类型总量"),
 *   @OA\Property(property="personal_total_type_calls", type="integer", description="个人日订单类型呼叫总量"),
 *   @OA\Property(property="successful", type="integer", description="成功"),
 *   @OA\Property(property="fail", type="integer", description="失败"),
 *   @OA\Property(property="success", type="integer", description="营销成功"),
 *   @OA\Property(property="voice_mail", type="integer", description="语音信箱"),
 *   @OA\Property(property="hand_up", type="integer", description="挂断"),
 *   @OA\Property(property="no_pick_up", type="integer", description="未接听"),
 *   @OA\Property(property="invalid_number", type="integer", description="无效号码"),
 *   @OA\Property(property="not_own_number", type="integer", description="非号机主"),
 *   @OA\Property(property="call_back", type="integer", description="回拨"),
 *   @OA\Property(property="not_answer", type="integer", description="无应答"),
 *   @OA\Property(property="not_interested_in", type="integer", description="没兴趣"),
 *   @OA\Property(property="other", type="integer", description="其他"),
 *   @OA\Property(property="admin_id", type="string", description="电销人员对应管理员ID"),
 *   @OA\Property(property="admin_name", type="string", description="电销人员对应管理员"),
 * )
 */
class CrmDailyReportExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
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
        return QueryBuilder::for(CrmDailyReport::class)
            ->allowedFilters(
                Filter::exact('week'),
                Filter::exact('date'),
                Filter::exact('admin_name'),
                Filter::exact('type')
            )
            ->orderBy('date', 'desc')
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
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(10, 1, 'Hand Up');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(11, 1, 'No Pick Up');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(12, 1, 'Invalid Number');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(13, 1, 'Not Own Number');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(14, 1, 'Call Back');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(15, 1, 'Not Answer');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(16, 1, 'Not Interested In');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(17, 1, 'Successful call');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(24, 1, 'Other');
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(25, 1, 'Admin ID');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(18, 1, 'Bo User Name');
            },
        ];
    }

    /**
     * @param mixed $report
     *
     * @return array
     */
    public function map($report): array
    {
        # todo report system need to rebuild
        return [
            'id'                       => $report->id,
            'week'                     => $report->week,
            'date'                     => $report->date,
            'type'                     => transfer_show_value($report->type, CrmDailyReport::$type),
            // 'total_orders'             => $report->total_orders,
            // 'total_calls'              => $report->successful + $report->fail, // for template $report->total_calls,
            // 'total_type_orders'        => $report->total_type_orders,
            // 'total_type_calls'         => $report->total_type_calls,
            // 'person_total_orders'      => $report->person_total_orders,
            // 'person_total_calls'       => $report->person_total_calls,
            'person_total_type_orders' => $report->person_total_type_orders,
            'person_total_type_calls'  => $report->person_total_type_calls,
            // 'successful'               => $report->successful,
            // 'fail'                     => $report->fail,
            'type_success'             => $report->success + $report->not_own_number + $report->call_back + $report->not_interested_in,
            'type_fail'                => $report->voice_mail + $report->hand_up + $report->no_pick_up
                                        + $report->invalid_number + $report->not_answer + $report->other,
            'voice_mail'               => $report->voice_mail,
            'hand_up'                  => $report->hand_up,
            'no_pick_up'               => $report->no_pick_up,
            'invalid_number'           => $report->invalid_number,
            'not_own_number'           => $report->not_own_number,
            'call_back'                => $report->call_back,
            'not_answer'               => $report->not_answer,
            'not_interested_in'        => $report->not_interested_in,
            'success'                  => $report->success,
            // 'other'                    => $report->other,
            // 'admin_id'                 => $report->admin_id,
            'admin_name'               => $report->admin_name,
        ];
    }

}
