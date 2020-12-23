<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->comment('账号')->unique();
            $table->string('nick_name')->default('')->comment('昵称');
            $table->string('password')->comment('登陆密码');
            $table->string('operate_password')->comment('操作密码');
            $table->string('avatar')->default('')->comment('头像地址');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('status')->default(\App\Models\Admin::STATUS_ACTIVE);
            $table->string('description')->default('')->comment('描述');
            $table->string('language')->default('zh-CN')->comment('语言');
            $table->boolean('is_super_admin')->default(false)->comment('是否是超级管理员');
            $table->softDeletes();
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
        Schema::dropIfExists('admins');
    }
}
