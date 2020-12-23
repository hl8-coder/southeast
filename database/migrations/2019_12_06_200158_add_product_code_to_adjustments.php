<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddProductCodeToAdjustments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->string('product_code')->default('')->after('platform_code')->comment('产品code');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->dropColumn('product_code');
        });
    }
}
