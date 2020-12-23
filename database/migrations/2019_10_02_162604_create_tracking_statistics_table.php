<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTrackingStatisticsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('tracking_statistics', function (Blueprint $table) {
            $table->increments('id');
            $table->string('tracking_name')->unique()->default('')->comment('名称');
            $table->integer('user_id')->comment('会员或代理ID');
            $table->string('user_name')->comment('会员或代理名');
            $table->dateTime('date')->nullable()->comment('当天第一次被点击事件');
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
        Schema::dropIfExists('tracking_statistics');
    }
}
