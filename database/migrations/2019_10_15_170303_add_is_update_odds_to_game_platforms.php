<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsUpdateOddsToGamePlatforms extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_platforms', function (Blueprint $table) {
            $table->boolean('is_update_odds')->default(false)->comment('是否更新odds');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_platforms', function (Blueprint $table) {
            $table->dropColumn('is_update_odds');
        });
    }
}
