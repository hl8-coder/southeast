<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmResourceCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_resource_call_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('crm_resource_id')->index()->comment('crm resource id');
            $table->integer('admin_id')->index()->comment('admin id');
            $table->string('channel')->comment('call channel');
            $table->integer('call_status')->index()->comment('联络状态');
            $table->string('purpose')->nullable()->comment('联络目的');
            $table->string('prefer_product')->nullable()->comment('偏爱的产品');
            $table->string('prefer_bank')->nullable()->comment('偏爱的银行');
            $table->string('source')->nullable()->comment('顾客来源');
            $table->string('reason')->nullable()->comment('原因');
            $table->text('comment')->nullable()->comment('备注');

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
        Schema::dropIfExists('crm_resource_call_logs');
    }
}
