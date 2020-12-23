<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPopUpToAffiliateAnnouncementsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('affiliate_announcements', function (Blueprint $table) {
            $table->boolean('pop_up')->default(false)->comment('公告置顶时是否允许弹窗');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('affiliate_announcements', function (Blueprint $table) {
            $table->dropColumn('pop_up');
        });
    }
}
