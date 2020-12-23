<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAffiliateTransferToUserPlatformTotalReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('user_platform_total_reports', function (Blueprint $table) {
            $table->decimal('affiliate_transfer_in', 16, 6)->default(0)->comment('代理转入');
            $table->decimal('affiliate_transfer_out', 16, 6)->default(0)->comment('代理转出');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('user_platform_total_reports', function (Blueprint $table) {
            $table->dropColumn(['affiliate_transfer_in', 'affiliate_transfer_out']);
        });
    }
}
