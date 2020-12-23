<?php
namespace App\Payments;

use App\Models\Deposit;

class Payment {

    /**
     * 回调显示讯息
     */
    public $callback_echo = "success";


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
    }

    /**
     * 签名后调整
     */
    public function modifyParamAfterSign(&$requestData, $platformType)
    {

    }

    public function modifyReturnDataForFront(&$requestData, &$returnData)
    {

    }

    /**
     * 获取header
     */
    public function getHeader($platformType)
    {

    }

    /**
     * 解析回调内容
     */
    public function analyticalBody($platformType, $body, &$aResultData, &$sUrl, &$sError)
    {

    }

    public function analyticalCallbackData($platformType, $data)
    {
        return $data;
    }

    /**
     * 验签
     */
    public function checkCallBackSign($platformType, $signData, $key) {}

    /**
     * 获取信息
     * order_no     交易订单号
     * is_success   是否上分成功
     * remarks      失败信息
     *
     * @param $data
     */
    public function getCallbackDepositResult($platformType, $data) {}

    public function getBankCodeList($paymentPlatform, $currency) { return []; }

    public function getCardTypeList($paymentPlatform, $currency) { return []; }


    /**
     * 该方法仅用来修改 deposit 的 reimbursement_fee 和 bank_fee 这两个字段，不得挪作他用
     *
     * @param string $platformType
     * @param Deposit $deposit
     * @param array $data
     * @return void
     */
    public function modifyDepositFeeBeforeCalculationByCallback($platformType, Deposit $deposit, $data){}


    /**
     * 回调后更新充值内容
     */
    public function updateDepositByCallback($platformType, Deposit $deposit, $data, $type='') {}

    public function removeEmpty(&$signData)
    {
        foreach ($signData as $key => $value) {
            if(!$value) {
                unset($signData[$key]);
            }
        }
    }

    public function sort(&$signData)
    {
        ksort($signData);
    }

    public function fail($deposit, $callback) {
        unset($deposit["deposit_created_at"]);
        unset($deposit["callback_url"]);
        $deposit->fail((array)$callback);
    }

    public function noticeVendor($deposit = null, $callbackData = null)
    {
        return $this->callback_echo;
    }
}
