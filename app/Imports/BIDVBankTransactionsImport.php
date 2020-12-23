<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class BIDVBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];

        foreach ($rows as $key => $row)
        {
            if (empty($row[0])) {
//                $data[count($data) - 1]['description'] .= $row[21];
                continue;
            }

            $row[0] = $this->replaceDate($row[0]);
            $row[3] = $this->transformBalance($row[3], false, true);
            $row[8] = $this->transformBalance($row[8], true);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'transaction_at'    => $row[0],
                'description'       => $row[21],
                'debit'             => $row[3] <= 0 ? abs($this->removeThreeZeros($row[3])) : 0,
                'credit'            => $row[3] > 0 ? $this->removeThreeZeros($row[3]) : 0,
                'balance'           => $this->removeThreeZeros($row[8]),
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function getTextData($text)
    {
        $originRows = explode("\n", $text);

        $rows = array_chunk($originRows, 4);
        $data = [];
        foreach ($rows as $row)
        {

            $temp = explode(' ', $row[1]);
            $row[0] = str_replace('/', '-', $row[0]);
            $tempArr = explode(' ', $row[0]);
            $ymd = explode('-', $tempArr[0]);
            $row[0] = $ymd[2] . '-' . $ymd[1] . '-' . $ymd[0] . ' ' . $tempArr[1];
            $row[0] = Carbon::parse($row[0]);

            $row[1] = $this->transformBalance($temp[1]);
            $row[2] = $this->transformBalance($row[2]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'transaction_at'    => $row[0],
                'description'       => $row[3],
                'debit'             => $temp[0] == '-' ? $this->removeThreeZeros($row[1]) : 0,
                'credit'            => $temp[0] == '+' ? $this->removeThreeZeros($row[1]) : 0,
                'balance'           => $this->removeThreeZeros($row[2]),
            ];
        }
        $data = array_reverse($data);

        return $data;
    }


    public function startRow(): int
    {
        return 16;
    }
}
