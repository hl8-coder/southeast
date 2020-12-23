<?php


namespace App\Payments;


use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\Log;

class Tpay extends Payment
{


    /**
     * 支持银行代码对照
     */
    public $aBankCode = [

        "THB" => [
            'qr' => 'QR CODE'
        ],
    ];


    /**
     * 币别对照表
     */
    public $aCurrencyCode = [
        'THB' => 'THB',
    ];


    /**
     * 状态代码对照表
     * Tpay 默认回调即成功，所以只要回调，都是成功 备注
     */
    public $aStatusCode = [
        "quickpay" => [
        ],

    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "quickpay" => [
            'appid'     => 0,
            'cliIP'     => '',
            'cliNA'     => '',
            'uid'       => '',
            'order'     => '',
            'price'     => 0,
            'payAcc'    => '', // 银行卡号，非必须
            'notifyUrl' => '',
            'sn'        => ''
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "quickpay" => [
            "customer_id"  => "appid",
            "user_id"      => "uid",
            "order_no"     => "order",
            "amount"       => "price",
            "bank_code"    => "bank_code",
            "user_ip"      => "cliIP",
            "callback_url" => "notifyUrl",
        ],
    ];


    /**
     * 范例请求栏位
     */
    public $aSampleRequestField = [
        "quickpay" => [
            'appid'     => 1000,  //Merchant ID
            'cliIP'     => '',  //user ipv4 or ipv6 address, required
            'cliNA'     => '',  //User equipment,required
            'uid'       => 'abc',  //The unique identification string of the user who placed the order
            'order'     => 'xxx',   //Merchant order id, String,
            'price'     => 0,  //amount,baht
            'payAcc'    => '',  //The payment bank card number is used to verify whether the payment is made by the user who placed the order, no verification needed, and it is not necessary to provide
            'notifyUrl' => 'http=>//xxx', //Callback url, doesn’t participate in encryption
            'sn'        => 'xxxxx'  //Signature verification value
        ],
    ];

    /**
     * 请求类型
     */
    public $aReuqstType = [
        "quickpay" => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_REDIRECT,
            "request_method" => PaymentPlatform::REQUEST_METHOD_GET,
        ],
    ];

    public function getBankCodeList($paymentPlatform, $currency)
    {
        return isset($this->aBankCode[$currency]) ? $this->aBankCode[$currency] : [];
    }

    /**
     * 签名前调整
     */
    public function modifyParamBeforeSign(Deposit $deposit, &$requestData, $platformType)
    {
        try {
            $type       = request()->header('device');
            $devices    = User::$devices;
            $userDevice = array_pull($devices, $type, 'pc');
        } catch (Exception $exception) {
            $userDevice = 'pc';
        }
        $requestData['cliNA'] = $userDevice;
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
        ksort($requestData);
        $arr = [];
        foreach ($requestData as $k => $v) {
            if ($k == 'notifyUrl' || $k == 'sn') {
                continue;
            }
            $arr[] = "{$k}=" . urlencode($v);
        }
        $arr[] = 'secret=' . $customKey;

        $requestData['sn'] = md5(join('', $arr));
    }

    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key)
    {
        $oldSign = $signData['sn'];
        unset($signData['sn']);
        $sign = '';
        switch ($platformType) {
            case 'quickpay':
                ksort($signData);
                $arr = [];
                foreach ($signData as $k => $v) {
                    $arr[] = "{$k}=" . urlencode($v);
                }
                $arr[] = 'secret=' . $key;
                $sign  = md5(join('', $arr));
                break;
        }

        return $oldSign == $sign;
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
        // 神奇的地方就是，这里支付平台回调的时候就是成功，不回调就是不成功或者其他
        return [
            'order_no'   => $data['order'],
            'is_success' => true,
            'remarks'    => $data['transaction_id'],
        ];
    }

    /**
     * 该方法仅用来修改 deposit 的 reimbursement_fee 和 bank_fee 这两个字段，不得挪作他用
     *
     * @param string $platformType
     * @param Deposit $deposit
     * @param array $data
     * @return void
     */
    public function modifyDepositFeeBeforeCalculationByCallback($platformType, Deposit $deposit, $data)
    {
        if ($platformType == 'quickpay'){
            $platform  = $deposit->paymentPlatform;
            $feeRebate = $platform->fee_rebate;
            $maxFee    = $platform->max_fee;
            $minFee    = $platform->min_fee;

            $fee = $deposit->amount * $feeRebate / 100;
            $fee = $fee >= $maxFee ? $maxFee : $fee;
            $fee = $fee <= $minFee ? $minFee : $fee;

            $deposit->bank_fee          = $fee;
            $deposit->reimbursement_fee = $fee;
            $deposit->save();
            $deposit->refresh();
        }
    }

    /**
     * 回调后更新充值内容
     */
    public function updateDepositByCallback($platformType, Deposit $deposit, $data, $type = '')
    {
        switch ($platformType) {
            case 'quickpay':
                $deposit->receive_amount = $data["amount"];
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
        return $this->callback_echo;
    }

    public function analyticalBody($platformType, $body, &$aResultData, &$sUrl, &$sError)
    {
        try {
            $returnArr = json_decode($body, true);
            if ($returnArr['e'] == 0) {
                $aResultData['request_url']        = $returnArr['d']['h5'];
                $aResultData['request_qrcode_url'] = $returnArr['d']['h5'];
                $sUrl                              = $returnArr['d']['h5'];
            }
        } catch (Exception $exception) {
            Log::error($exception);
        }
    }
}
