<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserBonusPrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_bonus_prizes', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name')->index();
            $table->json('set')->comment('奖励设定');
            $table->unsignedInteger('bonus_id')->index();
            $table->string('bonus_code');
            $table->unsignedInteger('bonus_group_id')->comment('红利组别id');
            $table->unsignedInteger('remark_id')->nullable()->index();
            $table->string('currency');
            $table->unsignedTinyInteger('category')->comment('新旧红利');
            $table->string('product_code')->comment('产品code')->index();
            $table->decimal('deposit_amount', 16, 6)->default(0)->comment('充值金额');
            $table->decimal('prize', 16, 6)->default(0)->comment('奖励金额');
            $table->boolean('is_max_prize')->default(false)->comment('是否是上限奖励');
            $table->string('date')->default('')->comment('归属日期');
            $table->unsignedTinyInteger('status')->default(\App\Models\UserBonusPrize::STATUS_CREATED);
            $table->string('remark', 1024)->default('');

            # 流水要求
            $table->boolean('is_turnover_closed')->default(false)->comment('流水限制是否关闭');
            $table->decimal('turnover_closed_value', 16, 6)->default(0)->comment('所需流水总数');
            $table->decimal('turnover_current_value', 16, 6)->default(0)->comment('当前流水数值');
            $table->dateTime('turnover_closed_at')->nullable();
            $table->string('turnover_closed_admin_name')->nullable();
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
        Schema::dropIfExists('user_bonus_prizes');
    }
}
