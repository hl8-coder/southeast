<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_bank_accounts', function (Blueprint $table) {

            $table->increments('id');
            $table->unsignedTinyInteger('platform_id')->nullable()->index();
            # 类型 start
            $table->tinyInteger('type')->comment('类型');
            # 类型 end

            $table->string('currency')->comment('币别')->index();
            $table->string('code')->comment('辨识码')->unique();
            $table->unsignedSmallInteger('payment_group_id')->comment('支付组别id')->index();

            # 银行相关 start
            $table->unsignedSmallInteger('bank_id')->comment('银行ID')->index();
            $table->string('bank_code')->comment('银行code')->index();
            $table->string('province')->default('')->comment('省');
            $table->string('city')->default('')->comment('市');
            $table->string('branch')->default('')->comment('分行');
            $table->string('account_name')->default('')->comment('开户人姓名');
            $table->string('account_no')->default('')->comment('开户账号');
            $table->string('phone')->default('')->comment('电话号码');
            $table->string('phone_asset')->default('');
            $table->string('user_name')->default('')->comment('登录账号');
            $table->string('password')->default('')->comment('登录密码');
            $table->string('safe_key_pass')->default('')->comment('app密码');
            $table->unsignedTinyInteger('otp')->nullable()->comment('关联密码');
            $table->unsignedTinyInteger('app_related')->nullable()->comment('关联app');
            $table->string('image')->default('')->comment('图片');
            # 银行相关 end

            # 余额相关 start
            $table->decimal('first_balance', 16, 6)->default(0)->comment('起始余额');
            $table->decimal('balance', 16, 6)->default(0)->comment('当前余额');
            # 余额相关 end

            # 日进出款相关 start
            $table->decimal('min_balance', 16, 6)->default(0)->comment('余额最小值');
            $table->decimal('max_balance', 16, 6)->default(0)->comment('余额最大值');
            $table->decimal('daily_fund_out', 16, 6)->default(0)->comment('日出款');
            $table->decimal('daily_fund_out_limit', 16, 6)->default(0)->comment('日最高提现金额');
            $table->decimal('daily_fund_in', 16, 6)->default(0)->comment('日存款');
            $table->decimal('daily_fund_in_limit', 16, 6)->default(0)->comment('日最高存款金额');
            $table->unsignedSmallInteger('daily_transaction')->default(0)->comment('日交易次数');
            $table->unsignedSmallInteger('daily_transaction_limit')->default(0)->comment('日最高交易次数');
            # 日进出款相关 end

            # 状态 start
            $table->unsignedTinyInteger('status')->default(\App\Models\CompanyBankAccount::STATUS_ACTIVE);
            # 状态 End

            # 创建管理员
            $table->string('admin_name');

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
        Schema::dropIfExists('company_bank_accounts');
    }
}
