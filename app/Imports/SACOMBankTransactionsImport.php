<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SACOMBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];
        $availableBalance = 0;
        foreach ($rows as $key => $row)
        {
            if ('Available Balance:' == $row[4]) {
                $availableBalance = $this->transformBalance(ltrim($row[12], 'VND '), false, false, '.');
                $availableBalance = $this->removeThreeZeros($availableBalance);
                continue;
            }

            if ($key < 5) {
                continue;
            }

            $row[8] = new Carbon($row[8]);

            $row[15] = $this->transformBalance($row[15], false, false, '.');
            $row[16] = $this->transformBalance($row[16], false, false, '.');

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'bank_reference'    => $row[7],
                'transaction_date'  => $row[8],
                'transaction_at'    => $row[8],
                'description'       => $row[13],
                'debit'             => !empty($row[15]) ? $this->removeThreeZeros($row[15]) : 0,
                'credit'            => !empty($row[16]) ? $this->removeThreeZeros($row[16]) : 0,
            ];
        }

        return $this->calculateByReverseLastBalance($data, $availableBalance);
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
            $row[1] = $this->transformDate($row[1], 'd-m-Y H:i:s');
            $row[5] = $this->transformBalance($row[5], false, false, '.');
            $row[6] = $this->transformBalance($row[6], false, false, '.');
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'bank_reference'    => $row[0],
                'transaction_date'  => $row[1],
                'transaction_at'    => $row[1],
                'description'       => $row[3],
                'debit'             => $this->removeThreeZeros($row[5]),
                'credit'            => $this->removeThreeZeros($row[6]),
            ];
        }

        return $this->calculateByLastBalance($data, $this->lastBalance, false);
    }

    public function startRow(): int
    {
        return 24;
    }
}
