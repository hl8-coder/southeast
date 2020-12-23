<?php

namespace Tests\Feature\Traits;

trait Authorization
{
    protected $sLoginName = "left";

    protected $sLoginPwd = "123qwe";

    protected $sUserLoginName = "";

    protected $sUserLoginPwd = "";

    protected $aHeader = [];

    protected $aMenu = [];

    protected $aAction = [];   

    /**
     * 管理員登入
     *
     * @return void
     */
    private function AdminloginNoMenu($name, $pasword) {

        # 登入取得Authorization
        $response = $this->json('POST', "/api/backstage/authorizations?name=$name&password=$pasword");

        // $response->assertStatus(201);    

        $result = $response->json();

        $this->aHeader["Authorization"] = $result["token_type"] . " " . $result["access_token"];
    }



    /**
     * 管理員登入
     *
     * @return void
     */
    private function Adminlogin($name, $pasword) {

        # 登入取得Authorization
        $response = $this->json('POST', "/api/backstage/authorizations?name=$name&password=$pasword");

        // $response->assertStatus(201);    

        $result = $response->json();

        $this->aHeader["Authorization"] = $result["token_type"] . " " . $result["access_token"];

        # 取得頁面需要測試的API
        $oMenu = \App\Models\Menu::where("code", $this->sMenuCode)->first();

        $oActions = \App\Models\Action::where("menu_id", $oMenu->id)->get();

        foreach ($oActions as $item) {
            $this->aAction[$item->action] = ["method"=>$item->method, "url"=>$item->url];
        }
    }

    /**
     * 管理員登入
     *
     * @return void
     */
    private function UserLogin($name, $pasword) {

        $sUrl = "/api/authorizations";
        $sMethod = "POST";
        # 傳送參數設定
        # 取得API資訊
        $aData = ["name" => $name, "password" => $pasword, "device" => \App\Models\User::DEVICE_PC];
        # 測試接口
        $response = $this->json($sMethod, $sUrl, $aData, ['device' => 1]);
        $result = $response->json();

        $this->aHeader["Authorization"] = $result['meta']["token_type"] . " " . $result['meta']["access_token"];
    }
}