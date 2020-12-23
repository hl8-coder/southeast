<?php

use App\Models\UserInfo;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_info', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->unique();
            $table->boolean('is_agent')->default(false)->comment('是否代理[冗余字段，便于判断]');
            $table->string('full_name')->default('')->comment('真实姓名');
            $table->string('gender')->default('')->comment('性别');
            $table->string('address', 512)->default('');
            $table->string('email')->nullable()->comment('邮箱')->index();
            $table->dateTime('email_verified_at')->nullable();
            $table->string('country_code')->default('')->comment('电话国际代码');
            $table->string('phone')->nullable()->comment('电话号码')->index();
            $table->string('other_contact')->nullable()->comment('其他联系方式');
            $table->dateTime('phone_verified_at')->nullable();
            $table->date('birth_at')->nullable()->comment('生日');
            $table->string('register_url')->default('')->comment('注册url');
            $table->string('register_ip')->default('')->comment('注册IP');
            $table->string('last_login_ip')->default('')->comment('最后登录IP');
            $table->dateTime('last_login_at')->nullable()->comment('最后登录时间');
            $table->dateTime('profile_verified_at')->nullable()->comment('验证会员基本信息时间');
            $table->dateTime('bank_account_verified_at')->nullable()->comment('是否已验证银行卡时间');
            $table->dateTime('claimed_verify_prize_at')->nullable()->comment('领取资料验证奖励时间');
            $table->string('old_token', 1024)->default('')->comment('当前使用token');
            $table->timestamps();
            $table->unique(['is_agent', 'email']);
            $table->unique(['is_agent', 'phone']);
        });
    }
    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_info');
    }
}
