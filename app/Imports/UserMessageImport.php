<?php

namespace App\Imports;

use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;

class UserMessageImport implements ToCollection, WithStartRow
{
    /**
     * @param Collection $rows
     */
    public function collection(Collection $rows)
    {
        return $rows;
        $data = $this->getExcelData($rows);
//        batch_insert('user_messages', $data, true);
    }

    public function getExcelData(Collection $rows)
    {
        $data = [];
        foreach ($rows as $key => $row) {
        }
        return $data;
    }

    public function startRow(): int
    {
        return 2;
    }
}
