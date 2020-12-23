<?php

namespace App\Exports;

use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

/**
 * @OA\Schema(
 *   schema="PaymentReportExport",
 *   type="object",
 *   @OA\Property(property="name", type="string", description="会员名称"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="deposit", type="string", description="充值"),
 *   @OA\Property(property="withdrawal", type="string", description="提现"),
 *   @OA\Property(property="payment_fee", type="string", description="支付费率"),
 *   @OA\Property(property="bonus", type="string", description="优惠"),
 *   @OA\Property(property="rebate", type="string", description="返点"),
 * )
 */
class PaymentReportExport implements WithMapping, ShouldAutoSize, FromCollection, WithHeadings
{
    use \Maatwebsite\Excel\Concerns\Exportable, SerializesModels;

    private $data;

    public function __construct($data)
    {
        $this->data = $data;
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            'name'          => $row->name,
            'currency'      => $row->currency,
            'deposit'       => $row->total_deposit,
            'withdrawal'    => $row->total_withdrawal,
            'payment_fee'   => $row->total_payment_fee,
            'bonus'         => $row->total_bonus,
            'rebate'        => $row->total_rebate,
        ];
    }

    public function headings(): array
    {
        return ['Member Code', 'Currency', 'Deposit', 'Withdrawal', 'Payment Fee', 'Bonus', 'Rebate'];
    }

}
