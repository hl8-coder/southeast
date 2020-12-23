<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsWalletMaintainToGamePlatformsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_platforms', function (Blueprint $table) {
            $table->boolean('is_wallet_maintain')->default(false)->comment('平台钱包是否处于维护状态');
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
            $table->dropColumn('is_wallet_maintain');
        });
    }
}
