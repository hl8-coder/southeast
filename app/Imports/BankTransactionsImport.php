<?php

namespace App\Imports;

use App\Models\BankTransaction;
use App\Models\CompanyBankAccount;
use App\Models\Currency;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Collection;

class BankTransactionsImport
{
    protected $currency;
    protected $account;
    protected $cacheKey;
    protected $lastBalance;
    protected $isForce;

    public function __construct(CompanyBankAccount $account,  $currency, $cacheKey, $lastBalance=0, $isForce=false)
    {
        $this->currency     = $currency;
        $this->account      = $account;
        $this->cacheKey     = $cacheKey;
        $this->lastBalance  = $lastBalance;
        $this->isForce      = $isForce;
    }

    /**
     * @param Collection $rows
     * @throws
     */
    public function collection(Collection $rows)
    {
        $data = $this->getExcelData($rows);

        if (!$this->isForce) {
            # 检查与上次导入金额是否能衔接上，如果衔接不上直接报错
            $this->checkExcelLastTransaction($data);
        }

        $data = $this->addAdminNameAndTime($data);

        # 设置amount
        $data  = $this->setAmount($data);

        batch_insert('bank_transactions', $data, true);

        $this->cacheDuplicateBalance($data);
    }

    /**
     * 获取excel表数转化后数据
     *
     * @param Collection $rows
     * @return array
     */
    public function getExcelData(Collection $rows)
    {
        $data = [];

        return $data;
    }

    /**
     * @param $text
     * @throws \Exception
     */
    public function importText($text)
    {
        $data = $this->getTextData($text);

        $data = $this->addAdminNameAndTime($data);

        # 设置amount
        $data = $this->setAmount($data);

        batch_insert('bank_transactions', $data, true);

        $this->cacheDuplicateBalance($data);
    }

    /**
     * 添加上传管理员及其时间
     *
     * @param  $data
     * @return array
     */
    public function addAdminNameAndTime($data)
    {
        return array_map(function($value) {
            $value['admin_name'] = Auth::guard('admin')->user()->name;
            $value['created_at'] = now()->toDateTimeString();
            $value['updated_at'] = now()->toDateTimeString();
            return $value;
        }, $data);
    }

    /**
     * 检查与上次导入金额是否能衔接上，如果衔接不上直接报错
     *
     * @param $data
     * @throws \Exception
     */
    public function checkExcelLastTransaction($data)
    {
        if (empty($data)) {
            return;
        }

        # 获取最后一次交易记录
        if (!$lastTransaction = BankTransaction::findLastTransaction($this->account->code)) {
            return;
        }

        $firstData = $data[0];

        # 判断金额是否相同
        if ($firstData['balance'] !== round(($lastTransaction->balance - $firstData['debit'] + $firstData['credit']), 6)) {
            throw new \Exception('Incorrect data.');
        }
//        if (0 !== \bccomp(($lastTransaction->balance - $firstData['debit'] + $firstData['credit']), $firstData['balance'], 6)) {
//            throw new \Exception('Incorrect data.');
//        }
    }

    /**
     * 最后余额是否匹配
     *
     * @param $fundInAccount
     * @param $lastBalance
     * @return bool
     */
    public static function isMatchLastTransaction($fundInAccount, $lastBalance)
    {
        # 获取最后一次交易记录
        if ($lastTransaction = BankTransaction::findLastTransaction($fundInAccount)) {

            $lastBalance = (float)str_replace(',', '', $lastBalance);

            # 判断金额是否相同
            if ($lastTransaction->balance != $lastBalance) {
                return false;
            }
        }

        return true;
    }

    /**
     * 获取text转化后数据
     *
     * @param  string  $text
     * @return array
     */
    public function getTextData($text)
    {
        $data = [];

        return $data;
    }

    public function transformDate($value, $format = 'Y-m-d')
    {
        try {
            return Carbon::instance(\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($value));
        } catch (\Exception $e) {
            return Carbon::createFromFormat($format, $value);
        }
    }

    public function replaceDate($value)
    {
        $value = str_replace('/', '-', $value);
        return new Carbon($value);
    }


