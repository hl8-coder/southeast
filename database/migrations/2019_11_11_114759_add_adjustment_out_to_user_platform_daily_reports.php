<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAdjustmentOutToUserPlatformDailyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_platform_daily_reports', function (Blueprint $table) {
            $table->dropColumn('adjustment');
            $table->decimal('adjustment_in', 16, 6)->default(0)->comment('加钱调整');
            $table->decimal('adjustment_out', 16, 6)->default(0)->comment('扣钱调整');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_platform_daily_reports', function (Blueprint $table) {
            $table->dropColumn('adjustment_out');
            $table->dropColumn('adjustment_in');
            $table->decimal('adjustment', 16, 6)->default(0)->comment('调整');
        });
    }
}
