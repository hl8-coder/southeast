<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGamePlatformPullReportSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('game_platform_pull_report_schedules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('platform_code')->index();
            $table->dateTime('start_at')->comment('拉取开始时间');
            $table->dateTime('end_at')->comment('拉取结束时间');
            $table->unsignedTinyInteger('status')->default(1)->comment('拉取状态');
            $table->dateTime('pulled_at')->nullable()->comment('拉取时间');
            $table->unsignedInteger('origin_total')->default(0)->comment('原始记录条数');
            $table->unsignedInteger('transfer_total')->default(0)->comment('转换记录条数');
            $table->string('remarks', 2048)->default('')->comment('系统备注');
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
        Schema::dropIfExists('game_platform_pull_report_schedules');
    }
}
