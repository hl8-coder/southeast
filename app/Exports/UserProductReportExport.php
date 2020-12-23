<?php

namespace App\Exports;

use App\Models\GameBetDetail;
use Illuminate\Queue\SerializesModels;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Spatie\QueryBuilder\Filter;
use Spatie\QueryBuilder\QueryBuilder;

/**
 * @OA\Schema(
 *   schema="UserProductReportExport",
 *   type="object",
 *   @OA\Property(property="user_id", type="integer", description="用户ID", format="date-time"),
 *   @OA\Property(property="user_name", type="string", description="会员名称"),
 *   @OA\Property(property="currency", type="string", description="币别"),
 *   @OA\Property(property="total_stake", type="string", description="总投注"),
 *   @OA\Property(property="total_profit", type="string", description="总盈亏"),
 *   @OA\Property(property="percent", type="string", description="盈亏比"),
 *   @OA\Property(property="product_code", type="integer", description="产品code"),
 * )
 */
class UserProductReportExport implements WithMapping, ShouldAutoSize, FromCollection, WithHeadings
{
    use \Maatwebsite\Excel\Concerns\Exportable, SerializesModels;

    private $data;
    private $productCode;

    public function __construct($data, $param)
    {
        $this->data        = $data;
        $this->productCode = $param['product_code'];
    }

    public function collection()
    {
        return $this->data;
    }

    public function map($row): array
    {
        return [
            'user_id'      => $row->user_id,
            'user_name'    => $row->user->name,
            'currency'     => $row->user->currency,
            'total_stake'  => thousands_number($row->total_stake),
            'total_profit' => thousands_number($row->total_profit),
            'percent'      => format_number($row->percent * 100, 2),
            'product_code' => $this->productCode,
        ];
    }

    public function headings(): array
    {
        return ['ID', 'Member Code', 'Currency', 'Bet Amount', 'W/L Amount', 'Win/Lose', 'Product Code'];
    }

}
