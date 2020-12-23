<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsCanTryToGamePlatformProducts extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('game_platform_products', function (Blueprint $table) {
            $table->boolean('is_can_try')->default(false)->comment('是否可以试玩');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('game_platform_products', function (Blueprint $table) {
            $table->dropColumn('is_can_try');
        });
    }
}
