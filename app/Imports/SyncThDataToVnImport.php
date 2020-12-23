<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;

class SyncThDataToVnImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $collection
     */
    public function collection(Collection $rows)
    {
    }

    public function startRow(): int
    {
        return 1;
    }


}
