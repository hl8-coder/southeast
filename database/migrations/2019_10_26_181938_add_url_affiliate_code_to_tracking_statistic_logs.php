<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddUrlAffiliateCodeToTrackingStatisticLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_statistic_logs', function (Blueprint $table) {
            $table->string('affiliate_code')->nullable();
            $table->string('url')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('tracking_statistic_logs', function (Blueprint $table) {
            $table->dropColumn('affiliate_code');
            $table->dropColumn('url');
        });
    }
}
