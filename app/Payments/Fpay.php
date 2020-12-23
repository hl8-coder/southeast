<?php

namespace App\Payments;

use App\Models\Config;
use App\Models\Currency;
use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Models\User;
use Carbon\Carbon;
use GuzzleHttp\Client;
use function GuzzleHttp\Psr7\build_query;

class Fpay extends Payment
{

    /**
     * 支持银行代码对照
     */
    public $aBankCode = [];


    /**
     * 币别对照表
     */
    public $aCurrencyCode = [
        'VND' => 'VND',
        'THB' => 'THB',
    ];


    /**
     * 状态代码对照表
     * 好像没用，先不理
     */
    public $aStatusCode = [
        "quickpay" => [
            'success' => ["status" => "Success", "description" => "Deposit process is successful."],
            'fail'    => ["status" => "Failed", "description" => "Transfer process is fail."],
        ],

    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "quickpay" => [
            "merchant"  => "",
            "orderNum"  => "",
            "amount"    => "",
            "bank"      => "",
            "returnUrl" => "",
            "sign"      => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "quickpay" => [
            "customer_id" => "merchant",
            // "currency"     => "currency",
            // "user_id"      => "member_id",
            "order_no"    => "orderNum",
            "amount"      => "amount",
            "bank_code"   => "bank",
            // "user_ip"      => "member_ip",
            "redirect"    => "returnUrl",
            // "callback_url" => "backend_url",
        ],
    ];


    /**
     * 范例请求栏位
     */
    public $aSampleRequestField = [
        "quickpay" => [
            "merchant"  => "MC200512103703",
            "orderNum"  => "MOD1595815359619",
            "amount"    => "5000000",
            "bank"      => "BidvBank",
            "returnUrl" => "http://merchant.com/returnAPI?isSuccess={isSuccess}&order={order}",
            "sign"      => "130349dc1ad6dbc6d8a531273c1f8833",
        ],
    ];

    /**
     * 请求类型
     */
    public $aReuqstType = [
        "quickpay" => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_GET,
            "request_method" => PaymentPlatform::REQUEST_METHOD_GET,
        ],
    ];

    public function getBankCodeList($paymentPlatform, $currency)
    {
        $time     = mb_substr(now()->timestamp . now()->micro, 0, 13);
        $bankUrl  = Config::findValue('fpay_query_bank_api');
        $queryStr = 'merchant=' . $paymentPlatform->customer_id . '&type=online&stamp=' . $time;
        $signStr  = $paymentPlatform->customer_id . '/online/' . $time;
        $queryStr .= '&sign=' . hash_hmac('md5', $signStr, $paymentPlatform->customer_key);
        $http     = new Client();
        $response = $http->get($bankUrl . '?' . $queryStr);
        $body     = $response->getBody();
        $bankList = json_decode($body, true);
        $banks    = [];
        if ($bankList) {
            foreach ($bankList as $bankInfo) {
                $banks[$bankInfo['code']] = $bankInfo['name'];
            }
        }
        # 这里强行加入 QR code 支付方式，暂时不开发，vendor 未通知开放
        // $banks['momo'] = 'QR Scan';

        return $banks;
    }

    public function modifyReturnDataForFront(&$requestData, &$returnData)
    {
        if (isset($requestData['bank']) && $requestData['bank'] == 'momo') {
            $returnData['request_url'] = str_replace('transfer', 'momo/transfer', $returnData['request_url']);
        }
        $returnData['request_url'] = $returnData['request_url'] . '?' . build_query($requestData);
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
        $fieldOrder = $this->aRequestField['quickpay'];
        $signString = '';
        unset($fieldOrder['returnUrl'], $fieldOrder['sign']);
        foreach ($fieldOrder as $orderKey => $value) {
            $signString .= $requestData[$orderKey] . '/';
        }
        $requestData['sign'] = hash_hmac('md5', trim($signString, '/'), $customKey);
    }


    /**
     * 签名前调整
     */
    public function modifyParamBeforeSign(Deposit $deposit, &$requestData, $platformType)
    {
        if (Currency::isVND($deposit->currency)) {
            $requestData['amount'] = $requestData['amount'] * 1000;
        }
    }


    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key)
    {
        $sign = '';
        switch ($platformType) {
            case 'quickpay':
                $signStr = 'merchant=' . $signData['merchant'];
                $signStr .= '&orderNum=' . $signData['orderNum'];
                $signStr .= '&depositType=' . $signData['depositType'];
                $signStr .= '&amount=' . $signData['amount'];
                $signStr .= '&originalAmount=' . $signData['originalAmount'];
                $signStr .= '&status=' . $signData['status'];
                $sign    = hash_hmac('md5', $signStr, $key);
                break;
        }

        return hash_equals($sign, $signData['sign']);
    }

    /**
     * 获取信息
     *
     * @param $platformType
     * @param $data
     * @return mixed
     */
    public function getCallbackDepositResult($platformType, $data)
    {
        return [
            'order_no'   => $data['orderNum'],
            'is_success' => strtolower($data['status']) == 'success',
        ];
    }


    /**
     * 回调后更新充值内容
     */
    public function updateDepositByCallback($platformType, Deposit $deposit, $data, $type = '')
    {
        switch ($platformType) {
            case 'quickpay':
                // VND 需要除 1000 * 100
                if ($deposit->currency == 'VND') {
                    $deposit->receive_amount = $data["amount"] / 1000;
                    //    $deposit->reimbursement_fee = ((int)$data["fee"]) / 100000;
                    //    $deposit->bank_fee          = ((int)$data["fee"]) / 100000;
                    //    $depositFee                 = ((int)$data["fee"]) / 100000;
                    //    $amount                     = ((int)$data["request_amount"]) / 100000;
                } else {
                    $deposit->receive_amount = $data["amount"];
                    //    $deposit->reimbursement_fee = ((int)$data["fee"]) / 100;
                    //    $deposit->bank_fee          = ((int)$data["fee"]) / 100;
                    //    $depositFee                 = ((int)$data["fee"]) / 100;
                    //    $amount                     = ((int)$data["request_amount"]) / 100;
                }
                $deposit->save();
                break;
        }
    }

    public function analyticalCallbackData($platformType, $data)
    {
        return $data;
    }

    public function noticeVendor($deposit = null, $callbackData = null)
    {
        return json_encode(['status' => 'success']);
    }
}
