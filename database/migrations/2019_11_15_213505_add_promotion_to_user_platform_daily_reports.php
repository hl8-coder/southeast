<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPromotionToUserPlatformDailyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_platform_daily_reports', function (Blueprint $table) {
            $table->decimal('promotion', 16, 6)->default(0)->comment('优惠');
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
            $table->dropColumn('promotion');
        });
    }
}
