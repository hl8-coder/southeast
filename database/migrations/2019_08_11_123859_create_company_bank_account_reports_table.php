<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBankAccountReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_bank_account_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->string('company_bank_account_code')->comment('公司银行卡code')->index();
            $table->decimal('opening_balance', 16, 6)->default(0)->comment('开始余额');
            $table->decimal('ending_balance', 16, 6)->default(0)->comment('结束余额');
            $table->decimal('buffer_in', 16, 6)->default(0)->comment('buffer转入');
            $table->decimal('buffer_out', 16, 6)->default(0)->comment('buffer转出');
            $table->decimal('deposit', 16, 6)->default(0)->comment('充值');
            $table->decimal('withdrawal', 16, 6)->default(0)->comment('提现');
            $table->decimal('adjustment', 16, 6)->default(0)->comment('调整');
            $table->decimal('internal_transfer', 16, 6)->default(0)->comment('内部转账');
            $table->date('date')->comment('所属日期');
            $table->timestamps();

            $table->unique(['company_bank_account_code', 'date'], 'unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('company_bank_account_reports');
    }
}
