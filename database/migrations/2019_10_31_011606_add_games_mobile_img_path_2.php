<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddGamesMobileImgPath2 extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->string('mobile_img_path_2', 255)->default('')->comment('手机图片2号位置');
            $table->boolean('is_soon')->default(false)->comment('是否即将发布');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('games', function (Blueprint $table) {
            $table->dropColumn('mobile_img_path_2');
            $table->dropColumn('is_soon');
        });
    }
}
