<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_platforms', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('code')->comment('唯一码')->unique();
            $table->text('icon')->nullable()->comment('图标');

            # 账户相关 start
            $table->string('request_url')->default('')->comment('api请求地址');
            $table->string('report_request_url')->default('')->comment('报表请求地址');
            $table->string('launcher_request_url')->default('')->comment('游戏启动地址');
            $table->string('rsa_our_private_key', 2048)->default('')->comment('RSA我方私钥');
            $table->string('rsa_our_public_key', 2048)->default('')->comment('RSA我方公钥');
            $table->string('rsa_public_key', 2048)->default('')->comment('RSA平台公钥');
            $table->json('account')->nullable()->comment('账户相关');
            $table->json('exchange_currencies')->nullable()->comment('币别转换');
            # 账户相关 end

            # 更新游戏列表 start
            $table->boolean('is_update_list')->default(false)->comment('是否更新游戏列表');
            $table->unsignedSmallInteger('update_interval')->default(7)->comment('更新列表间隔时间(天)');
            $table->dateTime('last_updated_at')->nullable()->comment('最后更新时间');
            # 更新游戏列表 end

            # 拉取报表设置 start
            $table->unsignedSmallInteger('interval')->default(1)->comment('间隔时间(分钟)');
            $table->unsignedSmallInteger('delay')->default(0)->comment('延迟时间(分钟)');
            $table->unsignedSmallInteger('offset')->default(0)->comment('偏移时间(分钟)');
            $table->unsignedSmallInteger('limit')->default(1)->comment('每分钟现在拉取几次');
            # 拉取报表设置 ent

            $table->boolean('is_auto_transfer')->default(false)->comment('是否支持自动转帐');
            $table->text('remark')->nullable()->comment('抽成资讯');
            $table->unsignedInteger('sort')->default(0);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('game_platforms');
    }
}
