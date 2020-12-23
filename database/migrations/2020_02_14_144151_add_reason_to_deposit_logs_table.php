<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddReasonToDepositLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('deposit_logs', function (Blueprint $table) {
            $table->string('reason')->nullable()->after('type')->comment('操作原因');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('deposit_logs', function (Blueprint $table) {
            $table->dropColumn('reason');
        });
    }
}
