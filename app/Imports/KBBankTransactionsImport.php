<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class KBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
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
                'transaction_at'    => $time,
                'description'       => $row[1],
                'debit'             => $this->transformBalance($row[2]),
                'credit'            => $this->transformBalance($row[3]),
                'balance'           => $this->transformBalance($row[4]),
                'channel'           => $row[5],
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
        if (1 == count($rows[0])) {
            foreach ($rows as $key => $row)
            {
                if (strpos($row[0], 'Total') !== false) {
                    break;
                }

                if (1 == count($row)) {
                    continue;
                }
                $year = explode('/', $rows[$key-1][0]);
                $year[2] = '20' . $year[2];
                $row[0] = implode('/', $year) . ' ' . $row[0];
                $time = $this->replaceDate($row[0]);

                $data[] = [
                    'currency'          => $this->currency,
                    'bank_code'         => $this->account->bank->code,
                    'fund_in_account'   => $this->account->code,
                    'transaction_date'  => $time->toDateString(),
                    'transaction_at'    => $time,
                    'transfer_details'  => $row[2],
                    'account_no'        => $row[5],
                    'description'       => $row[6],
                    'debit'             => $this->transformBalance($row[3]),
                    'credit'            => $this->transformBalance($row[4]),
                    'channel'           => $row[1],
                ];
            }

            $data = $this->calculateByLastBalance($data, $this->lastBalance, true);
        } else {
            foreach ($rows as $key => $row)
            {
                $time = $this->replaceDate($row[0]);
                $data[] = [
                    'currency'          => $this->currency,
                    'bank_code'         => $this->account->bank->code,
                    'fund_in_account'   => $this->account->code,
                    'transaction_date'  => $time,
                    'transaction_at'    => $time,
                    'description'       => $row[1],
                    'debit'             => $this->transformBalance($row[2]),
                    'credit'            => $this->transformBalance($row[3]),
                    'balance'           => $this->transformBalance($row[4]),
                    'channel'           => $row[5],
                ];
            }
        }
        return $data;
    }

    public function startRow(): int
    {
        return 8;
    }
}
