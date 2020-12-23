<?php


namespace App\Imports;

use App\Models\CrmResource;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\ToModel;
use Maatwebsite\Excel\Concerns\WithStartRow;

class CrmOrderImport implements ToCollection, WithStartRow
{
    public function collection(Collection $rows)
    {
        return $rows;
    }

    public function startRow(): int
    {
        return 2;
    }
}
