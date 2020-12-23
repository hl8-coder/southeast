<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateAffiliateRemarksTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('affiliate_remarks', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('affiliate_id')->index();
            $table->string('reason', 1024)->default('')->comment('原因');
            $table->string('remark', 1024)->default('')->comment('备注');
            $table->string('admin_name')->nullable()->comment('管理员名称');            
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
        Schema::dropIfExists('affiliate_remarks');
    }
}
