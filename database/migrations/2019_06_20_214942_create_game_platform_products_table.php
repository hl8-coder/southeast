<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlatformProductsTable extends Migration
{
    /**
     * Run the migrations
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_platform_products', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_code')->comment('平台code')->index();
            $table->string('code')->unique();
            $table->json('currencies')->comment('币别');
            $table->json('languages')->comment('语言');
            $table->json('devices')->comment('装置');
            $table->unsignedTinyInteger('type')->index()->comment('游戏类型');
            $table->string('one_web_img_path')->default('')->comment('web端图片1');
            $table->string('two_web_img_path')->default('')->comment('web端图片2');
            $table->string('mobile_img_path')->default('')->comment('mobile端图片');
            $table->boolean('is_close_bonus')->default(true)->comment('是否可用于关闭红利');
            $table->boolean('is_close_cash_back')->default(true)->comment('是否可用于关闭赎返');
            $table->boolean('is_close_adjustment')->default(true)->comment('是否可用于关闭调整');
            $table->boolean('is_calculate_reward')->default(true)->comment('是否可用于计算积分');
            $table->boolean('is_calculate_cash_back')->default(true)->comment('是否可用于计算积分');
            $table->boolean('is_calculate_rebate')->default(true)->comment('是否可用于计算返点');
            $table->boolean('status')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
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
        Schema::dropIfExists('game_platform_products');
    }
}
