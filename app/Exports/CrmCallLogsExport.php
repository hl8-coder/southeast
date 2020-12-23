<?php

namespace App\Exports;

use App\Models\CrmCallLog;
use App\Models\CrmOrder;
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
 *   schema="CrmOrderCallLogs",
 *   type="object",
 *   @OA\Property(property="call_at", type="string", description="call 时间", format="date-time"),
 *   @OA\Property(property="member_code", type="integer", description="会员 ID"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="status", type="integer", description="会员状态"),
 *   @OA\Property(property="source", type="integer", description="客户来源"),
 *   @OA\Property(property="call_status", type="integer", description="联络状态"),
 *   @OA\Property(property="type", type="integer", description="纪录类型"),
 *   @OA\Property(property="reason", type="string", description="会员原因"),
 *   @OA\Property(property="prefer_product", type="integer", description="产品活动优惠"),
 *   @OA\Property(property="prefer_bank", type="integer", description="偏好银行"),
 *   @OA\Property(property="remark", type="string", description="备注"),
 *   @OA\Property(property="agent_name", type="string", description="代理名称"),
 *   @OA\Property(property="created_at", type="string", description="创建时间", format="date-time"),
 * )
 */
class CrmCallLogsExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
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
        $collection = QueryBuilder::for(CrmOrder::class)
            ->allowedFilters(
                Filter::exact('type'),
                Filter::scope('name'),
                Filter::scope('full_name'),
                Filter::scope('email'),
                Filter::scope('phone'),
                Filter::scope('currency'),
                Filter::scope('user_status'),
                Filter::scope('risk_group_id'),
                Filter::scope('payment_group_id'),
                Filter::scope('affiliated_code'),
                Filter::scope('register_start'),
                Filter::scope('register_end'),
                Filter::scope('last_login_start'),
                Filter::scope('last_login_end'),
                Filter::scope('last_deposit_start'),
                Filter::scope('last_deposit_end'),
                Filter::scope('tag_start'),
                Filter::scope('tag_end'),
                Filter::scope('last_save_start'),
                Filter::scope('last_save_end'),
                Filter::scope('register_ip'),
                Filter::scope('deposit'),
                Filter::exact('status'),
                Filter::exact('call_status'),
                Filter::exact('admin_name'),
                Filter::exact('tag_admin_name')
            )
            ->defaultSort('created_at')
            ->limit(10000)->get();
        return $collection;
    }

    /**
     * @return array
     */
    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $event->sheet->getDelegate()->insertNewRowBefore(1);
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(1, 1, 'Fist Call Date');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(2, 1, 'Last Call Date');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(3, 1, 'Total Call');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(4, 1, 'Member Code');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(5, 1, 'Currency');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(6, 1, 'Status');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(7, 1, 'Source');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(8, 1, 'Call Status');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(9, 1, 'Call Type');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(10, 1, 'Member Reason');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(11, 1, 'Prefer Product');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(12, 1, 'Bank');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(13, 1, 'Agent');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(14, 1, 'Save Case Date');
                $event->sheet->getDelegate()->setCellValueByColumnAndRow(15, 1, 'Auto');
            },
        ];
    }

    /**
     * @param mixed $row
     *
     * @return array
     */
    public function map($crmOrder): array
    {
        $crmCallLogLatest = $crmOrder->crmCallLogs()->orderByDesc('created_at')->first();
        $crmCallLogFirst  = $crmOrder->crmCallLogs()->orderBy('created_at', 'asc')->first();
        $crmCallLogCount  = $crmOrder->crmCallLogs()->count();
        $source           = '';
        $crmOrder->crmCallLogs()->get()->every(function ($item) use (&$source) {
            $source .= isset(CrmCallLog::$source[$item->source]) ? CrmCallLog::$source[$item->source] : '';
        });
        $reason = '';
        $crmOrder->crmCallLogs()->get()->every(function ($item) use (&$reason) {
            $reason .= isset(CrmCallLog::$reason[$item->reason]) ? CrmCallLog::$reason[$item->reason] : '';
        });

        $product = '';
        $crmOrder->crmCallLogs()->get()->every(function ($item) use (&$product) {
            $product .= isset(CrmCallLog::$prefer_product[$item->prefer_product]) ? CrmCallLog::$prefer_product[$item->prefer_product] : '';
        });

        $bank = '';
        $crmOrder->crmCallLogs()->get()->every(function ($item) use (&$bank) {
            $bank .= $item->bank;
        });

        return [
            'fist_call_at'   => empty($crmCallLogFirst) ? '' : $crmCallLogFirst->created_at,
            'last_call_at'   => empty($crmCallLogLatest) ? '' : $crmCallLogLatest->created_at,
            'call_count'     => $crmCallLogCount,
            'member_code'    => $crmOrder->user->name,
            'currency'       => $crmOrder->user->currency,
            'status'         => User::$statuses[$crmOrder->user->status],
            'source'         => $source,
            'call_status'    => isset(CrmOrder::$call_statuses[$crmOrder->call_status]) ? CrmOrder::$call_statuses[$crmOrder->call_status] : "-",
            'type'           => CrmOrder::$type[$crmOrder->type],
            'reason'         => $reason,
            'prefer_product' => $product,
            'prefer_bank'    => $bank,
            'admin_name'     => $crmOrder->admin_name,
            'created_at'     => convert_time($crmOrder->created_at),
            'auto'           => CrmOrder::$booleanDropList[$crmOrder->is_auto],
        ];
    }

}
