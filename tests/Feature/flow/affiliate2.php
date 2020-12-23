<?php

namespace Tests\Feature\flow;

use Carbon\Carbon;
use Tests\TestCase;
use Tests\Feature\Traits\Authorization;
use Faker\Factory as Faker;
use Illuminate\Support\Str;
use App\Models\GameBetDetail;

class affiliate2 extends TestCase
{ //function start

    use Authorization;

    /**
     * 代理注册
     *
     * @return void
     */
    public function testRegister()
    {

        $faker = Faker::create();

        # 測試接口 - 有一層上級代理 start
        $sUrl = "/api/users?include=info,account,gamePlatformUsers,vip,reward";
        $sMethod = "POST";

        $date = Carbon::now()->format('dHis');
        $affiliate = \App\Models\Affiliate::first();

        # 設定header
        $this->aHeader["currency"] = "VND";
        # 傳送參數設定
        $aData = [
            "name" => 'test2'.$date,//,strtolower($faker->lastName),
            "email" => strtolower($faker->email),
            "password" => "123qwe",
            "password_confirmation" => "123qwe",
            "country_code" => "63",
            "phone" => "092".$date,
            "birth_at" => "2000-01-01",
            "full_name" => 'test2'.$date,
//            "email" => $date."2@mail.com",
            "is_agent" => true,
            "affiliate_id" => $affiliate->id,
            "register_url" => "www.". rand(0,100)."com",
        ];

        $response = $this->withHeaders($this->aHeader)->json($sMethod, $sUrl, $aData);
        $response->assertStatus(201);
        # 測試接口 - 有一層上級代理 end

    }

} //function end
