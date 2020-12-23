<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBetAtIndexToGameBetDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_bet_details', function (Blueprint $table) {
            $table->index('bet_at','bet_at_index');
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
            $table->dropIndex('bet_at_index');
        });
    }
}
