<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;
use Faker\Factory as Faker;


class user_mpay_number extends TestCase
{
    use Authorization;

    /**
     * 会员添加Mpay
     *
     * @return void
     */
    public function testAdd()
    {
        # 随几取一名会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();
        $this->UserLogin($oUser->name, "123qwe"); 

        # 測試接口 start
        $sUrl = "/api/user_mpay_numbers";
        $sMethod = "POST";
        # 傳送參數設定
        $faker = Faker::create();
        $aData = [
            "area_code" => $faker->areaCode,
            "number" => $faker->imei,
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
        $sUrl = "/api/user_mpay_numbers";
        $sMethod = "GET";
        $aData = [];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
        # 測試接口 end
    }
}
