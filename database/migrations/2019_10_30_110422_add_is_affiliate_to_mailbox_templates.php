<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsAffiliateToMailboxTemplates extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('mailbox_templates', function (Blueprint $table) {
            $table->boolean('is_affiliate')->default(false);
            $table->json('currencies')->nullable()->comment('币别');
            $table->unique(['is_affiliate', 'type']);
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('mailbox_templates', function (Blueprint $table) {
            $table->dropColumn('is_affiliate');
            $table->dropColumn('currencies');
        });
    }
}
