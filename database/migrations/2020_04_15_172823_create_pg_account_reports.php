<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePgAccountReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pg_account_reports', function (Blueprint $table) {
            $table->increments('id')->comment('第三方支付通道日统计报表');
            $table->string('payment_platform_code')->comment('第三方支付通道code')->index();
            $table->decimal('start_balance', 16, 6)->default(0)->comment('开始余额');
            $table->decimal('end_balance', 16, 6)->default(0)->comment('结束余额');
            $table->decimal('deposit', 16, 6)->default(0)->comment('用户存款金额 扣除手续费的实际到账金额');
            $table->decimal('deposit_fee', 16, 6)->default(0)->comment('用户存款，第三方收取的存款手续费金额');
            $table->decimal('withdraw', 16, 6)->default(0)->comment('公司从第三方提现,提现总额,扣除收手续费的实际到账金额');
            $table->decimal('withdraw_fee', 16, 6)->default(0)->comment('公司从第三方提现,第三方收取的提现手续费金额');
            $table->date('date')->comment('所属日期');
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
        Schema::dropIfExists('pg_account_reports');
    }
}
