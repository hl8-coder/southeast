<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPayoutCommMiniLimitToCurrencies extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->string('payout_comm_mini_limit')->nullable()->comment('代理盈亏最小出款金额');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->dropColumn('payout_comm_mini_limit');
        });
    }
}
