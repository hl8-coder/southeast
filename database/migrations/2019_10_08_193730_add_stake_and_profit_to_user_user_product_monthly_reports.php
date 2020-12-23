<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddStakeAndProfitToUserUserProductMonthlyReports extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_product_monthly_reports', function (Blueprint $table) {
            $table->decimal('stake', 16, 6)->default(0)->comment('总投注');
            $table->decimal('profit', 16, 6)->default(0)->comment('总盈亏');
            $table->dropColumn(['invalid_bet', 'invalid_profit']);
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
            $table->dropColumn(['stake', 'profit']);
        });
    }
}
