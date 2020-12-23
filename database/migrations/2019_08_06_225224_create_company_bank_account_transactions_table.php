<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBankAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_bank_account_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_bank_account_code')->comment('公司银行卡code')->index('code');
            $table->unsignedTinyInteger('type')->comment('类型');
            $table->boolean('is_income')->comment('是否是进帐 true:进账 false:出账');
            $table->string('from_account')->default('');
            $table->string('to_account')->default('');
            $table->string('user_name')->nullable()->comment('会员名称');
            $table->decimal('total_amount', 16, 6)->default(0)->comment('帐变总金额');
            $table->decimal('amount', 16, 6)->default(0)->comment('帐变金额');
            $table->decimal('fee', 12, 6)->default(0)->comment('帐变手续费');
            $table->decimal('after_balance', 16, 6)->default(0)->comment('帐变后金额');
            $table->unsignedInteger('trace_id')->nullable()->comment('追踪id');
            $table->string('admin_name')->nullable()->comment('管理员名称');
            $table->string('remark')->default('')->comment('备注');
            $table->timestamps();

            $table->index(['type', 'trace_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_bank_account_transactions');
    }
}
