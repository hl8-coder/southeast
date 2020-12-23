<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCategoryFieldToCompanyBankAccountRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('company_bank_account_remarks', function (Blueprint $table) {
            $table->string('category')->after('remark')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('company_bank_account_remarks', function (Blueprint $table) {
            $table->dropColumn('category');
        });
    }
}
