<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotions', function (Blueprint $table) {
            $table->increments('id');
            $table->json('currencies');
            $table->json('languages');
            $table->string('promotion_type_code')->comment('优惠类型code')->index();
            $table->json('codes')->nullable()->comment('关联优惠code');
            $table->string('backstage_title', 1024)->comment('后台显示标题');
            $table->dateTime('display_start_at')->nullable()->comment('开始时间');
            $table->dateTime('display_end_at')->nullable()->comment('结束时间');
            $table->string('web_img_path')->default('')->comment('pc显示图片');
            $table->string('web_content_img_path')->default('')->comment('pc内容显示图片');
            $table->string('mobile_img_path')->default('')->comment('mobile显示图片');
            $table->string('mobile_content_img_path')->default('')->comment('mobile内容显示图片');
            $table->boolean('status')->default(false);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->string('admin_name')->default('');
            $table->softDeletes();
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
        Schema::dropIfExists('promotions');
    }
}
