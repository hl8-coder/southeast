<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRoutesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('名称')->index();
            $table->string('method')->default('GET')->comment('请求方式');
            $table->string('action')->comment('操作');
            $table->string('remark')->comment('代码位置');
            $table->string('url')->default('')->comment('连结')->index();
            $table->string('location')->default('')->comment('位置');
            $table->string('version')->default('v1')->comment('版本');
            $table->timestamps();

            $table->unique(['method', 'action'], 'method_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('routes');
    }
}
