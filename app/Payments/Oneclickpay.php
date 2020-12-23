<?php

namespace App\Payments;

use App\Models\Deposit;
use App\Models\PaymentPlatform;
use App\Models\Transaction;
use App\Jobs\TransactionProcessJob;
use App\Models\TurnoverRequirement;
use App\Services\TransactionService;
use Cache;

class Oneclickpay extends Payment
{

    /**
     * 支持银行代码接口地址
     */
    protected $sBankCodeApiUrl = "http://1clickpay.co/api/v2/payment/listBank";

    /**
     * 支持银行代码对照
     */
    public $aBankCode = [
        "VND" => [
            'VCB'  => 'VietcomBank',
            'VTB'  => 'ViettinBank',
            'TCB'  => 'Techcombank',
            'VIB'  => 'VIB',
            'BIDV' => 'BIDV',
            'SHB'  => 'SHB',
            'DAB'  => 'DongABank',
            'ACB'  => 'ACB',
            'TPB'  => 'TPBank',
            'EXB'  => 'Eximbank',
            'SAC'  => 'Sacombank',
            'SCB'  => 'SaigonBank',
        ],
    ];

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
        ["key" => "VIETTEL", "value" => "VIETTEL", 'fee' => '40'],
        ["key" => "MOBI", "value" => "MOBIFONE", 'fee' => '40'],
        ["key" => "VINA", "value" => "VINAPHONE", 'fee' => '40'],
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
            "merchant_id"       => "",
            "merchant_txn"      => "",
            "merchant_customer" => "",
            "amount"            => "",
            "bank_code"         => "",
            "description"       => "",
            "url_success"       => "",
            "url_error"         => "",
            "sign"              => "",
        ],
        "card"     => [
            "order_id"      => "",
            "merchant_id"   => "",
            "merchant_pass" => "",
            "pin"           => "",
            "seri"          => "",
            "type"          => "",
            "amount"        => "",
            "note"          => "",
        ],
    ];

    /**
     * 自动对应栏位
     */
    public $aMappingField = [
        "quickpay" => [
            "customer_id" => "merchant_id",
            "user_id"     => "merchant_customer",
            "order_no"    => "merchant_txn",
            "amount"      => "amount",
            "bank_code"   => "bank_code",
            "redirect"    => "url_success",
        ],
        "card"     => [
            "customer_id"   => "merchant_id",
            "card_type"     => "type",
            "order_no"      => "order_id",
            "amount"        => "amount",
            "bank_code"     => "bank_code",
            "pin_number"    => "pin",
            "serial_number" => "seri",
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
        "card"     => [
            "request_type"   => PaymentPlatform::REQUEST_TYPE_MESSAGE,
            "request_method" => PaymentPlatform::REQUEST_METHOD_GET,
        ],
    ];

    public function getBankCodeList($paymentPlatform, $currency)
    {
        $result = [];

        if ($paymentPlatform->code == "OneClickPay-quickpay") {

            $cache_key = $paymentPlatform->code . "-banks";

            if (Cache::has($cache_key)) {
                $result = Cache::get($cache_key);
            } else {
                $query["merchant_id"] = $paymentPlatform->customer_id;
                $query["sign"]        = md5($paymentPlatform->customer_id . $paymentPlatform->customer_key);

                $url  = $this->sBankCodeApiUrl . "?" . http_build_query($query);
                $opts = array(
                    'http' => array(
                        'method'  => "GET",
                        'timeout' => 0.2,
                    ),
                );

                $context = stream_context_create($opts);

                $response = @file_get_contents($url, false, $context);

                $data = json_decode($response);

                if (isset($data->code) && $data->code != 14) {
                    $result = [];

                    foreach ($data->data as $key => $value) {
                        $result[$value->code] = $value->bank_name;
                    }

                    Cache::put($cache_key, $result, now()->addMinutes(10));
                }
            }

            if (empty($result)) {
                return isset($this->aBankCode[$currency]) ? $this->aBankCode[$currency] : [];
            }
        }

        return $result;
    }

    public function getCardTypeList($paymentPlatform, $currency)
    {
        switch ($paymentPlatform->code) {
            case 'OneClickPay-card':
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
        $requestData['amount'] = (int)$requestData['amount'] * 1000;

        switch ($platformType) {
            case 'quickpay':
                # no error page ,so same of success page
                $requestData['url_error'] = $requestData['url_success'];
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
            case 'quickpay':
                $signString = $requestData['merchant_id'] . $requestData['merchant_txn']
                    . $requestData['merchant_customer'] . $requestData['amount']
                    . $requestData['bank_code'] . $customKey;

                $requestData['sign'] = md5($signString);
                break;
            case 'card':
                $requestData['merchant_pass'] = $customKey;
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
            case 'quickpay':
                $signString = $signData['merchant_txn'] . $signData['merchant_customer']
                    . $signData['amount'] . $signData['net_amount']
                    . $signData['tnx']
                    . $key;
                $sign       = md5($signString);

                break;
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
            case 'quickpay':
                if ($result->code === 0) {
                    $sUrl = $result->data->redirect_url;
                } else {
                    $sError = json_encode($result->message);
                    $this->fail($aResultData["deposit"], $result);
                }
                break;
            case 'card':
                if ($result->code !== 1) {
                    $sError = $result->msg;
                    $this->fail($aResultData["deposit"], $result);
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
            case 'quickpay':
                return [
                    'order_no'   => $data['merchant_txn'],
                    'is_success' => true,
                    'remarks'    => '',
                ];
                break;
        }
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
                $deposit->reimbursement_fee = ($data["amount"] - $data["net_amount"]) / 1000;
                $deposit->bank_fee          = ($data["amount"] - $data["net_amount"]) / 1000;
            } else {
                $deposit->reimbursement_fee = $data["amount"] - $data["net_amount"];
                $deposit->bank_fee          = $data["amount"] - $data["net_amount"];
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
                // VND 需要除 1000
                $depositFee = 0;
                if ($deposit->currency == 'VND') {
                    $deposit->receive_amount    = $data["net_amount"] / 1000;
                } else {
                    $deposit->receive_amount    = $data["net_amount"];
                }

                $deposit->save();
                break;
            case 'card':
                # 充值成功能直接发起帐变不用透过回调
                if ($data['code'] == 1) {

                    # 计算手续费
                    if ($cardType = collect($this->aCardType)->where('key', $deposit->card_type)->first()) {
                        $deposit->amount                = (float)$data['data']['amount'] / 1000;
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
