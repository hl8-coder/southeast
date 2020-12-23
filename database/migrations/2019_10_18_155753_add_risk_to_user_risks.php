<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddRiskToUserRisks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_risks', function (Blueprint $table) {
            $table->integer('risk')->default(1)->comment('风险级别');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_risks', function (Blueprint $table) {
            $table->dropColumn('risk');
        });
    }
}
