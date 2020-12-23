<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeTrackingStatisticLogsTrackingId extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('tracking_statistic_logs', function (Blueprint $table) {
            $table->integer('tracking_id')->nullable()->change();
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
            $table->integer('tracking_id')->change();
        });
    }
}
