<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRebatesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('rebates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('code')->comment('辨识码')->unique();
            $table->string('product_code')->comment('产品code')->index();
            $table->json('currencies')->comment('币别');
            $table->json('vips')->comment('vip');
            $table->unsignedSmallInteger('risk_group_id')->nullable()->comment('风控组别')->index();
            $table->boolean('is_manual_send')->default(false)->comment('是否需要手动派发');
            $table->boolean('status')->default(true);
            $table->unsignedSmallInteger('sort')->default(0);
            $table->string('admin_name')->nullable();
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
        Schema::dropIfExists('rebates');
    }
}
