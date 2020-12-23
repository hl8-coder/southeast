<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropTrackingIdTrackingNameFromCreativeResources extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('creative_resources', function (Blueprint $table) {
            $table->json('currency')->nullable()->change();
            $table->dropColumn('tracking_id');
            $table->dropColumn('tracking_name');
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
            //
        });
    }
}
