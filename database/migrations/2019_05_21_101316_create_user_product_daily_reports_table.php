<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserProductDailyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_product_daily_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('会员id')->index();
            $table->string('user_name')->comment('会员名称')->index();
            $table->string('platform_code')->nullable()->comment('平台code')->index();
            $table->string('product_code')->comment('产品code')->index();
            $table->date('date')->comment('所属日期');
            $table->decimal('effective_bet', 16, 6)->default(0)->comment('有效流水');
            $table->decimal('invalid_bet', 16, 6)->default(0)->comment('无效流水');
            $table->decimal('close_bonus_bet', 16, 6)->default(0)->comment('关闭红利流水');
            $table->decimal('close_cash_back_bet', 16, 6)->default(0)->comment('关闭赎返流水');
            $table->decimal('close_adjustment_bet', 16, 6)->default(0)->comment('关闭调整流水');
            $table->decimal('close_deposit_bet', 16, 6)->default(0)->comment('关闭充值流水');
            $table->decimal('calculate_rebate_bet', 16, 6)->default(0)->comment('计算返点流水');
            $table->decimal('calculate_reward_bet', 16, 6)->default(0)->comment('计算积分流水');
            $table->decimal('effective_profit', 16, 6)->default(0)->comment('会员有效盈亏');
            $table->decimal('invalid_profit', 16, 6)->default(0)->comment('会员无效盈亏');
            $table->decimal('calculate_cash_back_profit', 16, 6)->default(0)->comment('计算赎返盈亏');
            $table->decimal('rebate', 16, 6)->default(0)->comment('返点');
            $table->decimal('bonus', 16, 6)->default(0)->comment('红利');
            $table->decimal('cash_back', 16, 6)->default(0)->comment('赎返');
            $table->decimal('proxy_bonus', 16, 6)->default(0)->comment('代理红利');
            $table->timestamps();

            $table->unique(['user_id', 'product_code', 'date'], 'user_product_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_product_daily_reports');
    }
}
