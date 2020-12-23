<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateVerifiedPrizeBlackUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('verified_prize_black_users', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->unique()->comment('user id');
            $table->string('user_name')->index()->comment('user name');
            $table->string('add_by')->index()->comment('admin name');
            $table->integer('add_by_admin_id')->index()->comment('admin id');
            $table->dateTime('add_at')->comment('增加时间');
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
        Schema::dropIfExists('verified_prize_black_users');
    }
}
