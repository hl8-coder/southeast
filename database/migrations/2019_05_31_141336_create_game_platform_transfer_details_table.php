<?php

use App\Models\GamePlatformTransferDetail;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlatformTransferDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_platform_transfer_details', function (Blueprint $table) {
            $table->increments('id');

            $table->unsignedInteger('user_id')->index();
            $table->string('user_name')->index();
            $table->string('user_ip')->default('');
            $table->string('platform_code')->index();
            $table->string('order_no')->nullable()->comment('订单编号')->unique();
            $table->string('platform_order_no')->nullable()->comment('第三方平台订单号')->unique();
            $table->unsignedInteger('user_bonus_prize_id')->nullable()->comment('红利奖励id')->index();

            $table->boolean('is_income')->default(true)->comment('是否入账');
            $table->string('user_currency')->default('')->comment('会员币别');
            $table->string('platform_currency')->default('')->comment('平台币别');
            $table->string('from')->default('')->comment('转出钱包');
            $table->string('to')->default('')->comment('转入钱包');
            $table->decimal('bonus_amount', 16, 6)->default(0)->comment('红利金额');
            $table->decimal('amount', 16, 6)->default(0)->comment('转换前金额');
            $table->decimal('conversion_amount', 16, 6)->default(0)->comment('转换后金额');
            $table->decimal('from_before_balance', 16, 6)->default(0)->comment('出帐钱包帐变前总金额');
            $table->decimal('from_after_balance', 16, 6)->default(0)->comment('出帐钱包帐变后总金额');
            $table->decimal('to_before_balance', 16, 6)->default(0)->comment('入帐钱包帐变前总金额');
            $table->decimal('to_after_balance', 16, 6)->default(0)->comment('入帐钱包帐变后总金额');

            $table->unsignedSmallInteger('check_times')->default(0)->comment('检查状态次数');
            $table->string('bet_order_id')->default('')->comment('投注订单号');
            $table->string('admin_name')->nullable()->comment('审核管理员');
            $table->string('remark', 1024)->default('')->comment('备注');
            $table->string('sys_remark', 1024)->default('')->comment('备注');

            $table->unsignedTinyInteger('status')->default(GamePlatformTransferDetail::STATUS_CREATED);
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
        Schema::dropIfExists('game_platform_transfer_details');
    }
}
