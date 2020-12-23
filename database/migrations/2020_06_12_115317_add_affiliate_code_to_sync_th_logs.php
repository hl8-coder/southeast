<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAffiliateCodeToSyncThLogs extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('sync_th_logs', function (Blueprint $table) {
            $table->string('old_affiliate_code')->nullable();
            $table->string('new_affiliate_code')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('sync_th_logs', function (Blueprint $table) {
            $table->dropColumn('old_affiliate_code');
            $table->dropColumn('new_affiliate_code');
        });
    }
}
