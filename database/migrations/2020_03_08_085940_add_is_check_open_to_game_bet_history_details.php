<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCheckOpenToGameBetHistoryDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        try {
            Schema::table('game_bet_history_details', function (Blueprint $table) {
                $table->tinyInteger('is_check_open')->default(0);
            });
        }catch (\Exception $exception) {

        }

    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::table('game_bet_history_details', function (Blueprint $table) {
                $table->dropColumn('is_check_open');
            });
        } catch (\Exception $exception) {
            
        }

    }
}
