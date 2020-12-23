<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePromotionTypesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('promotion_types', function (Blueprint $table) {
            $table->increments('id');
            $table->json('currencies');
            $table->json('languages');
            $table->string('code')->comment('辨识码')->unique();
            $table->string('web_img_path')->default('')->comment('pc显示图片');
            $table->string('mobile_img_path')->default('')->comment('mobile显示图片');
            $table->boolean('status')->default(false);
            $table->string('admin_name')->default('');
            $table->unsignedSmallInteger('sort')->default(0);
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
        Schema::dropIfExists('promotion_types');
    }
}
