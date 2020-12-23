<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDevicesCodeToCreativeResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creative_resources', function (Blueprint $table) {
            $table->string('code')->unique();
            $table->json('devices')->comment('装置');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('creative_resources', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('devices');
        });
    }
}
