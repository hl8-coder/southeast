<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BBLBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];

        foreach ($rows as $key => $row)
        {
            if ('Total' == $row[2]) {
                break;
            }

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => Carbon::parse($row[1]),
                'transaction_at'    => Carbon::parse($row[1]),
                'description'       => $row[2],
                'debit'             => $this->transformBalance($row[3]),
                'credit'            => $this->transformBalance($row[4]),
                'balance'           => $this->transformBalance($row[5]),
                'channel'           => $row[6],
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function getTextData($text)
    {
        $text = str_replace("\t\n", "\t", $text);
        $rows = explode("\n", $text);

        foreach ($rows as $key => $row) {
            $newRow = explode("\t",$row);
            if (1 == count($newRow)) {
                unset($rows[$key]);
                continue;
            }
            $rows[$key] = $newRow;
        }
        $data = [];
        foreach ($rows as $key => $row)
        {
            if ('Total' == $row[2]) {
                break;
            }
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => Carbon::parse($row[0]),
                'transaction_at'    => Carbon::parse($row[0]),
                'description'       => $row[1],
                'debit'             => $this->transformBalance($row[2]),
                'credit'            => $this->transformBalance($row[3]),
                'balance'           => $this->transformBalance($row[4]),
                'channel'           => $row[5],
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function startRow(): int
    {
        return 8;
    }
}
