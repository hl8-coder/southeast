<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateConfigsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('configs', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('辨识码')->unique();
            $table->string('name')->comment('名称');
            $table->unsignedTinyInteger('group')->default(1)->comment('分组');
            $table->string('remarks')->default('')->comment('描述');
            $table->boolean('is_front_show')->default(false)->comment('是否前端显示');
            $table->string('type')->default('string')->comment('数值类型');
            $table->string('value')->nullable()->comment('值');
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
        Schema::dropIfExists('configs');
    }
}
