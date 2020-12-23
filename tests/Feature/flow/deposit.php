<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;
use App\Models\PaymentPlatform;
use App\Models\Bank;

/**
 * 充值流程测试
 *
 * @return void
 */
class deposit extends TestCase
{
    use Authorization;

    /**
     * 会员使用公司銀行卡充值
     *
     * @return void
     */
    public function testUserDepositByCompanyBankAccounts()
    {
        # 会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();

        # step 2. 会员登入
        $this->UserLogin($oUser->name, "123qwe");     

        # step 3. 取得公司银行卡 
        $sUrl = "/api/deposits/company_bank_accounts";
        $sMethod = "GET";

        # 傳送參數設定
        $aData = [];
        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        $response->assertStatus(200);
        $oCompanyBankAccount = (object)($response->json()["data"])[0];

        # step 4. 發起充值請求
        $sUrl = "/api/deposits";
        $sMethod = "POST";

        # 情境一、Online banking - ATM
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_BANKCARD,
            "payment_platform_id" => $oCompanyBankAccount->platform_id, 
            "amount" => 20000, 
            "company_bank_account_id" => $oCompanyBankAccount->id,
            "deposit_date" => "2019-07-10 10:55",
            "online_banking_channel" => PaymentPlatform::ONLINE_BANKING_CHANNEL_ATM,
            "receipts" => "1",
            "user_bank_account_id" => $oUser->bankAccounts[0]->id
        ];

        // $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        
        # 情境二、Online banking - CASH DEPOSIT
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_BANKCARD,
            "payment_platform_id" => $oCompanyBankAccount->platform_id, 
            "amount" => 100, 
            "company_bank_account_id" => $oCompanyBankAccount->id,
            "deposit_date" => "2019-07-10 10:55",
            "online_banking_channel" => PaymentPlatform::ONLINE_BANKING_CHANNEL_CASH_DEPOSIT,
            "receipts" => "2",
            "user_bank_account_name" => "chien shun",
            "user_bank_id" => Bank::first()->id,
        ];

        // $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        
        $oPaymentPlatformQuickPayHelp2 = PaymentPlatform::where("code", "Help2-quickpay")->first();
        $oPaymentPlatformQuickPayPayTrust88 = PaymentPlatform::where("code", "PayTrust88-quickpay")->first();
        $oPaymentPlatformQuickPayOneClickPay = PaymentPlatform::where("code", "OneClickPay-quickpay")->first();
        $oPaymentPlatformMpay = PaymentPlatform::where("code", "Mpay")->first();
        $oPaymentPlatformScratchCardOneClick = PaymentPlatform::where("code", "OneClickPay-card")->first();
        $oPaymentPlatformScratchCardFgate = PaymentPlatform::where("code", "Fgate247-card")->first();

        # 情境三、Quickpay - PayTrust88
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_QUICKPAY,
            "payment_platform_id" => $oPaymentPlatformQuickPayPayTrust88->id, 
            "amount" => 100, 
            "bank_code" => "59f413927793b",
            "redirect" => "http://47.89.25.81/",
        ];

        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData);

        # 情境三、Quickpay - Help2
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_QUICKPAY,
            "payment_platform_id" => $oPaymentPlatformQuickPayHelp2->id, 
            "amount" => 1, 
            "bank_code" => "MBB",
            "redirect" => "http://47.89.25.81/",
        ];

        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 


        // # 情境三、Quickpay - 1clickpay
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_QUICKPAY,
            "payment_platform_id" => $oPaymentPlatformQuickPayOneClickPay->id, 
            "amount" => 200000, 
            "bank_code" => "VCB",
            "redirect" => "http://47.89.25.81/",
        ];

        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        // # 情境四、Mpay
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_MPAY,
            "payment_platform_id" => $oPaymentPlatformMpay->id, 
            "amount" => 100, 
            "user_mpay_number" => "123456",
            "mpay_trading_code" => "123456",
        ];

        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        
        # 情境五、Scratch Card
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD,
            "payment_platform_id" => $oPaymentPlatformScratchCardOneClick->id, 
            "amount" => 100.000, 
            "card_type" => "MOBI",
            "pin_number" => "934892349",
            "serial_number" => "39492348934",
        ];
        
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        # 情境五、Scratch Card
        $aData = [
            "payment_type" => PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD,
            "payment_platform_id" => $oPaymentPlatformScratchCardFgate->id, 
            "card_type" => "fgo",
            "pin_number" => "34281566551313",
            "serial_number" => "GO6256920132503",
        ];
        
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        
    }

    private function setForm($data)
    {
        $form =  '<form method="post" action="' . $data["request_url"] .' ">';

        foreach ($data["request_data"] as $key => $value) {
            $form .= '<input name="' . $key. '" value ="' . $value . '" >';
        }

        $form .= '<input type="submit"></form>';

        return $form;
    }
}
