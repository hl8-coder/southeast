<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRebatePrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_rebate_prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name')->default('');
            $table->unsignedSmallInteger('risk_group_id')->nullable()->index();
            $table->unsignedSmallInteger('vip_id')->nullable()->index();
            $table->string('rebate_code')->index();
            $table->unsignedInteger('report_id')->comment('来源数据的报表id');
            $table->decimal('effective_bet', 16, 6)->default(0)->comment('有效流水');
            $table->decimal('close_bonus_bet', 16, 6)->default(0)->comment('关闭红利的有效流水');
            $table->decimal('calculate_rebate_bet', 16, 6)->default(0)->comment('计算返点的有效流水');
            $table->string('currency');
            $table->string('product_code')->comment('产品code')->index();
            $table->decimal('multipiler', 6, 2)->default(0)->comment('计算数值');
            $table->decimal('prize', 16, 6)->default(0)->comment('奖励金额');
            $table->boolean('is_max_prize')->default(false)->comment('是否是上限奖励');
            $table->boolean('is_manual_send')->default(false)->comment('是否需要手动派发');
            $table->string('date')->comment('归属日期');
            $table->string('marketing_admin_name')->nullable()->comment('marketing派发管理员');
            $table->dateTime('marketing_sent_at')->nullable()->comment('marketing派发时间');
            $table->string('payment_admin_name')->nullable()->comment('payment派发管理员');
            $table->dateTime('payment_sent_at')->nullable()->comment('payment派发时间');
            $table->unsignedTinyInteger('status')->default(\App\Models\UserRebatePrize::STATUS_CREATED);
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
        Schema::dropIfExists('user_rebate_prizes');
    }
}
