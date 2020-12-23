<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('games', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_code')->comment('游戏平台code');
            $table->string('product_code')->comment('产品code')->index();
            $table->unsignedTinyInteger('type')->comment('游戏类别')->index();
            $table->string('code')->nullable()->comment('第三方游戏代码');
            $table->json('languages')->nullable()->comment('语言');
            $table->json('currencies')->comment('币别');
            $table->json('devices')->comment('装置');
            $table->string('web_img_path')->default('')->comment('web端图片');
            $table->string('mobile_img_path')->default('')->comment('mobile端图片');
            $table->boolean('is_hot')->default(false)->comment('是否为热门游戏');
            $table->boolean('is_new')->default(false)->comment('是否为新游戏');
            $table->boolean('is_iframe')->default(true)->comment('是否是 iframe 打开游戏');
            $table->boolean('is_close_bonus')->default(true)->comment('是否可用于关闭红利');
            $table->boolean('is_close_cash_back')->default(true)->comment('是否可用于关闭赎返');
            $table->boolean('is_close_adjustment')->default(true)->comment('是否可用于关闭调整');
            $table->boolean('is_calculate_reward')->default(true)->comment('是否可用于计算积分');
            $table->boolean('is_calculate_cash_back')->default(true)->comment('是否可用于计算赎返');
            $table->boolean('is_calculate_rebate')->default(true)->comment('是否可用于计算返点');
            $table->string('remark')->default('')->comment('备注');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('status')->default(true);
            $table->timestamps();
            $table->unique(['platform_code', 'code'], 'platform_code_unique');
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('games');
    }
}
