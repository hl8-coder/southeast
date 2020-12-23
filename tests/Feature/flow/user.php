<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;
use Faker\Factory as Faker;

class user extends TestCase
{
    use Authorization;

    /**
     * 会员注册
     *
     * @return void
     */
    public function testRegister()
    {
        $faker = Faker::create();

        # 測試接口 start
        $sUrl = "/api/users?include=info,account,gamePlatformUsers,vip,reward";
        $sMethod = "POST";

        # 設定header
        $this->aHeader["currency"] = "VND";
        # 傳送參數設定
        $aData = [
            "name" => 'test01',//,strtolower($faker->lastName),
            "email" => strtolower($faker->email),
            "password" => "123qwe",
            "password_confirmation" => "123qwe",
        ];
        
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        
        $response->assertStatus(201);
        # 測試接口 end
    }

    /**
     * 获取会员信息
     *
     * @return void
     */
    public function testInfo()
    {
        # 随几取一名会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();
        $this->UserLogin($oUser->name, "123qwe"); 
        

        # 測試接口 start
        $sUrl = "/api/user?include=info,account,gamePlatformUsers,vip,reward";
        $sMethod = "GET";
        # 傳送參數設定
        $aData = [];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
        # 測試接口 end
    }

    /**
     * 修改密码
     *
     * @return void
     */
    public function testPassword()
    {
        # 随几取一名会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();
        $this->UserLogin($oUser->name, "123qwe"); 
        
        # 測試接口 start
        $sUrl = "/api/user/password";
        $sMethod = "PATCH";
        # 傳送參數設定
        $aData = [
            "old_password" => "123qwe",
            "new_password" => "qwe123",
            "new_password_confirmation" => "qwe123",
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);

        # 密码改回来以便测试
        $aData = [
            "old_password" => "qwe123",
            "new_password" => "123qwe",
            "new_password_confirmation" => "123qwe",
        ];
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
        # 測試接口 end
    }

    /**
     * 找回密码 (暂时不测，会登入不了)
     *
     * @return void
     */
    public function Rest()
    {
        # 随几取一名会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();

        # 測試接口 start
        $sUrl = "/api/user/reset";
        $sMethod = "PATCH";
        # 傳送參數設定
        $aData = [
            "name" => $oUser->name,
            "email" => $oUser->info->email,
        ];

        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 

        $response->assertStatus(204);
        # 測試接口 end
    }

}
