<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateActionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('actions', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedSmallInteger('menu_id')->comment('菜单id')->index();
            $table->string('name')->default('')->comment('名称');
            $table->string('method')->default('GET')->comment('请求方式');
            $table->string('action')->comment('操作');
            $table->string('url')->default('')->comment('连结');
            $table->string('drop_list_url')->default('')->comment('下拉清单连结');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->timestamps();

            $table->unique(['menu_id', 'action'], 'menu_id_action');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('actions');
    }
}
