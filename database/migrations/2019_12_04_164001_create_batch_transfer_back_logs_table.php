<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBatchTransferBackLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('batch_transfer_back_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_code')->index();
            $table->unsignedInteger('platform_user_id')->index();
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name');
            $table->decimal('amount', 16, 6)->default(0);
            $table->unsignedTinyInteger('status');
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
        Schema::dropIfExists('batch_transfer_back_logs');
    }
}
