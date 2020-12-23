<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddBatchRemarkIdToRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('remarks', function (Blueprint $table) {
            $table->bigInteger('batch_remark_id')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('remarks', function (Blueprint $table) {
            try {
                Schema::table('remarks', function (Blueprint $table) {
                    $table->dropColumn('batch_remark_id');
                });
            }catch (Exception $exception){
                \Illuminate\Support\Facades\Log::error($exception);
            }
        });
    }
}
