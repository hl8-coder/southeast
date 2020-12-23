<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOpenBetToUserProductMonthlyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_product_monthly_reports', function (Blueprint $table) {
            $table->decimal('open_bet', '16', '6')->default(0)->comment('未开奖投注');
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
            $table->dropColumn('open_bet');
        });
    }
}
