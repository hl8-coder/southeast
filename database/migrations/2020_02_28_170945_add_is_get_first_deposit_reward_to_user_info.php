<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsGetFirstDepositRewardToUserInfo extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_info', function (Blueprint $table) {
            $table->boolean('is_get_first_deposit_reward')->default(false)->after('web_url')->comment('主要针对泰迁移会员  判断该用户是否领取过首存优惠');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_info', function (Blueprint $table) {
            $table->dropColumn('is_get_first_deposit_reward');
        });
    }
}
