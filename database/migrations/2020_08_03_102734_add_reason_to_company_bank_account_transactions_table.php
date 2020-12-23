<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonToCompanyBankAccountTransactionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_bank_account_transactions', function (Blueprint $table) {
            $table->tinyInteger('reason')->nullable();
            $table->string('order_no')->default('')->comment('订单号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_bank_account_transactions', function (Blueprint $table) {
            $table->dropColumn('order_no');
            $table->dropColumn('reason');
        });
    }
}
