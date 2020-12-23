<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchRemarkFails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_remark_fails', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('batch_remark_id')->index();
            $table->string('user_name')->nullable();
            $table->string('type')->nullable();
            $table->string('category')->nullable();
            $table->string('sub_category')->nullable();
            $table->string('reason')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('batch_remark_fails');
    }
}
