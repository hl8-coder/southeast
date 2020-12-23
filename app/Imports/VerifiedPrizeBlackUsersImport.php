<?php


namespace App\Imports;


use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class VerifiedPrizeBlackUsersImport implements ToCollection, WithStartRow
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
