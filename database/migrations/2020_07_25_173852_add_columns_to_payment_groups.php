<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddColumnsToPaymentGroups extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('payment_groups', function (Blueprint $table) {
            $table->string('currency')->after('name')->default('VND')->comment('币别');
            $table->json('account_code')->after('currency')->nullable()->comment('账户代号');
            $table->string('last_save_admin')->after('status')->nullable()->comment('最后操作的管理员');
            $table->dateTime('last_save_at')->after('last_save_admin')->nullable()->comment('最后保存时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('payment_groups', function (Blueprint $table) {
            $table->dropColumn(['currency', 'account_code', 'last_save_admin', 'last_save_at']);
        });
    }
}
