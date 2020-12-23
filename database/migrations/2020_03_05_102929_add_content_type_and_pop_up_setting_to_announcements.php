<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddContentTypeAndPopUpSettingToAnnouncements extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->unsignedTinyInteger('content_type')->default(1)->comment('内容类型,默认1 表示文字  2表示图片');
            $table->json('pop_up_setting')->nullable()->comment('弹窗相关设置,弹窗频率 弹窗延时');
            $table->json('access_pop_mobile_urls')->nullable()->comment('移动端允许弹窗的地址');
            $table->json('access_pop_pc_urls')->nullable()->comment('pc允许弹窗的地址');
            $table->boolean('is_login_pop_up')->default(False)->comment('是否登录触发的弹窗');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn(['content_type','pop_up_setting','access_pop_mobile_urls','access_pop_pc_urls','is_login_pop_up']);
        });
    }
}
