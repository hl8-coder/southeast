<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('vips', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedTinyInteger('level')->comment('等级')->unique();
            $table->string('name')->default('')->comment('名称');
            $table->string('display_name')->default('')->comment('前端显示名称');
            $table->unsignedInteger('rule')->comment('等级条件');
            $table->string('remark', 1024)->default('')->comment('备注');
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
        Schema::dropIfExists('vips');
    }
}
