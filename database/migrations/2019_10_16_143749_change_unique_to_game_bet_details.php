<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueToGameBetDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_bet_details', function (Blueprint $table) {
            $table->dropUnique(['platform_code', 'order_id']);
            $table->unique(['platform_code', 'order_id', 'platform_status']);
            $table->boolean('is_check_open')->default(false)->comment('是否检查未开奖订单');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_bet_details', function (Blueprint $table) {
//            $table->dropUnique(['platform_code', 'order_id', 'platform_status']);
//            $table->unique(['platform_code', 'order_id']);
//            $table->dropColumn('is_check_open');
        });
    }
}
