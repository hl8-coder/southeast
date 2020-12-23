<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddTurnoverToTransferDetails extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('transfer_details', function (Blueprint $table) {
            # 流水要求
            $table->boolean('is_turnover_closed')->default(false)->comment('流水限制是否关闭');
            $table->decimal('turnover_closed_value', 16, 6)->default(0)->comment('所需流水总数');
            $table->decimal('turnover_current_value', 16, 6)->default(0)->comment('当前流水数值');
            $table->dateTime('turnover_closed_at')->nullable();
            $table->string('turnover_closed_admin_name')->nullable();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('transfer_details', function (Blueprint $table) {
            $table->dropColumn('turnover_closed_admin_name');
            $table->dropColumn('turnover_closed_at');
            $table->dropColumn('turnover_current_value');
            $table->dropColumn('turnover_closed_value');
            $table->dropColumn('is_turnover_closed');
        });
    }
}
