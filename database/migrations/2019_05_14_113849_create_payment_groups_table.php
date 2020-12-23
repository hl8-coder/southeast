<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreatePaymentGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payment_groups', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->unique()->comment('分组名称');
            $table->string('remark', 1024)->default('')->comment('分组备注');
            $table->unsignedSmallInteger('preset_risk_group_id')->nullable()->comment('预设风控组别')->index();
            $table->unsignedTinyInteger('status')->default(true);
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
        Schema::dropIfExists('payment_groups');
    }
}
