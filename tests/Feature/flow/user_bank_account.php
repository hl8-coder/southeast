<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;
use Faker\Factory as Faker;

class user_bank_account extends TestCase
{
    use Authorization;

    /**
     * 会员添加银行卡
     *
     * @return void
     */
    public function testAdd()
    {
        # 随几取一名会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();
        $this->UserLogin($oUser->name, "123qwe"); 

        # 取得有效銀行資訊
        $oBank = \App\Models\Bank::first();

        # 測試接口 start
        $sUrl = "/api/user_bank_accounts?include=bank";
        $sMethod = "POST";
        # 傳送參數設定
        $faker = Faker::create();
        $aData = [
            "account_name" => $faker->name,
            "bank_id" => $oBank->id,
            "branch" => "test",
            "province" => "test",
            "city" => "test",
            "is_preferred" => "0",
            "account_no" => $faker->bankAccountNumber,

        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        # 傳送參數設定
        $faker = Faker::create();
        $aData = [
            "account_name" => $faker->name,
            "bank_id" => $oBank->id,
            "branch" => "test",
            "province" => "test",
            "city" => "test",
            "is_preferred" => 1,
            "account_no" => $faker->bankAccountNumber,

        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(201);
        # 測試接口 end
    }

    public function testInfo()
    {
        # 随几取一名会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();
        $this->UserLogin($oUser->name, "123qwe"); 

        # 測試接口 start
        $sUrl = "/api/user_bank_accounts?include=bank";
        $sMethod = "GET";
        $aData = [];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
        # 測試接口 end

    }
}
