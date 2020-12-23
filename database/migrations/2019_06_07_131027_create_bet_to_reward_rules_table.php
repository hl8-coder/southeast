<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBetToRewardRulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('bet_to_reward_rules', function (Blueprint $table) {
            $table->increments('id');
            $table->string('currency')->unique();
            $table->unsignedInteger('rule')->comment('1积分兑换所需金额');
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
        Schema::dropIfExists('bet_to_reward_rules');
    }
}
