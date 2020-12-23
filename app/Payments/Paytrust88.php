<?php

namespace App\Payments;

use App\Models\Currency;
use App\Models\Deposit;
use App\Models\PaymentPlatform;
use Illuminate\Support\Facades\Cache;

class Paytrust88 extends Payment
{

    /**
     * 支持银行代码接口地址
     */
    protected $sBankCodeApiUrl = "http://api.paytrust88.com/v1/bank";

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "quickpay" => [
            "return_url"        => "",
            "failed_return_url" => "",
            "http_post_url"     => "",
            "amount"            => "",
            "currency"          => "",
            "item_id"           => "",
            "item_description"  => "deposit",
            "name"              => "",
            "bank_code"         => "",
            "api_key"           => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "quickpay" => [
            "customer_id"  => "api_key",
            "currency"     => "currency",
            "user_id"      => "name",
            "order_no"     => "item_id",
            "amount"       => "amount",
            "bank_code"    => "bank_code",
            "redirect"     => "return_url",
            "callback_url" => "http_post_url",
        ],
    ];

    /**
     * 范例请求栏位
     */
    public $aSampleRequestField = [
        "quickpay" => [
            "return_url"        => "http:/example.com/thankyou",
            "failed_return_url" => "http:/example.com/retry",
            "http_post_url"     => "http:/example.com/invoice/1234",
            "amount"            => "45.99",
            "bank_code"         => "4828d8a8fd43",
            "currency"          => "USD",
            "item_id"           => "inv-3422",
            "item_description"  => "top up for account #1234",
            "name"              => "Jon Doe",
            "email"             => "user@example.com",
            "account"           => "4",
            "descriptor"        => "Coffeeshop_ABC",
        ],
    ];

    /**
     * 请求类型
     */
    public $aReuqstType = [
        "quickpay" => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_REDIRECT,
            "request_method" => PaymentPlatform::REQUEST_METHOD_POST,
        ],
    ];

    public $aBankCode = [
        'THB' => [
            '59f414091aeb1' => 'Kasikorn Bank',
            '59f4143921ba5' => 'Bangkok Bank',
            '59f414434c28e' => 'KTB NetBank',
            '59f414509ca5d' => 'SCB Easy',
        ],
        'VND' => [
            '5a8d9b3432bc7' => 'VietinBank',
            '5a8dbfef271b0' => 'VietComBank',
            '5a8ee643945a3' => 'TechComBank',
            '5a8eec3fc74e6' => 'SacomBank',
            '5a900eca03af6' => 'ACB',
        ],
    ];

    public function getBankCodeList($paymentPlatform, $currency)
    {
        $result = [];
        if ($paymentPlatform->code == "Paytrust88-quickpay" || $paymentPlatform->code == "Paytrust88-quickpay-thb") {

            $cache_key = $paymentPlatform->code . "-banks";

            if (Cache::has($cache_key)) {
                $result = Cache::get($cache_key);
            } else {
                $url = $this->sBankCodeApiUrl;

                $token = base64_encode($paymentPlatform->customer_id . ":");

                $opts = array(
                    'http' => array(
                        'method'  => "GET",
                        'header'  => 'Authorization: Basic ' . $token,
                        'timeout' => 1,
                    ),
                );

                $context  = stream_context_create($opts);
                $response = @file_get_contents($url, false, $context);

                if (!empty($response)) {
                    $data   = json_decode($response);
                    $result = [];
                    foreach ($data as $key => $value) {
                        if ($value->currency == $currency && $value->status == 1) {
                            $result[$value->bank_code] = $value->name;
                        }
                    }

                    Cache::put($cache_key, $result, now()->addMinutes(10));
                }
            }

            if (empty($result)) {
                Cache::forget($cache_key);
                return isset($this->aBankCode[$currency]) ? $this->aBankCode[$currency] : [];
            }
        }
        return $result;
    }

    /**
     * 签名前调整
     */
    public function modifyParamBeforeSign(Deposit $deposit, &$requestData, $platformType)
    {
        # 金额只能是数值,平台是1:1000, 所以有需要*1000
        if (Currency::isVND($deposit->currency)) {
            $requestData['amount'] = format_number((int)$requestData['amount'] * 1000, 2);
        }
        $requestData["failed_return_url"]        = $requestData["return_url"];
        $token                                   = base64_encode($requestData["api_key"] . ":");
        $requestData["headers"]["Authorization"] = "Basic " . $token;
        unset($requestData["api_key"]);
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {

    }

    /**
     * 解析回调内容
     */
    public function analyticalBody($platformType, $body, &$aResultData, &$sUrl, &$sError)
    {
        $result = json_decode($body);

        switch ($platformType) {
            case 'quickpay':
                if (isset($result->status) && $result->status === 0) {
                    $sUrl = $result->redirect_to;
                } else {
                    $sError = $result->decline_reason;
                    $this->fail($aResultData["deposit"], $result);
                }
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
            case 'quickpay':

                $signString = @($signData['transaction'] . $signData['status']
                    . $signData['currency'] . $signData['amount']
                    . $signData['created_at']);

                $sign = hash_hmac("sha256", $signString, $key);
                break;
        }

        return @$signData['signature2'] ? hash_equals($sign, $signData['signature2']) : false;
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
        return [
            'order_no'   => $data['item_id'],
            'is_success' => '1' == $data['status'],
            'remarks'    => $data['status'],
        ];
    }

    /**
     * 回调后更新充值内容
     */
    public function updateDepositByCallback($platformType, Deposit $deposit, $data, $type = '')
    {
        switch ($platformType) {
            case 'quickpay':
                // VND 需要除 1000
                if ($deposit->currency == 'VND') {
                    $deposit->receive_amount    = ((float)$data["amount"] - (float)$data["total_fees"]) / 1000;
                    $deposit->reimbursement_fee = ((float)$data["total_fees"]) / 1000;
                    $deposit->bank_fee          = ((float)$data["total_fees"]) / 1000;
                    $depositFee                 = ((float)$data["total_fees"]) / 1000;
                    $amount                     = ((float)$data["amount"] - (float)$data["total_fees"]) / 1000;
                } else {
                    $deposit->receive_amount    = (float)$data["amount"] - (float)$data["total_fees"];
                    $deposit->reimbursement_fee = (float)$data["total_fees"];
                    $deposit->bank_fee          = (float)$data["total_fees"];
                    $depositFee                 = (float)$data["total_fees"];
                    $amount                     = (float)$data["amount"] - (float)$data["total_fees"];
                }

                $transaction = $deposit->pgAccountTransaction;
                if ($transaction) {
                    $transaction->fee    = $depositFee;
                    $transaction->amount = $amount;
                    $transaction->save();
                }
                $deposit->save();
                break;
        }
    }
}
