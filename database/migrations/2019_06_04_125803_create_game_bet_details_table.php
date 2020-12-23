<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGameBetDetailsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_bet_details', function (Blueprint $table) {

            $table->increments('id');
            $table->string('platform_code')->comment('游戏平台code')->index();
            $table->string('product_code')->comment('游戏产品code')->index();
            $table->string('platform_currency')->comment('平台币别');
            $table->string('order_id')->comment('游戏平台订单编号');
            $table->unsignedTinyInteger('game_type')->nullable()->comment('游戏类型');
            $table->string('game_code')->nullable()->comment('游戏编码');
            $table->string('game_name')->default('')->comment('游戏名称');
            $table->unsignedInteger('user_id')->comment('会员id');
            $table->string('user_name')->comment('会员名称')->index();
            $table->string('issue')->default('')->comment('奖期');
            $table->decimal('stake', 16, 6)->default(0)->comment('总投注额原始数据');
            $table->decimal('user_stake', 16, 6)->default(0)->comment('总投注额转化后数据');
            $table->decimal('bet', 16, 6)->comment('总投注额原始数据');
            $table->decimal('user_bet', 16, 6)->comment('有效投注额转化后数据');
            $table->decimal('prize', 16, 6)->comment('中奖奖金');
            $table->decimal('profit', 16, 6)->comment('会员盈亏');
            $table->string('odds')->default('')->comment('赔率');
            $table->decimal('after_balance', 16, 6)->nullable()->comment('余额');
            $table->dateTime('bet_at')->nullable()->comment('投注时间');
            $table->dateTime('payout_at')->nullable()->comment('结算时间');
            $table->string('user_currency')->comment('会员币别');
            $table->decimal('user_prize', 16, 6)->comment('会员中奖奖金');
            $table->decimal('user_profit', 16, 6)->comment('会员会员盈亏');
            $table->decimal('platform_profit', 16, 6)->comment('平台盈亏');
            $table->unsignedMediumInteger('multiple')->default(0)->comment('倍数');
            $table->string('money_unit')->default('')->comment('资金单位');
            $table->text('bet_info')->nullable()->comment('投注详情');
            $table->text('win_info')->nullable()->comment('开奖详情');
            $table->text('win_result')->nullable()->comment('开奖结果');
            $table->string('user_prize_group')->nullable()->comment('会员当下奖金组');
            $table->decimal('available_bet', 16, 6)->default(0)->comment('可用投注');
            $table->decimal('available_profit', 16, 6)->default(0)->comment('可用盈亏');
            $table->decimal('available_rebate_bet', 16, 6)->default(0)->comment('可用于返点的');
            $table->decimal('jpc', 12, 6)->default(0)->comment('老虎机奖池贡献值');
            $table->decimal('jpw', 16, 6)->default(0)->comment('老虎机奖池中奖值');
            $table->decimal('jpw_jpc', 16, 6)->default(0)->comment('老虎机奖池中奖额玩家贡献部分');
            $table->boolean('is_close')->default(false)->comment('是否关闭');
            $table->unsignedTinyInteger('platform_status')->default(1)->comment('第三方投注状态');
            $table->unsignedTinyInteger('status')->default(\App\Models\GameBetDetail::STATUS_CREATED);
            $table->dateTime('finished_at')->nullable()->comment('处理完成时间');
            $table->string('remark', 1024)->default('')->comment('备注');
            $table->json('trace_logs')->nullable()->comment('追踪日志');
            $table->timestamps();

            $table->unique(['platform_code', 'order_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('game_bet_details');
    }
}
