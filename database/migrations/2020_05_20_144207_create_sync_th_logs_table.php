<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSyncThLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sync_th_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('old_id');
            $table->unsignedInteger('new_id');
            $table->unsignedInteger('old_parent_id')->nullable();
            $table->unsignedInteger('new_parent_id')->nullable();
            $table->string('old_name')->nullable();
            $table->string('new_name')->nullable();
            $table->string('old_email')->nullable();
            $table->string('new_email')->nullable();
            $table->string('old_phone')->nullable();
            $table->string('new_phone')->nullable();
            $table->boolean('is_agent');
            $table->boolean('status');
            $table->string('remark', 2048)->default('');
            $table->unique(['old_id', 'is_agent', 'status']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('sync_th_logs');
    }
}
