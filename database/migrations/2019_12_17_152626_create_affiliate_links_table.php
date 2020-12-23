<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateLinksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_links', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->comment('链接的类型');
            $table->integer('platform')->comment('链接跳转的平台');
            $table->integer('sort')->default(0)->comment('排序');
            $table->string('link')->comment('链接');
            $table->string('admin_name')->default('')->comment('修改人');
            $table->json('currencies')->comment('币别');
            $table->json('languages')->comment('语言');
            $table->boolean('status')->default(true)->comment('状态');
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
        Schema::dropIfExists('affiliate_links');
    }
}
