<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWebUrlAndCityToUserInfoTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_info', function (Blueprint $table) {
            $table->string('city')->nullable()->comment('城市');
            $table->string('web_url')->nullable()->comment('网站');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_info', function (Blueprint $table) {
            $table->dropColumn(['city', 'web_url']);
        });
    }
}
