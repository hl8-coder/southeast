<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banners', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency');
            $table->string('code');
            $table->unsignedTinyInteger('show_type')->comment('显示类型');
            $table->unsignedTinyInteger('position')->comment('位置');
            $table->unsignedTinyInteger('target_type')->comment('链接打开方式');
            $table->json('languages')->comment('多语言内容');
            $table->dateTime('display_start_at')->nullable()->comment('开始时间');
            $table->dateTime('display_end_at')->nullable()->comment('结束时间');
            $table->string('web_img_path')->default('')->comment('pc显示图片');
            $table->string('mobile_img_path')->default('')->comment('mobile显示图片');
            $table->string('web_link_url', 1024)->default('')->comment('pc跳转链接');
            $table->string('mobile_link_url', 1024)->default('')->comment('mobile跳转链接');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('status')->default(false);
            $table->boolean('is_agent')->default(false);
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
        Schema::dropIfExists('banners');
    }
}
