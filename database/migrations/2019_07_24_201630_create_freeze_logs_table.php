<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateFreezeLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('freeze_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->string('user_name');
            $table->string('currency');
            $table->boolean('is_freeze')->default(true)->comment('是否冻结');
            $table->decimal('amount', 16, 6)->default(0)->comment('冻结金额');
            $table->decimal('before_freeze_balance',16, 6)->default(0)->comment('冻结前金额');
            $table->decimal('after_freeze_balance',16, 6)->default(0)->comment('冻结后金额');
            $table->unsignedTinyInteger('type')->default(1)->comment('冻结类型');
            $table->unsignedInteger('trace_id')->nullable()->comment('追踪id');
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
        Schema::dropIfExists('freeze_logs');
    }
}
