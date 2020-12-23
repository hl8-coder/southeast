<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUrlsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('urls', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type');
            $table->string('address')->comment('链接地址');
            $table->boolean('status')->comment('状态');
            $table->string('remark')->nullable()->comment('备注');
            $table->string('update_by')->nullable()->comment('修改者');
            $table->integer('device')->comment('设备');
            $table->integer('platform')->comment('平台');
            $table->json('currencies');
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
        Schema::dropIfExists('urls');
    }
}
