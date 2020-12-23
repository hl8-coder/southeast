<?php

namespace App\Payments;

use App\Models\Deposit;
use App\Models\PaymentPlatform;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Models\Transaction;
use App\Models\TurnoverRequirement;
use App\Jobs\TransactionProcessJob;
use App\Services\TransactionService;
use Cache;

class Fgate247 extends Payment
{

    /**
     * 充值卡類型码对照
     */
    public $aCardType = [
        ["key" => "fgo", "value" => "fgo", 'fee' => '15'],
    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "card" => [
            "pin"        => "",
            "serial"     => "",
            "tran_id"    => "",
            "type"       => "",
            "token"      => "",
            "partner_id" => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "card" => [
            "customer_id"   => "partner_id",
            "card_type"     => "type",
            "order_no"      => "tran_id",
            "pin_number"    => "pin",
            "serial_number" => "serial",
        ],
    ];

    /**
     * 请求类型
     */
    public $aReuqstType = [
        "card" => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_MESSAGE,
            "request_method" => PaymentPlatform::REQUEST_METHOD_POST,
        ],
    ];

    public function getCardTypeList($paymentPlatform, $currency)
    {
        switch ($paymentPlatform->code) {
            case 'Fgate247-card':
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
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
        switch ($platformType) {
            case 'card':
                $data = $requestData['tran_id'] .
                        $requestData['pin'] .
                        $requestData['serial'] .
                        $requestData['type'];
                $requestData['token'] = hash_hmac('sha256', $data, $customKey);
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key)
    {
        $sign = '';
        switch ($platformType) {
            case 'card':
                return true;
                break;
        }

        return hash_equals($sign, $signData['sign']);
    }

    /**
     * 解析回调内容
     */
    public function analyticalBody($platformType, $body, &$aResultData, &$sUrl, &$sError)
    {
        $result = json_decode($body);
        switch ($platformType) {
            case 'card':
                if ($result->error_code != "00") {
                    $sError = $result->error_message;
                    $this->fail($aResultData["deposit"], $result);
                } else if ($result->status != 1) {
                    $sError = $result->message;
                    $this->fail($aResultData["deposit"], $result);
                }else if ($result->status == 1) {
                    $deposit = Deposit::findByOrderNo($aResultData["deposit"]->order_no);
                    $this->updateDeposit($platformType,  $deposit, $result, 'callback');
                }
                break;
        }
    }


    /**
     * No need to create
     */
    public function updateDeposit($platformType, Deposit $deposit, $data)
    {

        switch ($platformType) {
            case 'card':
                # 充值成功能直接发起帐变不用透过回调
                if ($data->status == 1 && $deposit->status != Deposit::STATUS_RECHARGE_SUCCESS) {


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
                }
                break;
        }
    }

}
