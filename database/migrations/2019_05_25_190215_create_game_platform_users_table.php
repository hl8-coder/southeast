<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlatformUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_platform_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_code')->comment('第三方平台code')->index();
            $table->unsignedInteger('user_id')->comment('会员id')->index();
            $table->string('user_name')->default('')->comment('会员名称');
            $table->string('currency')->default('')->comment('第三方币别');
            $table->string('name')->comment('登入第三方帐号');
            $table->string('password')->comment('登入第三方密码');
            $table->decimal('balance', 14, 4)->default(0)->comment('第三方余额');
            $table->string('platform_user_id')->nullable()->comment('第三方平台id');
            $table->dateTime('platform_created_at')->nullable()->comment('第三方注册成功时间');
            $table->boolean('balance_status')->default(true)->comment('第三方钱包状态');
            $table->unsignedTinyInteger('status')->default(1)->comment('第三方会员状态');

            $table->unique(['user_id', 'platform_code'], 'user_game_platform_unique');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_platform_users');
    }
}
