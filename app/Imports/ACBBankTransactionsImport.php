<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class ACBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{

    public function getExcelData(Collection $rows)
    {
        $data = [];

        foreach ($rows as $row)
        {
            $row[2] = $this->transformBalance($row[2]);
            $row[3] = $this->transformBalance($row[3]);
            $row[4] = $this->transformBalance($row[4]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $this->transformDate($row[0]),
                'description'       => $row[1],
                'debit'             => $this->removeThreeZeros($row[2]),
                'credit'            => $this->removeThreeZeros($row[3]),
                'balance'           => $this->removeThreeZeros($row[4]),
            ];
        }

        return $data;
    }

    public function getTextData($text)
    {
        $text = str_replace("\t\n", "\t", $text);

        $rows = explode("\n", $text);

        foreach ($rows as $key => $row) {
            if ($key%2 == 1) {
                $rows[$key - 1] = $rows[$key - 1] . "\t" . $rows[$key];
                unset($rows[$key]);
            }
        }

        foreach ($rows as $key => $row) {
            $rows[$key] = explode("\t",$row);
        }

        $data = [];
        foreach ($rows as $row)
        {
            # 月-日-年
            $row[0] = explode('-', $row[0]);
            array_unshift($row[0], $row[0][2]);
            unset($row[0][3]);
            $row[0] = implode('-', $row[0]);

            $row[3] = $this->transformBalance($row[3]);
            $row[4] = $this->transformBalance($row[4]);
            $row[5] = $this->transformBalance($row[5]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => new Carbon($row[0]),
                'transfer_details'  => $row[1],
                'description'       => $row[2],
                'debit'             => $this->removeThreeZeros($row[3]),
                'credit'            => $this->removeThreeZeros($row[4]),
                'balance'           => $this->removeThreeZeros($row[5]),
            ];
        }

        return $data;
    }

    public function startRow(): int
    {
        return 10;
    }
}
