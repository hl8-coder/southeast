<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddFieldDepositForImageReciept extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
         Schema::table('deposits', function (Blueprint $table) {
            $table->dateTime('receipt_img_created_at')->after('deposit_at')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposits', function (Blueprint $table) {
            $table->dropColumn('receipt_img_created_at');
        });
    }
}
