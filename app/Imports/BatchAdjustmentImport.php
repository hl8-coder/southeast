<?php


namespace App\Imports;


use App\Repositories\UserRepository;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

/**
 * Class BatchAdjustmentImport
 * @package App\Imports
 * Row: 'Member code','Related Txn ID','Amount','Turnover','Turnover amount','Category','Platform','Reason','Remark',
 */
class BatchAdjustmentImport implements ToCollection, WithStartRow
{
    private $rows = 0;

    private $startRow = 2;

    public function collection(Collection $rows)
    {
        foreach ($rows as $row)
        {
            print_r($row .PHP_EOL);
        }
    }

    public function checkName($name)
    {
        return UserRepository::findByName($name);
    }

    public function getRowCount(): int
    {
        return $this->rows;
    }

    public function startRow(): int
    {
        return $this->startRow;
    }
}