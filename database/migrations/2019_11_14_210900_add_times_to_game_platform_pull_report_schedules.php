<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTimesToGamePlatformPullReportSchedules extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_platform_pull_report_schedules', function (Blueprint $table) {
            $table->unsignedSmallInteger('times')->default(0)->comment('拉取次数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_platform_pull_report_schedules', function (Blueprint $table) {
            $table->dropColumn('times');
        });
    }
}
