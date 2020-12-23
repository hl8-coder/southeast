<?php

namespace App\Payments;

use App\Models\Currency;
use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Models\User;
use Carbon\Carbon;

class Eeziepay extends Payment
{

    /**
     * 支持银行代码对照
     */
    public $aBankCode = [

        "THB" => [
            "KTB.TH"   => "Krung Thai Bank",
            "SCB.TH"   => "Siam Commercial Bank",
            "BBL.TH"   => "Bangkok Bank",
            "KBANK.TH" => "Karsikorn Bank (K-Bank)",
            "TMB.TH"   => "TMB Bank Public Company Limited",
            "BAY.TH"   => "Bank of Ayudhya (Krungsri)",
            "GSB.TH"   => "Government Savings Bank",
//            "QR.TH"    => "Thai QR Payment",
        ],
        "VND" => [
            "TCB.VN"   => "Techcombank",
            "SCM.VN"   => "Sacombank",
            "VCB.VN"   => "Vietcombank",
            "ACB.VN"   => "Asia Commercial Bank",
            "DAB.VN"   => "DongA Bank",
            "VTB.VN"   => "Vietinbank",
            "BIDV.VN"  => "Bank for Investment and Development of Vietnam",
            "EXIM.VN"  => "Eximbank Vietnam",
            "VBARD.VN" => "Agribank",
        ],
    ];


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
            "000" => ["status" => "Success", "description" => "Deposit process is successful."],
            "001" => ["status" => "Pending", "description" => "The transfer still in pending status."],
            "002" => ["status" => "Success", "description" => "Deposit process is Bank Success."], // ??
            "110" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Expired."],
            "111" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Fail."],
            "112" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Login Error."],
            "113" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Amount Error"],
            "114" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Pin Error."],
            "115" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Pin Timeout."],
            "116" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Login Timeout."],
            "117" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Account Timeout."],
            "118" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Security Question error."],
            "119" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: User Abort."],
            "200" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: Refunded."],
        ],

    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "quickpay" => [
            "service_version" => "3.0",
            "partner_code"    => "",
            "partner_orderid" => "",
            "member_id"       => "",
            "member_ip"       => "",
            "currency"        => "",
            "amount"          => "",
            "backend_url"     => "",
            "redirect_url"    => "",
            "bank_code"       => "",
            "trans_time"      => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "quickpay" => [
            "customer_id"  => "partner_code",
            "currency"     => "currency",
            "user_id"      => "member_id",
            "order_no"     => "partner_orderid",
            "amount"       => "amount",
            "bank_code"    => "bank_code",
            "user_ip"      => "member_ip",
            "redirect"     => "redirect_url",
            "callback_url" => "backend_url",
        ],
    ];


    /**
     * 范例请求栏位
     */
    public $aSampleRequestField = [
        "quickpay" => [
            "service_version" => "3.0",
            "partner_code"    => "ABC123",
            "partner_orderid" => "20190606004030",
            "member_id"       => "test",
            "member_ip"       => "205.245.167.89",
            "currency"        => "VND",
            "amount"          => "100000",
            "backend_url"     => "http://www.returnlocation.com/server.asp",
            "redirect_url"    => "http://www.returnlocation.com/browser.asp",
            "bank_code"       => "VCB.VN",
            "trans_time"      => "2019-06-06 13:10:00",
        ],
    ];

    /**
     * 请求类型
     */
    public $aReuqstType = [
        "quickpay" => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_FROM,
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
        if (Currency::isVND($deposit->currency)) {
            $requestData['amount'] = (int)$requestData['amount'] * 1000;
        }
        # Eeziepay 的金额数据仅接受整数，并且接受的金额是实际金额的一百倍，即小数部分直接 *100 转化为整数
        $requestData['amount']     = (int)$requestData['amount'] * 100;
        $requestData['trans_time'] = now()->toDateTimeString();
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
        $fieldOrder = $this->aRequestField['quickpay'];
        $signString = '';
        foreach ($fieldOrder as $orderKey => $value) {
            if ($orderKey == 'amount') {
                $signString .= $orderKey . '=' . (int)$requestData[$orderKey] . '&';
            } else {
                $signString .= $orderKey . '=' . $requestData[$orderKey] . '&';
            }
        }
        $signString .= 'key=' . $customKey;

        $requestData['sign'] = strtoupper(sha1($signString));
    }

    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key)
    {
        $sign = '';
        switch ($platformType) {
            case 'quickpay':
                $signString = 'service_version=' . $signData['service_version'] . '&'
                    . 'billno=' . $signData['billno'] . '&'
                    . 'partner_orderid=' . $signData['partner_orderid'] . '&'
                    . 'currency=' . $signData['currency'] . '&'
                    . 'request_amount=' . $signData['request_amount'] . '&'
                    . 'receive_amount=' . $signData['receive_amount'] . '&'
                    . 'fee=' . $signData['fee'] . '&'
                    . 'status=' . $signData['status'] . '&'
                    . 'key=' . $key;
                $sign       = strtoupper(sha1($signString));
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
            'order_no'   => $data['partner_orderid'],
            'is_success' => in_array($data['status'], ['000', '002']),
            'remarks'    => $data['status'],
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
            if ($deposit->currency == 'VND') {
                $deposit->reimbursement_fee = ((float)$data["fee"]) / 100000;
                $deposit->bank_fee          = ((float)$data["fee"]) / 100000;
            } else {
                $deposit->reimbursement_fee = ((float)$data["fee"]) / 100;
                $deposit->bank_fee          = ((float)$data["fee"]) / 100;
            }
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
                // VND 需要除 1000 * 100
                if ($deposit->currency == 'VND') {
                    $deposit->receive_amount    = ((float)$data["receive_amount"]) / 100000;
                } else {
                    $deposit->receive_amount    = ((float)$data["receive_amount"]) / 100;
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
        $billno = $callbackData['billno'];
        $str = '<xml>
<billno>billno_back</billno>
<status>OK</status>
</xml>';
        return str_replace('billno_back', $billno, $str);
    }
}
