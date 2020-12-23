<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePgAccountTransactions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pg_account_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_platform_code')->comment('第三方支付通道code')->index();
            $table->unsignedTinyInteger('type')->comment('类型 1用户存款变动 2从第三方提款到公司银行变动 3手动调整变动');
            $table->boolean('is_income')->comment('是否是进帐 true:进账 false:出账');
            $table->string('from_account')->default('');
            $table->string('to_account')->default('');
            $table->string('user_name')->nullable()->comment('会员名称');
            $table->decimal('total_amount', 16, 6)->default(0)->comment('帐变总金额,帐变金额+帐变手续费');
            $table->decimal('amount', 16, 6)->default(0)->comment('帐变金额');
            $table->decimal('fee', 12, 6)->default(0)->comment('帐变手续费');
            $table->decimal('after_balance', 16, 6)->default(0)->comment('帐变后金额');
            $table->string('trace_id')->nullable()->comment('追踪order_no，用户存款的话就对应用户的存款记录order_no');
            $table->string('admin_name')->nullable()->comment('管理员名称');
            $table->string('remark')->default('')->comment('备注');
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
        Schema::dropIfExists('pg_account_transactions');
    }
}
