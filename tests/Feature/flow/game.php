<?php

namespace Tests\Feature\flow;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;

class game extends TestCase
{
    use Authorization;

    /**
     * A basic feature test example.
     *
     * @return void
     */
    public function testExample()
    {
        # 会员
        $oUser = \App\Models\User::orderBy("id", "desc")->first();

        # step 2. 会员登入
        $this->UserLogin($oUser->name, "123qwe");    


        # step 3. 遊戲列表
        $sUrl = "/api/games";
        $sMethod = "GET";

        # 傳送參數設定
        $this->aHeader["currency"] = "VND";
        $aData = [];
        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        

        $sUrl = "/api/games/9/login";
        $sMethod = "POST";

        # 傳送參數設定
        $this->aHeader["currency"] = "VND";
        $this->aHeader["device"] = "1";
        $aData = [];
        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData);

        $response->assertStatus(200);
    }
}
