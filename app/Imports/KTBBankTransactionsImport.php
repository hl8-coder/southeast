<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KTBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];

        foreach ($rows as $key => $row)
        {
            $amount = $this->transformBalance($row[4]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => Carbon::parse($row[0]),
                'transaction_at'    => Carbon::parse($row[0]),
                'description'       => $row[2],
                'bank_reference'    => $row[1],
                'debit'             => $amount <= 0 ? abs($amount) : 0,
                'credit'            => $amount > 0  ? abs($amount) : 0,
                'balance'           => $this->transformBalance($row[6]),
            ];
        }
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
            $amount = $this->transformBalance($row[4]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => Carbon::parse($row[0]),
                'transaction_at'    => Carbon::parse($row[0]),
                'description'       => $row[2],
                'bank_reference'    => $row[1],
                'debit'             => $amount <= 0 ? abs($amount) : 0,
                'credit'            => $amount > 0  ? abs($amount) : 0,
                'balance'           => $this->transformBalance($row[6]),
                'location'          => $row[7],
            ];
        }
        return $data;
    }

    public function startRow(): int
    {
        return 8;
    }
}
