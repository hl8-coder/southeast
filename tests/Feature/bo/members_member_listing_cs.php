<?php

namespace Tests\Feature\bo;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Feature\Traits\Authorization;
use App\Models\User;

class members_member_listing_cs extends TestCase
{
    use Authorization;


    protected $sMenuCode = "members_member_listing_cs";

    /**
     * 測試更改第三方錢包狀態
     *
     * @return void
     */
    public function testGamePlatformUserBalanceStatus()
    {
        # 功能代碼
        $sActionName = "backstage.game_platform_user.balance_status";

        # 工廠建立會員
        $oUser = factory(\App\Models\User::class)->create();
        # 子錢包
        $oGamePlatformUser = $oUser->gamePlatformUsers->first();

        # 登入取得授權
        $this->Adminlogin($this->sLoginName, $this->sLoginPwd);

        # 取得API資訊
        $sUrl = $this->aAction[$sActionName]["url"];
        $sMethod = $this->aAction[$sActionName]["method"];
        $sUrl = str_replace("{game_platform_user}", $oGamePlatformUser->id, $sUrl);

        # 傳送參數設定
        $aData = ["status" => !$oGamePlatformUser->status];

        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);

    }


    /**
     * 測試更改密碼
     *
     * @return void
     */
    public function testUsersResetPassword()
    {
        # 功能代碼
        $sActionName = "backstage.users.reset_password";

        # 工廠建立會員
        $oUser = factory(\App\Models\User::class)->create();

        # 登入取得授權
        $this->Adminlogin($this->sLoginName, $this->sLoginPwd);

        # 取得API資訊
        $sUrl = $this->aAction[$sActionName]["url"];
        $sMethod = $this->aAction[$sActionName]["method"];
        $sUrl = str_replace("{user}", $oUser->id, $sUrl);

        ##### 手動更新 需設定密碼 #####

        # 傳送參數設定
        $aData = ["type" => "manual", "new_password" => "qwe123"];

        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(204);

        ##### 自動更新 #####

        # 傳送參數設定
        $aData = ["type" => "auto"];

        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(204);
    }

    

    /**
     * 測試更改會員狀態
     *
     * @return void
     */
    public function testUsersUpdateStatus()
    {
        # 功能代碼
        $sActionName = "backstage.users.update_status";

        # 工廠建立會員
        $oUser = factory(\App\Models\User::class)->create();

        # 登入取得授權
        $this->Adminlogin($this->sLoginName, $this->sLoginPwd);

        # 取得API資訊
        $sUrl = env("APP_URL", "/") . $this->aAction[$sActionName]["url"];
        $sMethod = $this->aAction[$sActionName]["method"];
        $sUrl = str_replace("{user}", $oUser->id, $sUrl);

        # 傳送參數設定
        $aData = ["status" => User::STATUS_BLOCKED, "remark" => "test"];

        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData); 
        $response->assertStatus(200);
    }

    /**
     * 測試取得列表
     *
     * @return void
     */
    public function testUsersIndex()
    {
        # 功能代碼
        $sActionName = "users.index";

        # 登入取得授權
        $this->Adminlogin($this->sLoginName, $this->sLoginPwd);

        # 取得API資訊
        $sUrl = env("APP_URL", "/") . $this->aAction[$sActionName]["url"];
        $sMethod = $this->aAction[$sActionName]["method"];
        # 測試接口
        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl);  
        $response->assertStatus(200);        
    }
}
