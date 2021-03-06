<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBetNumToUserProductMonthlyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_product_monthly_reports', function (Blueprint $table) {
            $table->unsignedInteger('bet_num')->default(0)->comment('注单数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_product_monthly_reports', function (Blueprint $table) {
            $table->dropColumn('bet_num');
        });
    }
}
