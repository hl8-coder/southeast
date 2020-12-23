<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class AGRIBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];
        foreach ($rows as $key => $row)
        {
            if (empty($row[2])) {
                break;
            }

            $row[1] = $this->transformAGRIDate($row[1]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[1],
                'transaction_at'    => $row[1],
                'account_no'        => $row[3],
                'debit'             => '-' == $row[6] ? $this->removeThreeZeros($row[7]) : 0,
                'credit'            => '+' == $row[6] ? $this->removeThreeZeros($row[7]) : 0,
                'balance'           => $this->removeThreeZeros($row[8]),
                'description'       => $row[10],
                'location'          => $row[11],
                'transfer_details'  => $row[12],
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
            $row[1] = $this->replaceDate($row[1]);
            $row[3] = $this->transformBalance($row[3], false, false, '.');
            $row[4] = $this->transformBalance($row[4], false, false, '.');

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[1],
                'debit'             => '-' == $row[2] ? $this->removeThreeZeros($row[3]) : 0,
                'credit'            => '+' == $row[2] ? $this->removeThreeZeros($row[3]) : 0,
                'balance'           => $this->removeThreeZeros($row[4]),
                'description'       => $row[6],
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function startRow(): int
    {
        return 5;
    }

    public function transformAGRIDate($value)
    {
        $value = str_replace('/', '-', $value);
        $value = str_replace('h ', ':', $value);
        $value = str_replace( "' ", ':', $value);
        return new Carbon($value);
    }
}
