<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateRiskGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('risk_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('分组名称');
            $table->string('description', 1024)->default('')->comment('分组描述');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->boolean('status')->default(true);
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
        Schema::dropIfExists('risk_groups');
    }
}
