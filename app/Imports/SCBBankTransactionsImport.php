<?php

namespace App\Imports;

use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithStartRow;

class SCBBankTransactionsImport extends BankTransactionsImport implements ToCollection, WithStartRow
{
    public function getExcelData(Collection $rows)
    {
        throw new \Exception('No SCB excel import function.');
    }

    public function getTextData($text)
    {
        $rows = explode("\n", $text);

        foreach ($rows as $key => $row) {
            $rows[$key] = explode("\t",$row);
        }

        $data = [];
        $isNeedCalculateBalance = false;
        foreach ($rows as $key => $row)
        {
            $time = $this->replaceDate($row[0] . ' ' . $row[1] . ':00');

            if (count($row) == 9) {
                $debit = str_replace('-', '',$row[6]);
                $credit = str_replace('+', '',$row[7]);
                $balance = str_replace('+', '',$row[8]);
                $data[] = [
                    'currency'          => $this->currency,
                    'bank_code'         => $this->account->bank->code,
                    'fund_in_account'   => $this->account->code,
                    'transaction_date'  => $time,
                    'transaction_at'    => $time,
                    'transfer_details'  => $row[2],
                    'channel'           => $row[3],
                    'description'       => $row[4],
                    'debit'             => $this->transformBalance($debit),
                    'credit'            => $this->transformBalance($credit),
                    'balance'           => $this->transformBalance($balance),
                ];
            } elseif (count($row) == 7) {
                $debit = str_replace('-', '',$row[4]);
                $credit = str_replace('+', '',$row[5]);
                $data[] = [
                    'currency'          => $this->currency,
                    'bank_code'         => $this->account->bank->code,
                    'fund_in_account'   => $this->account->code,
                    'transaction_date'  => $time,
                    'transaction_at'    => $time,
                    'transfer_details'  => $row[2],
                    'channel'           => $row[3],
                    'debit'             => $this->transformBalance($debit),
                    'credit'            => $this->transformBalance($credit),
                    'description'       => $row[6],
                ];
                $isNeedCalculateBalance = true;
            }
        }
        if ($isNeedCalculateBalance) {
            $data = $this->calculateByLastBalance($data, $this->lastBalance, false);
        }

        return $data;
    }

    public function startRow(): int
    {
        return 8;
    }
}
