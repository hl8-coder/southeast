<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCurrenciesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('currencies', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('名称');
            $table->string('code')->comment('币别')->unique();
            $table->string('preset_language')->comment('预设语言');
            $table->string('country')->comment('所属国家');
            $table->string('country_code')->comment('国家电话编码');
            $table->boolean('is_remove_three_zeros')->default(false)->comment('是否去掉三个零');
            $table->decimal('deposit_second_approve_amount', 16, 6)->default(0)->comment('充值需要二次审核金额');
            $table->decimal('withdrawal_second_approve_amount', 16, 6)->default(0)->comment('提现需要二次审核金额');
            $table->decimal('bank_account_verify_amount', 16, 6)->default(0)->comment('个人银行卡验证金额');
            $table->decimal('info_verify_prize_amount', 16, 6)->default(0)->comment('资料验证完成奖金');
            $table->decimal('max_deposit', 16, 6)->default(0)->comment('最高充值限制');
            $table->decimal('min_deposit', 16, 6)->default(0)->comment('最低充值限制');
            $table->decimal('max_withdrawal', 16, 6)->default(0)->comment('最高出款限制');
            $table->decimal('min_withdrawal', 16, 6)->default(0)->comment('最低出款限制');
            $table->decimal('max_daily_withdrawal', 16, 6)->default(0)->comment('日出款总金额限制');
            $table->decimal('min_transfer', 16, 6)->default(0)->comment('最小转账限制');
            $table->decimal('max_transfer', 16, 6)->default(0)->comment('最大转账限制');
            $table->boolean('status')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
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
        Schema::dropIfExists('currencies');
    }
}
