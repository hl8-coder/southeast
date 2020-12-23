<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateImagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('images', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_type');
            $table->integer('user_id');
            $table->string('name')->default('')->comment('上传文件名称');
            $table->string('path')->default('');
            $table->string('imageable_type')->nullable();
            $table->string('imageable_id')->nullable();
            $table->timestamps();

            $table->index(['user_type', 'user_id']);
            $table->index(['imageable_type', 'imageable_id']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('images');
    }
}
