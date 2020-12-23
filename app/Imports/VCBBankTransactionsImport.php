<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class VCBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];
        $openBalance = 0;
        $closeBalance = 0;
        foreach ($rows as $key => $row)
        {
            if (0 == $key) {
                $openBalance = $this->transformBalance($row[1], true);
                continue;
            }

            if (1 == $key) {
                $closeBalance = $this->transformBalance($row[1], true);
                continue;
            }

            if (2 == $key || 3 == $key) {
                continue;
            }
            if ('Total' == $row[0]) {
                if ($openBalance == $closeBalance) {
                    break;
                } else {
                    error_response(422, 'calculation mistake.');
                }
            }

            $row[3] = $this->transformBalance($row[3]);

            if ('-' == $row[2]) {
                $openBalance -= $row[3];
            } else {
                $openBalance += $row[3];
            }

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $this->transformDate($row[0]),
                'bank_reference'    => $row[1],
                'debit'             => $row[2] == '-' ? $this->removeThreeZeros($row[3]) : 0,
                'credit'            => $row[2] == '-' ? 0 : $this->removeThreeZeros($row[3]),
                'balance'           => $this->removeThreeZeros($openBalance),
                'description'       => $row[4],
            ];
        }

        $data = array_reverse($data);

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);

        if (count($rows) % 4 != 0) {
            error_response(422, 'Incomplete content');
        }

        $rows = array_chunk($rows, 4);
        $data = [];
        foreach ($rows as $key => $row)
        {
            $row[3] = $this->transformBalance($row[3], false, true);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $this->replaceDate($row[0]),
                'description'       => $row[1],
                'bank_reference'    => $row[2],
                'debit'             => $row[3] <0 ? $this->removeThreeZeros($row[3]) : 0,
                'credit'            => $row[3] >0 ? $this->removeThreeZeros($row[3]) : 0,
            ];
        }

        $availableBalance = $this->transformBalance($this->lastBalance);

        return $this->calculateByLastBalance($data, $this->transformBalance($availableBalance));
    }

    public function startRow(): int
    {
        return 10;
    }
}
