<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderNoToUserBonusPrizesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_bonus_prizes', function (Blueprint $table) {
            $table->string('order_no')->default('')->comment('订单号');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_bonus_prizes', function (Blueprint $table) {
            $table->dropColumn('order_no');
        });
    }
}
