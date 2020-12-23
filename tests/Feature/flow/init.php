<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;
use App\Models\CompanyBankAccount;
use App\Models\PaymentGroup;
use App\Models\PaymentPlatform;
use App\Models\Bank;
use Faker\Factory as Faker;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Maatwebsite\Excel\Facades\Excel;

class init extends TestCase
{
	use Authorization;

    /**
     * 建立公司銀行卡
     *
     * @return void
     */
    public function testCompanyBankAccounts()
    {
        factory(\App\Models\User::class,200)->create();
    	# 登入取得授權
        $this->AdminloginNoMenu($this->sLoginName, $this->sLoginPwd);


        //$this->_test2();

        # 建立第三方充值渠道
        $this->_create3rd();

        # 建立公司銀行卡
        $this->_createCompanyBankAccount();        

    }

    private function _test3()
    {
        Storage::fake('local');
        $file = UploadedFile::fake()->create('/home/vagrant/code/PHP/VCB4046.xls');

        # 測試接口 start
        $sUrl = "/api/backstage/bank_transactions/excel";
        $sMethod = "POST";
        # 傳送參數設定
        $aData = [
            'currency' => 'VND',
            'fund_in_account' => 'TCB-01',
            'excel' => $file
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        dd($response);
        $response->assertStatus(201);
    }

    private function _test2()
    {
        $this->UserLogin('weissnat', '123qwe');

        Storage::fake('avatars');

        # 測試接口 start
        $sUrl = "/api/images";
        $sMethod = "POST";
        # 傳送參數設定
        $aData = [
            'image' => UploadedFile::fake()->image('avatar.jpg')
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        dd($response);
        $response->assertStatus(201);
    }

    private function _test()
    {
        Storage::fake('avatars');

        # 測試接口 start
        $sUrl = "/api/backstage/deposits/1/receipt";
        $sMethod = "POST";
        # 傳送參數設定
        $aData = [
            'image' => UploadedFile::fake()->image('avatar.jpg')
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        dd($response);
        $response->assertStatus(201);
    }

    # 建立公司銀行卡
    private function _createCompanyBankAccount()
    {
        # 測試接口 start
        $sUrl = "/api/backstage/company_bank_accounts";
        $sMethod = "POST";
        # 傳送參數設定
        $faker = Faker::create('vi_VN');
        $aData = [
            "type" => CompanyBankAccount::TYPE_DEPOSIT,
            "payment_group_id" => PaymentGroup::first()->id,
            "bank_id" => Bank::first()->id,
            "province" => $faker->province,
            "city" => $faker->city,
            "branch" => $faker->province,
            "account_name" => $faker->firstName,
            "account_no" => $faker->bankAccountNumber,
            "code" => 'TCB-01',
            "user_name" => $faker->firstName,
            "password" => "123qwe",
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        $response->assertStatus(201);
        # 測試接口 end
    }

    # 建立第三方充值渠道
    private function _create3rd()
    {
        # Quickpay -> help2
        if(!($oPaymentPlatformQuickPayHelp2 = PaymentPlatform::where("code", "Help2-quickpay")->first())) {
            $oPaymentPlatformQuickPayHelp2 = factory(\App\Models\PaymentPlatform::class)->create([
                "payment_type" => PaymentPlatform::PAYMENT_TYPE_QUICKPAY, 
                "name" => "Help2Pay",
                "display_name" => "Help2Pay",
                "code" => "Help2-quickpay",
                "customer_id" => "M0344",
                "customer_key" => "pnPbtU8WhgZKxm8",
                "request_url" => "http://api.besthappylife.biz/MerchantTransfer",
            ]);
        }

        # Quickpay -> PayTrust88
        if(!($oPaymentPlatformQuickPayTrust88 = PaymentPlatform::where("code", "PayTrust88-quickpay")->first())) {
            $oPaymentPlatformQuickPayTrust88 = factory(\App\Models\PaymentPlatform::class)->create([
                "payment_type" => PaymentPlatform::PAYMENT_TYPE_QUICKPAY, 
                "name" => "PayTrust88",
                "display_name" => "PayTrust88",
                "code" => "Paytrust88-quickpay",
                "customer_id" => "CJGxfqXqNi21GDZR8LTgF1pZTdxJMHqf",
                "customer_key" => "CJGxfqXqNi21GDZR8LTgF1pZTdxJMHqf",
                "request_url" => "https://api.paytrust88.com/v1/transaction/start",
            ]);
        }

        # Quickpay -> 1clickpay
        if(!($oPaymentPlatformQuickPayOneClickPay = PaymentPlatform::where("code", "OneClickPay-quickpay")->first())) {
            $oPaymentPlatformQuickPayOneClickPay = factory(\App\Models\PaymentPlatform::class)->create([
                "payment_type" => PaymentPlatform::PAYMENT_TYPE_QUICKPAY, 
                "name" => "1ClickPay",
                "display_name" => "1ClickPay",
                "code" => "OneClickPay-quickpay",
                "customer_id" => "C00075",
                "customer_key" => '$10$EgM8o8PHJ.o8',
                "request_url" => "https://1clickpay.co/api/v2/payment/request",
                "is_fee" => false,
                "fee_rebate" => 0.023,
            ]);
        }

        # Mpay
        if(!($oPaymentPlatformMpay = PaymentPlatform::where("code", "Mpay")->first())) {
            $oPaymentPlatformMpay = factory(\App\Models\PaymentPlatform::class)->create([
                "payment_type" => PaymentPlatform::PAYMENT_TYPE_MPAY, 
                "name" => "Mpay",
                "display_name" => "Mpay",
                "code" => "Mpay",
            ]);

        }

        # Scratch Card 1ClickPay
        if(!($oPaymentPlatformScratchCardOneClick = PaymentPlatform::where("code", "OneClickPay-card")->first())) {
            $oPaymentPlatformScratchCardOneClick = factory(\App\Models\PaymentPlatform::class)->create([
                "payment_type" => PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD, 
                "name" => "Scratch 1",
                "display_name" => "Scratch 1",
                "code" => "OneClickPay-card",
                "customer_id" => "100002",
                "customer_key" => 'zqQSjTrYq3YaRSb',
                "request_url" => "http://card.1clickpay.co/services",
                "is_fee" => false,
                "fee_rebate" => 0.023,
            ]);
        }

        # Scratch Card FGATE247
        if(!($oPaymentPlatformScratchCardFgate = PaymentPlatform::where("code", "Fgate247-card")->first())) {
            $oPaymentPlatformScratchCardFgate = factory(\App\Models\PaymentPlatform::class)->create([
                "payment_type" => PaymentPlatform::PAYMENT_TYPE_SCRATCH_CARD, 
                "name" => "Scratch 2",
                "display_name" => "Scratch 2",
                "code" => "Fgate247-card",
                "customer_id" => "76",
                "customer_key" => 'rwY8XO',
                "request_url" => "https://api.fgate247.com/charge_card/",
                "is_need_type_amount" => false,
            ]);
        }
    }

    /**
     * 更新公司銀行卡
     *
     * @return void
     */
    public function CompanyBankAccountsUpdate()
    {
        # 登入取得授權
        $this->AdminloginNoMenu($this->sLoginName, $this->sLoginPwd);

        # 測試接口 start
        $sUrl = "/api/backstage/company_bank_accounts/1";
        $sMethod = "PATCH";
        # 傳送參數設定
        $faker = Faker::create('vi_VN');
        $aData = [
            "type" => CompanyBankAccount::TYPE_DEPOSIT,
            "payment_group_id" => paymentGroup::first()->id,
            "bank_id" => Bank::first()->id,
            "province" => $faker->province,
            "city" => $faker->city,
            "branch" => $faker->province,
            "account_name" => $faker->firstName,
            "account_no" => $faker->bankAccountNumber,
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
        # 測試接口 end
    }

    /**
     * 公司銀行卡列表
     *
     * @return void
     */
    public function CompanyBankAccountsInfo()
    {
    	# 登入取得授權
        $this->AdminloginNoMenu($this->sLoginName, $this->sLoginPwd);

        # 測試接口 start
        $sUrl = "/api/backstage/company_bank_accounts?include=bank,paymentGroup";
        $sMethod = "GET";
        # 傳送參數設定
        $aData = [];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
        # 測試接口 end
    }
}
