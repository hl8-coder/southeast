<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TCBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];
        foreach ($rows as $key => $row)
        {
            $row[0] = $this->replaceDate($row[0]);
            $row[2] = $this->transformBalance($row[2]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'description'       => $row[1],
                'debit'             => $row[2] < 0 ? $this->removeThreeZeros($row[2]) : 0,
                'credit'            => $row[2] >= 0 ? $this->removeThreeZeros($row[2]) : 0,
                'balance'           => $this->removeThreeZeros($this->transformBalance($row[3])),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);

        foreach ($rows as $key => $row) {
            $rows[$key] = explode("\t",$row);
        }
        $data = [];
        foreach ($rows as $key => $row)
        {
            $row[0] = $this->replaceDate($row[0]);
            $row[2] = $this->transformBalance($row[2]);
            $row[3] = $this->transformBalance($row[3]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'description'       => $row[1],
                'debit'             => $row[2] < 0 ? $this->removeThreeZeros($row[2]) : 0,
                'credit'            => $row[2] >= 0 ? $this->removeThreeZeros($row[2]) : 0,
                'balance'           => $this->removeThreeZeros($row[3]),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function startRow(): int
    {
        return 7;
    }
}
