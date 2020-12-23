<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPlatformCodeAndRelatedIdToAdjustments extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('adjustments', function (Blueprint $table) {
            $table->dropColumn('product_code');
            $table->index('order_no');
            $table->string('platform_code')->default('')->comment('第三方平台code');
            $table->string('related_order_no')->default('')->comment('关联订单号');
            $table->string('platform_transfer_detail_id')->nullable()->comment('第三方转账id');
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
//            $table->string('product_code')->default('')->comment('product备注');
//            $table->dropIndex('order_no');
//            $table->dropColumn('platform_code');
//            $table->dropColumn('related_order_no');
//            $table->dropColumn('platform_transfer_detail_id');
        });
    }
}
