<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdvertisementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('advertisements', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency')->index()->comment('币别');
            $table->string('web_img_path')->default('')->comment('WEB端图片地址');
            $table->string('mobile_img_path')->default('')->comment('移动端图片地址');
            $table->string('login_img_path')->default('')->comment('登录页图片地址');
            $table->string('description', 1024)->default('')->comment('图片描述');
            $table->string('img_link_url')->default('')->comment('图片跳转地址');
            $table->string('alone_link_url')->default('')->comment('独立跳转地址');
            $table->unsignedTinyInteger('target_type')->default(1)->comment('链接打开方式');
            $table->unsignedTinyInteger('show_type')->default(1)->comment('显示类型');
            $table->unsignedSmallInteger('sort')->default(0);
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
        Schema::dropIfExists('advertisements');
    }
}
