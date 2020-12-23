<?php

namespace App\Payments;

use App\Models\Deposit;
use App\Models\PaymentPlatform;
use Cache;

class Congthe extends Payment
{
    public $callback_echo = "";

    /**
     * 充值卡類型码对照
     */
    public $aCardType = [
        ["key" => "VIETTEL", "value" => "VIETTEL", 'fee' => '32'],
        ["key" => "MOBIFONE", "value" => "MOBIFONE", 'fee' => '37'],
        ["key" => "VINAPHONE", "value" => "VINAPHONE", 'fee' => '30'],
    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "card"     => [
            "telco"         => "",
            "code"          => "",
            "serial"        => "",
            "amount"        => "",
            "partner_id"    => "",
            "sign"          => "",
            "command"       => "",
            "request_id"    => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "card"     => [
            "card_type"     => "telco",
            "pin_number"    => "code",
            "serial_number" => "serial",
            "amount"        => "amount",
            "customer_id"   => "partner_id",
            "order_no"      => "request_id",
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
            case 'Congthe-card':
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
        # 金额只能是数值,平台是1:1000, oneclick是1:1, 所以有需要*1000
        $requestData['amount'] = format_number((int)$requestData['amount'] * 1000, 2);

        switch ($platformType) {
            case 'card':
                $requestData['command'] = 'charging';
                break;

            default:
                # code...
                break;
        }
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
        switch ($platformType) {
            case 'card':
                $signData = collect($requestData)->only(['request_id', 'code', 'partner_id', 'serial', 'telco', 'command'])->sortKeys()->toArray();
                $sign = $customKey;
                foreach ($signData as $item) {
                    $sign .= $item;
                }
                $requestData['sign'] = md5($sign);
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
                $signString = $key . $signData['code'] . $signData['serial'];
                $sign       = md5($signString);
                break;
        }
        return hash_equals($sign, $signData['callback_sign']);
    }

    /**
     * 解析回调内容
     */
    public function analyticalBody($platformType, $body, &$aResultData, &$sUrl, &$sError)
    {
        $result = json_decode($body);

        switch ($platformType) {
            case 'card':
                if (!in_array($result->status, [1, 2, 9])) {
                    $sError = $result->message;
                    $this->fail($aResultData['deposit'], $result);
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
                return [
                    'order_no'   => $data['request_id'],
                    'is_success' => in_array($data['status'], [1, 2]),
                    'remarks'    => isset($data['message']) ? $data['message'] : '',
                ];
                break;
        }
    }


    /**
     * 回调后更新充值内容
     */
    public function updateDepositByCallback($platformType, Deposit $deposit, $data, $type='')
    {
        # 均以回调为准
        switch ($platformType) {
            case 'card':
                if ('callback' == $type) {
                    $deposit->amount                = (float)$data['value']  / 1000;
                    $deposit->arrival_amount        = (float)$data['amount'] / 1000;
                    $deposit->bank_fee              = $deposit->amount - $deposit->arrival_amount;
                    $deposit->turnover_closed_value = $deposit->arrival_amount;
                    $deposit->save();

                    $echoData = collect($data)->only(['status', 'message', 'request_id', 'trans_id', 'declared_value', 'value', 'amount', 'code', 'serial', 'telco'])->toArray();
                    echo json_encode($echoData);
                }
                break;
        }
    }
}
