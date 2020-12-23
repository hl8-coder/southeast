<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKpiReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('kpi_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->date('date')->comment('统计日期');
            $table->string('currency')->comment('币别');
            $table->decimal('total_deposit', 16, 6)->nullable()->default(0)->comment('当天充值成功总数额');
            $table->decimal('total_withdrawal', 16, 6)->nullable()->default(0)->comment('当天成功体现总额');
            $table->decimal('net_profit', 16, 6)->nullable()->default(0)->comment('当天净利润');
            $table->integer('total_new_members')->nullable()->default(0)->comment('当天新注册会员数量');
            $table->integer('total_active_members')->nullable()->default(0)->comment('总活跃会员');
            $table->integer('total_login_members')->nullable()->default(0)->comment('当天总的登陆人数');
            $table->integer('total_deposit_members')->nullable()->default(0)->comment('当天有成功充值总人数');
            $table->integer('total_withdrawal_members')->nullable()->default(0)->comment('当天总提现成功人数');
            $table->integer('total_count_deposit')->nullable()->default(0)->comment('当天总的充值成功笔数');
            $table->integer('total_count_withdrawal')->nullable()->default(0)->comment('当天总的提现成功笔数');
            $table->decimal('total_turnover', 16, 6)->nullable()->default(0)->comment('当天总营业额');
            $table->decimal('total_payout', 16, 6)->nullable()->default(0)->comment('当天总支出');
            $table->decimal('total_rebate', 16, 6)->nullable()->default(0)->comment('当天总调额返点额');
            $table->decimal('total_adjustment', 16, 6)->nullable()->default(0)->comment('当天除特定类型外的总调额');
            $table->decimal('total_promotion_cost', 16, 6)->nullable()->default(0)->comment('当天调额中特定类型的总额');
            $table->decimal('total_promotion_cost_by_code', 16, 6)->nullable()->default(0)->comment('当天调额中特定类型的总额');
            $table->decimal('total_bank_fee', 16, 6)->nullable()->default(0)->comment('当天总的银行手续费，包括充值和提现');
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
        Schema::dropIfExists('kpi_reports');
    }
}
