<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class VTBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];
        foreach ($rows as $key => $row)
        {
            if (empty($row[2])) {
                break;
            }

            $row[1] = $this->replaceDate($row[1]);
            $row[5] = $this->transformBalance($row[5], true);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[1],
                'transaction_at'    => $row[1],
                'transfer_details'  => $row[2],
                'description'       => $row[4],
                'debit'             => $row[5] < 0 ? $this->removeThreeZeros($row[5]) : 0,
                'credit'            => $row[5] >= 0 ? $this->removeThreeZeros($row[5]) : 0,
                'balance'           => $this->removeThreeZeros($this->transformBalance($row[6], true)),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);

        foreach ($rows as $key => $row) {
            if ($key%3 == 2) {
                $rows[$key] = $rows[$key - 2] . ' ' . $rows[$key - 1] . "\t" . $rows[$key];
                unset($rows[$key - 2], $rows[$key - 1]);
            }
        }

        $newRows = [];
        foreach ($rows as $row) {
            $newRows[] = explode("\t",$row);
        }
        $data = [];
        foreach ($newRows as $row)
        {
            $row[0] = $this->replaceDate($row[0]);
            $row[3] = $this->transformBalance($row[3], true);
            $row[4] = $this->transformBalance($row[4], true);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'transaction_at'    => $row[0],
                'description'       => $row[2],
                'debit'             => 'Debit'  == $row[1] ? $this->removeThreeZeros($row[3]) : 0,
                'credit'            => 'Credit' == $row[1] ? $this->removeThreeZeros($row[3]) : 0,
                'balance'           => $this->removeThreeZeros($row[4]),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function startRow(): int
    {
        return 5;
    }
}
