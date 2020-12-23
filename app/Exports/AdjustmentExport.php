<?php

namespace App\Exports;

use App\Models\Adjustment;
use App\Models\GameBetDetail;
use App\Models\Model;
use App\Transformers\AdjustmentTransformer;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

class AdjustmentExport implements WithMapping, ShouldAutoSize, FromCollection, WithHeadings
{
    use \Maatwebsite\Excel\Concerns\Exportable, SerializesModels;

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {
        $request        = $this->request;
        $conditionArray = [
            'id'        => Filter::exact('id'),
            'user_name' => 'user_name',
            'type'      => Filter::exact('type'),
            'category'  => Filter::exact('category'),
            'start_at'  => Filter::scope('start_at'),
            'end_at'    => Filter::scope('end_at'),
            'is_agent'  => Filter::exact('is_agent')->ignore([true, false, null]),
            'status'    => Filter::exact('status'),
            'reason'    => 'reason',
            'currency'  => Filter::scope('currency'),
        ];

        $userName = $request->input('filter.user_name');
        $isAgent  = $request->input('filter.is_agent', false);

        $data = QueryBuilder::for(Adjustment::class)
            ->allowedFilters(array_values($conditionArray))
            ->whereHas('user', function ($query) use ($userName, $isAgent) {
                if ($userName) {
                    return $query->where('name', $userName)
                        ->where('is_agent', $isAgent);
                }
            })
            ->latest()
            ->limit(10000)
            ->get();

        return $data;
    }

    public function map($row): array
    {
        return [
            'member_code'          => $row->user_name,
            'date'                 => $row->created_at,
            'type'                 => isset(Adjustment::$types[$row->type]) ? Adjustment::$types[$row->type] : $row->type,
            'amount'               => $row->amount,
            'turnover_amt'         => $row->turnover_closed_value,
            'current_turnover_amt' => $row->turnover_current_value,
            'meet_rollover'        => transfer_show_value($row->is_turnover_closed, Model::$booleanDropList),
            'category'             => isset(Adjustment::$categories[$row->category]) ? Adjustment::$categories[$row->category] : $row->category,
            'platform'             => $row->platform_code,
            'reason'               => $row->reason,
            'remarks'              => $row->remark,
            'adjustment_id'        => $row->order_no,
            'status'               => Adjustment::$statuses[$row->status],
            'admin'                => $row->created_admin_name,
            'verified_admin'       => $row->verified_admin_name,
        ];
    }

    public function headings(): array
    {
        return ['Member Code', 'Date', 'Type', 'amount', 'Turnover Amt', 'Current Turnover Amt', 'Meet Rollover',
                'Category', 'Platform', 'Reason', 'Remarks', 'Adjustment ID', 'Status', 'Admin', 'Verified Admin'];
    }

}
