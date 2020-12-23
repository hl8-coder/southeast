<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddCodeAndIsVerifiedToPromotions extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->string('code')->nullable()->comment('优惠辨识码')->unique();
            $table->string('related_type')->nullable()->comment('关联类型');
            $table->boolean('is_verified')->default(false)->comment('是否需要审核');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('promotions', function (Blueprint $table) {
            $table->dropColumn('code');
            $table->dropColumn('related_type');
            $table->dropColumn('is_verified');
        });
    }
}
