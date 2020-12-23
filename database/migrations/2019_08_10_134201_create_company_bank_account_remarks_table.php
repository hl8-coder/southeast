<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCompanyBankAccountRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('company_bank_account_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('company_bank_account_id')->index();
            $table->string('remark', 2048);
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
        Schema::dropIfExists('company_bank_account_remarks');
    }
}
