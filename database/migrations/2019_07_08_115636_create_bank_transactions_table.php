<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBankTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bank_transactions', function (Blueprint $table) {
            $table->increments('id');
            $table->string('order_no')->default('')->comment('订单号');
            $table->string('transaction_id')->default('')->comment('第三方银行交易id');
            $table->string('bank_code')->comment('银行辨识码')->index();
            $table->string('currency')->default('')->comment('币别')->index();
            $table->string('fund_in_account')->default('')->comment('公司账户')->index();
            $table->string('account_no')->default('')->comment('转账账户');
            $table->string('bank_reference')->default('')->comment('银行参考');
            $table->string('transfer_details')->default('')->comment('交易详情');
            $table->string('description')->default('');
            $table->decimal('debit', 16, 6)->default(0)->comment('取款金额');
            $table->decimal('credit', 16, 6)->default(0)->comment('存款金额');
            $table->decimal('amount', 16, 6)->default(0)->comment('固定存款金额');
            $table->decimal('balance', 16, 6)->default(0)->comment('余额');
            $table->string('channel')->default('')->comment('通道');
            $table->date('transaction_date')->nullable()->comment('交易日期');
            $table->dateTime('transaction_at')->nullable()->comment('交易详细时间');
            $table->string('location')->default('')->comment('本地');
            $table->unsignedTinyInteger('status')->default(\App\Models\BankTransaction::STATUS_NOT_MATCH);
            $table->string('remark', 1024)->default('')->comment('备注');
            $table->string('admin_name')->nullable();
            $table->unsignedInteger('deposit_id')->nullable()->comment('领取id');
            $table->timestamps();
            $table->softDeletes();
        });

        \Illuminate\Support\Facades\DB::statement('alter table bank_transactions add unique(fund_in_account,debit,credit,balance,transaction_date,transaction_at,description)');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('bank_transactions');
    }
}
