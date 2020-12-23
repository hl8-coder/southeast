<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class TMBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
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
                'transaction_date'  => Carbon::parse($time),
                'description'       => $row[1],
                'channel'           => $row[2],
                'debit'             => $row[3] <= 0 ? $this->transformBalance(abs($row[3])) : 0,
                'credit'            => $row[3] > 0  ? $this->transformBalance($row[3]) : 0,
                'balance'           => $this->transformBalance($row[4]),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);
        $rows = array_chunk($rows, 5);

        $data = [];

        foreach ($rows as $key => $row)
        {
            $time = $this->replaceDate($row[0]);

            $amountArr = explode(' ', $row[3]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => Carbon::parse($time),
                'description'       => $row[1],
                'channel'           => $row[2],
                'debit'             => $amountArr[0] == '-' ? $this->transformBalance($amountArr[1]) : 0,
                'credit'            => $amountArr[0] == '+' ? $this->transformBalance($amountArr[1]) : 0,
                'balance'           => $this->transformBalance($row[4], true, false, ',', ' ฿'),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function startRow(): int
    {
        return 2;
    }
}
