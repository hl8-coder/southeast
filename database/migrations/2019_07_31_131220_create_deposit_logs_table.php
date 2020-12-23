<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDepositLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('deposit_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('deposit_id')->index()->comment('充值id');
            $table->string('admin_name')->comment('管理员名称');
            $table->unsignedTinyInteger('type')->index()->comment('类型');
            $table->unsignedInteger('bank_transaction_id')->nullable()->comment('银行充值记录id');
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
        Schema::dropIfExists('deposit_logs');
    }
}
