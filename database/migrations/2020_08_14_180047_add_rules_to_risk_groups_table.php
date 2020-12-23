<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRulesToRiskGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('risk_groups', function (Blueprint $table) {
            $table->json('rules')->nullable()->comment('风控组规则');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('risk_groups', function (Blueprint $table) {
            $table->dropColumn(['rules']);
        });
    }
}
