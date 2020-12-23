<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddShowTypeToPaymentPlatforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_platforms', function (Blueprint $table) {
            $table->unsignedTinyInteger('show_type')->default(\App\Models\PaymentPlatform::SHOW_TYPE_ALL)->comment('显示类型');
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
            $table->dropColumn('show_type');
        });
    }
}
