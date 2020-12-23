<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class EXBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{

    public function getExcelData(Collection $rows)
    {
        $data = [];

        foreach ($rows as $key => $row)
        {

            if ('Total' == $row[2]) {
                break;
            }
            $row[2] = $this->replaceDate($row[2]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[2],
                'transaction_at'    => $row[2],
                'description'       => $row[6],
                'debit'             => $this->removeThreeZeros($this->transformBalance($row[3])),
                'credit'            => $this->removeThreeZeros($this->transformBalance($row[4])),
                'balance'           => $this->removeThreeZeros($this->transformBalance($row[5])),
            ];
        }

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);

        foreach ($rows as &$row) {
            $row = explode("\t",$row);
        }
        $data = [];
        foreach ($rows as $row)
        {
            $row[0] = $this->replaceDate($row[0]);
            $row[1] = $this->transformBalance($row[1]);
            $row[2] = $this->transformBalance($row[2]);
            $row[3] = $this->transformBalance($row[3]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'transaction_at'    => $row[0],
                'description'       => $row[4],
                'debit'             => $this->removeThreeZeros($row[1]),
                'credit'            => $this->removeThreeZeros($row[2]),
                'balance'           => $this->removeThreeZeros($row[3]),
            ];
        }

        return $data;
    }

    public function startRow(): int
    {
        return 11;
    }
}
