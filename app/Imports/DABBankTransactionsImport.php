<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class DABBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        $data = [];
        $openBalance = 0;
        foreach ($rows as $key => $row)
        {
            if (empty($row[1])) {
                continue;
            }

            if (0 == $key) {
                $temp = explode(':', $row[1]);
                $openBalance = $this->transformBalance($temp[1], true, true);
                continue;
            }

            if (false !== strstr($row[1], 'Balance')) {
                $temp = explode(':', $row[1]);
                $closeBalance = $this->transformBalance($temp[1], true, true);
                if ($openBalance == $closeBalance) {
                    break;
                } else {
                    error_response(422, 'calculation mistake.');
                }
            }

            if (!empty($row[6]) ) {
                $row[6] = $this->transformBalance($row[6]);
                $openBalance += $row[6];
            }

            if (!empty($row[7]) ) {
                $row[7] = $this->transformBalance($row[7]);
                $openBalance += $row[7];
            }

            $row[2] = $this->replaceDate($row[2]);

            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[2],
                'channel'           => $row[3],
                'description'       => $row[4],
                'transfer_details'  => $row[5],
                'credit'            => $this->removeThreeZeros($row[6]),
                'debit'             => $this->removeThreeZeros($row[7]),
                'balance'           => $this->removeThreeZeros($openBalance),
            ];
        }

        return $data;
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);

        $startNum = $rows[0] - 1;
        $tempRows = [];

        foreach ($rows as $key => $row) {

            if (is_numeric($row) && $row == $startNum + 1) {
                $startNum++;
            }

            if ((string)$row != (string)$startNum) {
                $tempRows[$startNum][] = $row;
            }
        }

        $newRows =[];
        foreach ($tempRows as $row) {
            if (4 == count($row)) {
                $row[4] = $row[3];
                $row[3] = '';
            } elseif (3 == count($row)) {
                $row[4] = $row[2];
                $row[2] = $row[1];
                $row[1] = '';
                $row[3] = '';
            }
            $newRows[] = $row;
        }

        foreach ($newRows as $key => $row)
        {
            $row[0] = $this->replaceDate($row[0]);
            $row[4] = $this->transformBalance($row[4]);
            $data[] = [
                'currency'          => $this->currency,
                'bank_code'         => $this->account->bank->code,
                'fund_in_account'   => $this->account->code,
                'transaction_date'  => $row[0],
                'transaction_at'    => $row[0],
                'channel'           => $row[1],
                'description'       => $row[2],
                'transfer_details'  => $row[3],
                'debit'             => $row[4] < 0 ? $this->removeThreeZeros(abs($row[4])) : 0,
                'credit'            => $row[4] >= 0 ? $this->removeThreeZeros($row[4]) : 0,
            ];
        }

        $availableBalance = $this->transformBalance($this->lastBalance);

        return $this->calculateByLastBalance($data, $availableBalance);
    }

    public function startRow(): int
    {
        return 5;
    }
}
