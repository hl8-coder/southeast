<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePgAccountRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('pg_account_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->string('payment_platform_code')->index();
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
        Schema::dropIfExists('pg_account_remarks');
    }
}
