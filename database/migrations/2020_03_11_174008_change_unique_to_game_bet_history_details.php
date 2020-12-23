<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeUniqueToGameBetHistoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_bet_history_details', function (Blueprint $table) {
            $table->dropUnique(['platform_code', 'order_id']);
            $table->unique(['platform_code', 'order_id', 'platform_status'],'platform_code_order_id_platform_status_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_bet_history_details', function (Blueprint $table) {
            //
        });
    }
}
