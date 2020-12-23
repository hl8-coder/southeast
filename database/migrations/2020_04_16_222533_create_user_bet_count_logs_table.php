<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBetCountLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bet_count_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('unique_id')->unique();
            $table->unsignedInteger('user_id')->comment('会员id')->index();
            $table->string('product_code')->comment('产品code')->index();
            $table->date('date')->comment('所属日期');
            $table->decimal('open_bet', 16, 6)->default(0);
            $table->unsignedInteger('bet_num')->default(0);
            $table->decimal('stake', 16, 6)->default(0);
            $table->decimal('profit', 16, 6)->default(0);
            $table->decimal('effective_bet', 16, 6)->default(0);
            $table->decimal('effective_profit', 16, 6)->default(0);
            $table->decimal('calculate_rebate_bet', 16, 6)->default(0);
            $table->decimal('calculate_cash_back_profit', 16, 6)->default(0);
            $table->decimal('close_bonus_bet', 16, 6)->default(0);
            $table->decimal('close_cash_back_bet', 16, 6)->default(0);
            $table->decimal('close_adjustment_bet', 16, 6)->default(0);
            $table->decimal('close_deposit_bet', 16, 6)->default(0);
            $table->decimal('calculate_reward_bet', 16, 6)->default(0);
            $table->decimal('rebate', 16, 6)->default(0);
            $table->decimal('bonus', 16, 6)->default(0);
            $table->decimal('cash_back', 16, 6)->default(0);
            $table->decimal('proxy_bonus', 16, 6)->default(0);
            $table->unsignedTinyInteger('status')->default(1);
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
        Schema::dropIfExists('user_bet_count_logs');
    }
}
