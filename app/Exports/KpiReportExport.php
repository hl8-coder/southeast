<?php


namespace App\Exports;


use App\Models\KpiReport;
use Carbon\Carbon;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Events\AfterSheet;
use Spatie\QueryBuilder\QueryBuilder;

class KpiReportExport implements FromCollection, WithMapping, ShouldAutoSize, WithEvents
{
    use Exportable;

    private $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function collection()
    {

        $query = QueryBuilder::for(KpiReport::class);

        if ($this->request->input('filter.start_at') != null) {
            $startDate = Carbon::parse($this->request->input('filter.start_at'))->toDateString();
            $query->where('date', '>=', $startDate);
        }

        if ($this->request->input('filter.end_at') != null) {
            $endDate = Carbon::parse($this->request->input('filter.end_at'))->toDateString();
            $query->where('date', '<=', $endDate);
        }

        if ($this->request->input('filter.currency') != null) {
            $query->where('currency', $this->request->input('filter.currency'));
        }

        $report = $query->orderBy('date')
            ->get();
        return $report;
    }


    public function registerEvents(): array
    {
        $headerList = ['Date', 'Currency', 'Withdrawal', 'Net Profit', 'New Members', 'Deposit', 'Active User', 'Deposit User',
            'Withdrawal User', 'Total Turnover', 'Win/Los', 'Member Rebate', 'Member Adjustment',
            'Promotion Cost(Adjust)', 'Promotion Cost(Code)', 'Bank Fees'];

        $header = [
            AfterSheet::class => function (AfterSheet $event) use ($headerList) {
                $event->sheet->getDelegate()->insertNewRowBefore(1);
                // $event->sheet->getDelegate()->setCellValueByColumnAndRow(1, 1, 'ID');
                $i = 1;
                foreach ($headerList as $headerName) {
                    $event->sheet->getDelegate()->setCellValueByColumnAndRow($i, 1, $headerName);
                    $i++;
                }
            },
        ];
        return $header;
    }

    public function map($row): array
    {
        return [
            'date'                         => $row->date,
            'currency'                     => $row->currency,
            'total_withdrawal'             => $row->total_withdrawal,
            'net_profit'                   => $row->net_profit,
            'total_new_members'            => $row->total_new_members,
            'total_deposit'                => $row->total_deposit,
            'total_active_members'         => $row->total_active_members,
            'total_deposit_members'        => $row->total_deposit_members,
            'total_withdrawal_members'     => $row->total_withdrawal_members,
            'total_turnover'               => $row->total_turnover,
            'total_payout'                 => $row->total_payout,
            'total_rebate'                 => $row->total_rebate,
            'total_adjustment'             => $row->total_adjustment,
            'total_promotion_cost'         => $row->total_promotion_cost,
            'total_promotion_cost_by_code' => $row->total_promotion_cost_by_code,
            'total_bank_fee'               => $row->total_bank_fee
        ];
    }
}
