<?php

use App\Models\User;
use App\Models\UserBankAccount;
use App\Repositories\UserRepository;
use Carbon\Carbon;
use App\Models\GameBetDetail;
use App\Models\Affiliate;
use Faker\Generator as Faker;
use Illuminate\Support\Str;

$factory->define(App\Models\User::class, function (Faker $faker) {
    return [
        "name" => Str::random(mt_rand(6, 16)),
        "currency" => \App\Models\Currency::inRandomOrder()->first()->code,
        "language" => \App\Models\Language::inRandomOrder()->first()->code,
        "password" => bcrypt("123qwe"),
        "referral_code" => Str::random(10),
        "payment_group_id" => 1, //暂定1(目前没相关资料)
    ];
});


$factory->afterCreating(App\Models\User::class, function ($user, $faker) {
    if ($user->parentUser) {
        $user->affiliated_code = $user->parentUser->affiliate_code;
        $user->save();
    }
    if ($user->is_agent == true) {
        $user->status         = User::STATUS_PENDING;
        $user->affiliate_code = UserRepository::findAvailableAffiliateCode();
        $user->save();
    }
	# 建立會員資訊
    $user->info = factory(App\Models\UserInfo::class)->create(["user_id" => $user->id]);

    # 建立會員帳戶
    $user->account = factory(App\Models\UserAccount::class)->create(["user_id" => $user->id]);

    # 代理
    if($user->is_agent == true)
    {
        $code = "";
        if($user->parent_id)
        {
            $code = $user->parentUser->affiliate_code;
        }
        $affiliate = factory(App\Models\Affiliate::class)->create([
            "user_id" => $user->id,
            'code' => $user->affiliate_code,
            'is_fund_open' => true,
            'refer_by_code' => $code,
            'cs_status' => Affiliate::CS_STATUS_APPROVED,
            'cs_status_last_updated_at'  => Carbon::now(),
        ]);
    }
    # 會員
    else {
        $user->gamePlatformUsers = [];
        
        # 取得遊戲平台
        $oGamePlatforms = \App\Models\GamePlatform::get();
        foreach ($oGamePlatforms as $oGamePlatform) 
        {
            $randUserBetMonth = 0; //0 这个月, 1上个月, 2前个月
            $randUserBetMonth = rand(0,2);

        	# 建立子錢包
            factory(\App\Models\GamePlatformUser::class)->create([
                "user_id"=> $user->id, 
                "name" =>  strtolower($oGamePlatform->code) . "_" . $user->name,
                "platform_code" => $oGamePlatform->code
            ]);

            //部分模拟投注资料, 会在帐号建立时间之前, 正常现象
            factory(\App\Models\GameBetDetail::class,1)->create([
                "user_id"=> $user->id,
                "platform_code" => $oGamePlatform->code,
                "user_name"=> $user->name,
                'finished_at' => Carbon::now()->firstOfMonth()->subMonths($randUserBetMonth)->firstOfMonth(),
                'created_at'  => Carbon::now()->firstOfMonth()->subMonths($randUserBetMonth)->firstOfMonth(),
            ]);
        }
        $user->gamePlatformUsers = \App\Models\GamePlatformUser::where(["user_id" => $user->id])->get();

    }

    # 建立会员银行卡
    factory(App\Models\UserBankAccount::class)->create(["user_id" => $user->id]);

    $user->bankAccounts = \App\Models\UserBankAccount::where(["user_id" => $user->id])->get();

    $randAmount = rand(10,10000);
    # 建立充值记录
    factory(App\Models\Deposit::class)
            ->create([
                "user_id" => $user->id,
                "currency" => $user->currency,
                "language" => $user->language,
                "amount"  => $randAmount,
                "receive_amount" => $randAmount,
                "arrival_amount" => $randAmount,
                "payment_platform_id" => rand(1,8),
                "status" => rand(1,4),
            ]);

    # 建立会员帐户CRM
    factory(App\Models\CrmOrder::class)->create(['user_id' => $user->id]);

});