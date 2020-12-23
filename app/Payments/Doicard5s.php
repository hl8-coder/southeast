<?php

namespace App\Payments;

use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Models\Transaction;
use App\Jobs\TransactionProcessJob;
use App\Models\TurnoverRequirement;
use App\Services\TransactionService;
use Cache;

class Doicard5s extends Payment
{

    /**
     * 支持语系代码对照
     */
    public $aLangCode = [
        "en-us" => "English",
        "zh-cn" => "Chinese Simplified",
        "th"    => "Thai",
        "ms-my" => "Malay (Malaysia)",
        "vi-VN" => "Vietnamese (Vietnam)",
        "id-id" => "Indonesian",
        "bur"   => "Burmese",
    ];

    /**
     * 充值卡類型码对照
     */
    public $aCardType = [
        ["key" => "2", "value" => "Viettel", 'fee'      => '35'],
        ["key" => "3", "value" => "Mobifone", 'fee'     => '38'],
        ["key" => "6", "value" => "Vinaphone", 'fee'    => '32'],
        ["key" => "5", "value" => "Zing", 'fee'         => '25'],
        ["key" => "7", "value" => "Gate", 'fee'         => '30'],
//        ["key" => "8", "value" => "Vcoin", 'fee'        => '28'],
        ["key" => "11", "value" => "Vietnamobile", 'fee' => '25'],
    ];

    public static $showCardTypes = [
        ["key" => "2", "value" => "Viettel", 'fee'      => '35'],
        ["key" => "3", "value" => "Mobifone", 'fee'     => '38'],
        ["key" => "6", "value" => "Vinaphone", 'fee'    => '32'],
        ["key" => "5", "value" => "Zing", 'fee'         => '25'],
        ["key" => "7", "value" => "Gate", 'fee'         => '30'],
        ["key" => "8", "value" => "Vcoin", 'fee'        => '28'],
        ["key" => "11", "value" => "Vietnamobile", 'fee' => '25'],
    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "card" => [
            "access_token"      => "",
            "typeCard"          => "",
            "money"             => "",
            "code"              => "",
            "seri"              => "",
            "transaction_id"    => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "card"     => [
            "customer_key"      => "access_token",
            "card_type"         => "typeCard",
            "amount"            => "money",
            "pin_number"        => "code",
            "serial_number"     => "seri",
            "order_no"          => "transaction_id",
        ],
    ];

    /**
     * 请求类型
     */
    public $aReuqstType = [
        "card"     => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_MESSAGE,
            "request_method" => PaymentPlatform::REQUEST_METHOD_POST,
        ],
    ];


    public function getCardTypeList($paymentPlatform, $currency)
    {
        switch ($paymentPlatform->code) {
            case 'Doicard5s-card':
                return $this->aCardType;
                break;

            default:
                return [];
                break;
        }
    }

    /**
     * 签名前调整
     */
    public function modifyParamBeforeSign(Deposit $deposit, &$requestData, $platformType)
    {
        $requestData['money'] = (int)$requestData['money'] * 1000;
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
    }

    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key)
    {
        return true;
    }

    /**
     * 解析回调内容
     */
    public function analyticalBody($platformType, $body, &$aResultData, &$sUrl, &$sError)
    {
        $result = json_decode($body);
        switch ($platformType) {
            case 'card':
                if (1000  == $result->status_code) {
                    $deposit = Deposit::findByOrderNo($aResultData["deposit"]->order_no);
                    $this->updateDeposit($platformType,  $deposit, $result, 'callback');
                }elseif (1009  == $result->status_code) {
                } else {
                    $this->fail($aResultData["deposit"], $result);
                    $sError = $result->message;
                }
                break;
        }
    }

    /**
     * 获取信息
     * order_no     交易订单号
     * is_success   是否上分成功
     * remarks      失败信息
     *
     * @param $data
     */
    public function getCallbackDepositResult($platformType, $data)
    {
        switch ($platformType) {
            case 'card':
                $deposit = Deposit::findByOrderNo($data['transaction_id']);
                $this->updateDepositByCallback($platformType,  $deposit, $data, 'callback');
                break;
        }
    }


    /**
     * 回调后更新充值内容
     */
    public function updateDepositByCallback($platformType, Deposit $deposit, $data, $type='')
    {

        switch ($platformType) {
            case 'card':
                # 充值成功能直接发起帐变不用透过回调
                if ($data['status'] == 1) {

                    // TODO 先更新 再执行success success后会自动执行调用其他操作


                    # 计算手续费
                    if ($cardType = collect($this->aCardType)->where('key', $deposit->card_type)->first()) {
                        $deposit->amount                = (float)$data['amount'] / 1000;
                        $deposit->bank_fee              = (float)($deposit->amount * $cardType['fee'] / 100);
                        $deposit->arrival_amount        = $deposit->amount - $deposit->bank_fee;
                        $deposit->turnover_closed_value = $deposit->arrival_amount;
                        $deposit->save();
                    }

                    $deposit->success($data);


                    $user = $deposit->user;

                    # 帐变记录
                    $transaction = (new TransactionService())->addTransaction(
                        $user,
                        $deposit->arrival_amount,
                        Transaction::TYPE_THIRD_PARTY_SAVE,
                        $deposit->id,
                        $deposit->order_no
                    );

                    if ($transaction) {
                        # 创建流水要求
                        TurnoverRequirement::add($deposit, $deposit->is_turnover_closed);
                        dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
                    }
                }else {
                    $deposit->fail($data);
                }
                break;
        }
        return false;
    }

    /**
     * 回调后更新充值内容
     */
    public function updateDeposit($platformType, Deposit $deposit, $data, $type='')
    {

        switch ($platformType) {
            case 'card':
                # 充值成功能直接发起帐变不用透过回调
                if ($data->status == 'success') {

                    // TODO 先更新 再执行success success后会自动执行调用其他操作
                    # 计算手续费
                    if ($cardType = collect($this->aCardType)->where('key', $deposit->card_type)->first()) {
                        $deposit->amount                = (float)$data->amount / 1000;
                        $deposit->bank_fee              = (float)($deposit->amount * $cardType['fee'] / 100);
                        $deposit->arrival_amount        = $deposit->amount - $deposit->bank_fee;
                        $deposit->turnover_closed_value = $deposit->arrival_amount;
                        $deposit->save();
                    }

                    $deposit->success($data);

                    $user = $deposit->user;

                    # 帐变记录
                    $transaction = (new TransactionService())->addTransaction(
                        $user,
                        $deposit->arrival_amount,
                        Transaction::TYPE_THIRD_PARTY_SAVE,
                        $deposit->id,
                        $deposit->order_no
                    );

                    if ($transaction) {
                        # 创建流水要求
                        TurnoverRequirement::add($deposit, $deposit->is_turnover_closed);
                        dispatch(new TransactionProcessJob($transaction))->onQueue('balance');
                    }
                }else {
                    $deposit->fail($data);
                }
                break;
        }
        return false;
    }

}
