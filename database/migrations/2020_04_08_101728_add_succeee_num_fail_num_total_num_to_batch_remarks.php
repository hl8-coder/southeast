<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddSucceeeNumFailNumTotalNumToBatchRemarks extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('batch_remarks', function (Blueprint $table) {
            $table->integer('success_num')->default(0)->comment('成功的条数');
            $table->integer('fail_num')->default(0)->comment('失败的条数');
            $table->integer('total_num')->default(0)->comment('总条数');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('batch_remarks', function (Blueprint $table) {
            try {
                Schema::table('batch_remarks', function (Blueprint $table) {
                    $table->dropColumn('success_num');
                    $table->dropColumn('fail_num');
                    $table->dropColumn('total_num');
                });
            }catch (Exception $exception){
                \Illuminate\Support\Facades\Log::error($exception);
            }
        });
    }
}
