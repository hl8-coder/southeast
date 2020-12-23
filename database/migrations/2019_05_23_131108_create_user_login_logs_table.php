<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserLoginLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_login_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id');
            $table->string('user_name')->default('');
            $table->unsignedTinyInteger('device')->comment('装置');
            $table->string('equipment')->default('')->comment('设备');
            $table->string('browser', 1024)->default('');
            $table->string('ip')->default('');
            $table->string('country')->default('');
            $table->string('city')->default('');
            $table->string('state')->default('');
            $table->string('remark', 2048)->default('')->comment('备注');
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
        Schema::dropIfExists('user_login_logs');
    }
}
