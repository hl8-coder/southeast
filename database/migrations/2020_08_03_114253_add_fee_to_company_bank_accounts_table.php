<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFeeToCompanyBankAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_bank_accounts', function (Blueprint $table) {
            $table->decimal('fee', 12, 6)->default(0)->comment('帐变手续费');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_bank_accounts', function (Blueprint $table) {
            $table->dropColumn('fee');
        });
    }
}
