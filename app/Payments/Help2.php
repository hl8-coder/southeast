<?php

namespace App\Payments;

use App\Models\Currency;
use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Models\User;
use Carbon\Carbon;

class Help2 extends Payment
{

    /**
     * 支持银行代码对照
     */
    public $aBankCode = [
        "MYR" => [
            "MBB"  => "Maybank Berhad",
            "PBB"  => "Public Bank Berhad",
            "CIMB" => "CIMB Bank Berhad",
            "HLB"  => "Hong Leong Bank Berhad",
            "RHB"  => "RHB Banking Group",
            "AMB"  => "AmBank Group",
            "BIMB" => "Bank Islam Malaysia",
        ],
        "THB" => [
            "KKR"   => "Karsikorn Bank (K-Bank)",
            "BBL"   => "Bangkok Bank",
            "SCB"   => "Siam Commercial Bank",
            "KTB"   => "Krung Thai Bank",
            "BOA"   => "Bank of Ayudhya (Krungsri)",
            "GSB"   => "Government Savings Bank",
            "TMB"   => "TMB Bank Public Company Limited",
            "CIMBT" => "CIMB Thai",
            "KNK"   => "Kiatnakin Bank",
            "PPTP"  => "PROMPTPAY",
        ],
        "VND" => [
            "TCB"   => "Techcombank",
            "SACOM" => "Sacombank",
            "VCB"   => "Vietcombank",
            "ACB"   => "Asia Commercial Bank",
            "DAB"   => "DongA Bank",
            "VTB"   => "Vietinbank",
            "BIDV"  => "Bank for Investment and Development of Vietnam",
            "EXIM"  => "Eximbank Vietnam",
        ],
        "IDR" => [
            "BCA"   => "Bank Central Asia",
            "BNI"   => "Bank Negara Indonesia",
            "BRI"   => "Bank Rakyat Indonesia",
            "MDR"   => "Mandiri Bank",
            "CIMBN" => "CIMB Niaga",
        ],
        "PHP" => [
            "BDO" => "Banco de Oro",
            "MTB" => "MetroBank",
        ],
    ];

    /**
     * 支持语系代码对照
     */
    public $aLangCode = [
        "en-US" => "en-us",
        "zh-CN" => "zh-cn",
        "vi-VN" => "vi-vn",
        "th"    => "th",
    ];


    /**
     * 状态代码对照表
     */
    public $aStatusCode = [
        "quickpay" => [
            "000" => ["status" => "Success", "description" => "Deposit process is successful."],
            "001" => ["status" => "Failed", "description" => "Transfer process is incomplete. Possible reasons: The Bank or the user terminates the process before it is completed."],
            "006" => ["status" => "Approved", "description" => "The transfer approve by Gateway after verified that the transfer was completed."],
            "007" => ["status" => "Rejected", "description" => "The transfer reject by Gateway after verifying the transfer was not completed or failed."],
            "008" => ["status" => "Canceled", "description" => "The transfer has been canceled."],
            "009" => ["status" => "Pending", "description" => "The transfer still in pending status."],
        ],

    ];

    /**
     * 请求栏位,包含预设值
     */
    public $aRequestField = [
        "quickpay" => [
            "Merchant"  => "",
            "Currency"  => "",
            "Customer"  => "",
            "Reference" => "",
            "Key"       => "",
            "Amount"    => "",
            "Note"      => "",
            "Datetime"  => "",
            "FrontURI"  => "",
            "BackURI"   => "",
            "Bank"      => "",
            "Language"  => "en-us",
            "ClientIP"  => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "quickpay" => [
            "customer_id"  => "Merchant",
            "currency"     => "Currency",
            "user_id"      => "Customer",
            "order_no"     => "Reference",
            "amount"       => "Amount",
            "bank_code"    => "Bank",
            "language"     => "Language",
            "user_ip"      => "ClientIP",
            "redirect"     => "FrontURI",
            "callback_url" => "BackURI",
        ],
    ];

    /**
     * 范例请求栏位
     */
    public $aSampleRequestField = [
        "quickpay" => [
            "Merchant"  => "TA00001",
            "Currency"  => "MYR",
            "Customer"  => "220099",
            "Reference" => "1609032335",
            "Key"       => "EF369CAF6A96ACEFE77",
            "Amount"    => "1.00",
            "Note"      => "",
            "Datetime"  => "2012-05-09 04:09:41AM",
            "FrontURI"  => "http://merchant.com/Front.aspx",
            "BackURI"   => "http://merchant.com/Back.aspx",
            "Bank"      => "MBB",
            "Language"  => "en-us",
            "ClientIP"  => "128.199.171.73",
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
        # 金额只能是数值,平台是1:1000, help2是1:1, 所以有需要*1000
        if (Currency::isVND($deposit->currency)) {
            $requestData['Amount'] = format_number((int)$requestData['Amount'] * 1000, 2);
        }
        $requestData['Datetime'] = Carbon::now()->format('Y-m-d h:i:sA');
        $requestData['Language'] = $this->getLanguage($deposit->user);
    }

    /**
     * 加密
     */
    public function encryptSign($platformType, Deposit $deposit, &$requestData, $customKey)
    {
        $dateTime = Carbon::parse($requestData['Datetime'])->format('YmdHis');

        $signString = $requestData['Merchant'] . $requestData['Reference']
            . $requestData['Customer'] . $requestData['Amount']
            . $requestData['Currency'] . $dateTime
            . $customKey . $requestData['ClientIP'];

        $requestData['Key'] = strtoupper(md5($signString));
    }

    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key)
    {
        $sign = '';
        switch ($platformType) {
            case 'quickpay':
                $signString = $signData['Merchant'] . $signData['Reference']
                    . $signData['Customer'] . $signData['Amount']
                    . $signData['Currency'] . $signData['Status']
                    . $key;
                $sign       = strtoupper(md5($signString));
                break;
        }
        return hash_equals($sign, $signData['Key']);
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
            'order_no'   => $data['Reference'],
            'is_success' => in_array($data['Status'], ['000', '006']),
            'remarks'    => $data['Status'],
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
                    $deposit->receive_amount    = ((float)$data["Amount"] - (float)$data["DepositFee"]) / 1000;
                    $deposit->reimbursement_fee = ((float)$data["DepositFee"]) / 1000;
                    $deposit->bank_fee          = ((float)$data["DepositFee"]) / 1000;
                    $depositFee                 = ((float)$data["DepositFee"]) / 1000;
                    $amount                     = ((float)$data["Amount"] - (float)$data["DepositFee"]) / 1000;
                } else {
                    $deposit->receive_amount    = (float)$data["Amount"] - (float)$data["DepositFee"];
                    $deposit->reimbursement_fee = (float)$data["DepositFee"];
                    $deposit->bank_fee          = (float)$data["DepositFee"];
                    $depositFee                 = (float)$data["DepositFee"];
                    $amount                     = (float)$data["Amount"] - (float)$data["DepositFee"];
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

    /**
     * 获取语言
     * @param $user
     * @return mixed|string
     */
    public function getLanguage(User $user)
    {
        return !empty($this->aLangCode[$user->language]) ? $this->aLangCode[$user->language] : 'en-us';
    }
}
