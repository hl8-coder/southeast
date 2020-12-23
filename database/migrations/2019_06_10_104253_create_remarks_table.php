<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedTinyInteger('type')->comment('类型');
            $table->unsignedTinyInteger('category')->comment('分类');
            $table->unsignedTinyInteger('sub_category')->nullable()->comment('子分类');
            $table->string('reason', 1024)->default('')->comment('备注');
            $table->string('remove_reason', 1024)->default('')->comment('移除理由');
            $table->string('admin_name')->nullable()->comment('管理员名称');
            $table->string('remove_admin_name')->nullable()->comment('移除管理员名称');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('remarks');
    }
}
