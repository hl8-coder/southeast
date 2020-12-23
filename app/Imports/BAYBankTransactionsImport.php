<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BAYBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];

        foreach ($rows as $key => $row)
        {
            $time = $this->replaceDate($row[0]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $time,
                'bank_reference'    => $row[1]. ' ' . $row[2],
                'transfer_details'  => $row[3],
                'channel'           => $row[7],
                'location'          => $row[8],
                'debit'             => !empty($row[4]) ? $this->transformBalance($row[4]) : 0,
                'credit'            => !empty($row[5]) ? $this->transformBalance($row[5]) : 0,
                'balance'           => $this->transformBalance($row[6]),
            ];
        }

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);
        $data = [];
        foreach ($rows as $key => $row)
        {
            $row = explode("\t", $row);
            $time = $this->replaceDate($row[0]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $time,
                'transaction_at'    => $time,
                'bank_reference'    => $row[1],
                'channel'           => $row[5],
                'debit'             => !empty($row[2]) ? $this->transformBalance($row[2]) : 0,
                'credit'            => !empty($row[3]) ? $this->transformBalance($row[3]) : 0,
                'balance'           => $this->transformBalance($row[4]),
            ];
        }

        return $data;
    }

    public function startRow(): int
    {
        return 2;
    }
}
