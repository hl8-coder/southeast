<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsGameToAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->boolean('is_game')->default(false)->comment('如果为true 按照游戏跳转方式跳转');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('announcements', function (Blueprint $table) {
            $table->dropColumn('is_game');
        });
    }
}
