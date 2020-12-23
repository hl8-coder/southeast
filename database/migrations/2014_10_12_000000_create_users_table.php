<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency')->index();
            $table->string('language');
            $table->string('name')->comment('会员名称')->index();
            $table->string('password')->comment('密码');
            $table->string('fund_password')->nullable()->comment('资金密码');
            $table->unsignedSmallInteger('vip_id')->nullable()->index();
            $table->unsignedSmallInteger('reward_id')->nullable()->index();
            $table->unsignedSmallInteger('risk_group_id')->nullable()->comment('风控分组id');
            $table->unsignedSmallInteger('payment_group_id')->nullable()->comment('支付分组id');
            $table->unsignedInteger('parent_id')->nullable()->comment('上级id');
            $table->string('parent_id_list', 2048)->default('')->comment('所有上级id');
            $table->string('parent_name')->default('')->comment('上级名称');
            $table->string('parent_name_list', 2048)->default('')->comment('所有上级名称');
            $table->boolean('is_agent')->default(false)->comment('是否是代理');
            $table->boolean('is_need_change_password')->default(false)->comment('是否需要强制修改密码');
            $table->unsignedTinyInteger('odds')->default(\App\Models\User::ODDS_MALAY)->comment('赔率类型');

            $table->string('referral_code')->comment('推荐代码')->unique();
            $table->string('referrer_code')->default('')->comment('推荐人代码');

            $table->string('affiliate_code')->nullable()->comment('代理推荐码')->unique();
            $table->string('affiliated_code')->nullable()->comment('上级代理推荐码')->index();

            $table->unsignedSmallInteger('security_question')->nullable()->comment('密保问题');
            $table->string('security_question_answer', 1024)->default('')->comment('密保问题');

            $table->unsignedInteger('notification_count')->default(0)->comment('未读消息数');

            $table->unsignedTinyInteger('status')->default(\App\Models\User::STATUS_ACTIVE)->comment('状态');
            $table->timestamps();

            $table->unique(['is_agent', 'name']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
