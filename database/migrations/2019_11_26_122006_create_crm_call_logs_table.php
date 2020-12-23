<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmCallLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::dropIfExists('crm_call_logs');
        Schema::create('crm_call_logs', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->bigInteger('crm_order_id')->comment('crm_orders id');
            $table->integer('admin_id')->comment('admin id');
            $table->string('channel')->comment('call channel');
            $table->unsignedTinyInteger('call_status')->default(0)->comment('联络状态');
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
        Schema::dropIfExists('crm_call_logs');
    }
}
