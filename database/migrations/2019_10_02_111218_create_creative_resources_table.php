<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCreativeResourcesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('creative_resources', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('type')->comment('资源类型');
            $table->integer('group')->comment('资源分组');
            $table->integer('size')->comment('图片尺寸');
            $table->integer('tracking_id')->comment('名称ID');
            $table->string('currency')->comment('币别');
            $table->string('tracking_name')->default('')->comment('名称');
            $table->string('banner_path')->default('')->comment('资源地址');
            $table->string('banner_url')->nullable()->default('')->comment('资源链接');
            $table->string('last_update_by')->nullable()->comment('更新者');
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
        Schema::dropIfExists('creative_resources');
    }
}
