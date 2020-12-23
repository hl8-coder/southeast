<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRelatedNoAndRelatedNameToPaymentPlatforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_platforms', function (Blueprint $table) {
            $table->string('related_name')->default('')->comment('关联名称');
            $table->string('related_no')->default('')->comment('关联号码');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_platforms', function (Blueprint $table) {
            $table->dropColumn('related_name');
            $table->dropColumn('related_no');
        });
    }
}
