<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCurrenciesPendingLimit extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('currencies', function (Blueprint $table) {
            $table->integer('deposit_pending_limit')->default(10)->comment('允许订单pending数量最大值');
            $table->integer('withdrawal_pending_limit')->default(10)->comment('允许订单pending数量最大值');
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
            $table->dropColumn('deposit_pending_limit');
            $table->dropColumn('withdrawal_pending_limit');
        });
    }
}
