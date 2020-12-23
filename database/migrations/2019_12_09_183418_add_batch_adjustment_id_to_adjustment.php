<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchAdjustmentIdToAdjustment extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->bigInteger('batch_adjustment_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        try {
            Schema::table('adjustments', function (Blueprint $table) {
                $table->dropColumn('batch_adjustment_id');
            });
        }catch (Exception $exception){
            \Illuminate\Support\Facades\Log::error($exception);
        }
    }
}