    /**
     * 转换余额
     *
     * @param   string      $balance            余额
     * @param   bool        $isTrimCurrency     是否去除币别
     * @param   bool        $isTrim             是否去除空格
     * @param   string      $replaceStr         需要去除的字符串
     * @param   string      $trimCurrency       币别字符
     * @return  float
     */
    public function transformBalance($balance, $isTrimCurrency=false, $isTrim=false, $replaceStr=',', $trimCurrency=' VND')
    {

        if ($isTrimCurrency) {
            $balance = rtrim($balance, $trimCurrency);
        }

        if ($isTrim) {
            $balance = str_replace(' ', '', $balance);
        }

        return (float)str_replace($replaceStr, '', $balance);
    }

    /**
     * 将相同余额保存到缓存中
     *
     * 原因：批量保存在数据库中无法返回问题
     *
     * @param $data
     */
    public function cacheDuplicateBalance($data)
    {
        if (count($data) > 1) {

            # 获取相同balance次数
            $balances = collect($data)->pluck('balance')->map(function($item) {
                return (string)$item;
            })->toArray();
            $balanceCounts = array_count_values($balances);

            $sameTransactions = [];

            foreach ($balanceCounts as $balance => $count) {
                if ($count > 1) {
                    $temp = collect($data)->where('balance', $balance)->toArray();
                    $sameTransactions = array_merge($sameTransactions, $temp);
                }
            }
            foreach ($sameTransactions as &$transaction) {
                $transaction['transaction_date'] = $transaction['transaction_date'] instanceof Carbon ? $transaction['transaction_date']->toDateString() : $transaction['transaction_date'];
            }

            if (!empty($sameTransactions)) {
                Cache::put($this->cacheKey, $sameTransactions, now()->addHour());
            }

        }
    }

    /**
     * 正叙计算余额
     *
     * @param   array       $data               导出的数据
     * @param   float       $availableBalance   起始余额
     * @param   bool        $isReverse          是否倒序
     * @return  array
     */
    public function calculateByLastBalance($data, $availableBalance, $isReverse=true)
    {
        if ($isReverse) {
            $data = array_reverse($data);
        }

        # 判断最后余额是否是为null，无效拉取系统最后余额
        if (is_null($availableBalance)) {
            # 获取最后一次交易记录
            if ($lastTransaction = BankTransaction::findLastTransaction($this->account->code)) {
                $availableBalance = $lastTransaction->balance;
            } else {
                $availableBalance = 0;
            }
        } else {
            $availableBalance = $this->transformBalance($availableBalance);
        }

        foreach ($data as $key => &$row) {

            if (!empty($row['debit'])) {
                $availableBalance -= $row['debit'];
            } else {
                $availableBalance += $row['credit'];
            }

            $row['balance'] = $availableBalance;
        }

        return $data;
    }

    /**
     * 倒序计算余额
     *
     * @param   array       $data               导出的数据
     * @param   float       $availableBalance   起始余额
     * @param   bool        $isReverse          是否倒序
     * @return  array
     */
    public function calculateByReverseLastBalance($data, $availableBalance, $isReverse=true)
    {
        if ($isReverse) {
            $data = array_reverse($data);
        }

        foreach ($data as $key => &$row) {

//            if ($key == 0) {
//                $row['balance'] = $availableBalance;
//            }

            $row['balance'] = $availableBalance;

            if (!empty($row['debit'])) {
                $availableBalance += $row['debit'];
            } else {
                $availableBalance -= $row['credit'];
            }
//            if ($key != 0) {
//                $row['balance'] = $availableBalance;
//            }
        }

        # 倒序转正序
        $data = array_reverse($data);

        return $data;
    }



    /**
     * 是否去除末尾三个0
     *
     * @param $data
     * @return float|int
     */
    public function removeThreeZeros($data)
    {
        $currencySet = Currency::findByCodeFromCache($this->currency);

        if ($currencySet->isCanRemoveThreeZeros()) {
            $data = (float) $data / 1000;
        }

        return $data;
    }

    public function setAmount($data)
    {
        foreach ($data as &$item) {
            $item['amount'] = $item['credit'];
        }

        return $data;
    }

}
