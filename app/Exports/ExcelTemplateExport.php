<?php

namespace App\Exports;

use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStrictNullComparison;

/**
 * @OA\Schema(
 *   schema="ExcelTemplateExport",
 *   type="object",
 *   @OA\Property(property="Member_code", type="string", description="会员名"),
 * )
 */
class ExcelTemplateExport implements FromCollection, WithHeadings, WithStrictNullComparison
{
    use Exportable;

    private $data;
    private $headings;

    public function __construct($data, $headings)
    {
        $this->data = $data;
        $this->headings = $headings;
    }

    public function collection()
    {
        return collect($this->data);
    }

    public function headings(): array
    {
        return $this->headings;
    }
}