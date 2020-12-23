<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserRisksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_risks', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id');
            $table->integer('behaviour')->default(1)->comment('行为');
            $table->string('remark')->nullable()->comment('备注');
            $table->string('updated_by')->nullable()->comment('修改人');
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
        Schema::dropIfExists('user_risks');
    }
}
