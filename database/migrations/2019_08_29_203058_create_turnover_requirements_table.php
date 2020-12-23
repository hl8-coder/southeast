<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateTurnoverRequirementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('turnover_requirements', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->index();
            $table->unsignedInteger('requireable_id');
            $table->string('requireable_type');
            $table->boolean('is_closed')->default(false);
            $table->dateTime('closed_at')->nullable();
            $table->timestamps();

            $table->unique(['requireable_id', 'requireable_type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('turnover_requirements');
    }
}
