<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUserPlatformDailyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_platform_daily_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->comment('会员id')->index();
            $table->string('user_name')->comment('会员名称')->index();
            $table->string('platform_code')->comment('平台code')->index();
            $table->date('date')->comment('所属日期');
            $table->decimal('deposit', 16, 6)->default(0)->comment('充值');
            $table->decimal('withdrawal', 16, 6)->default(0)->comment('提现');
            $table->decimal('transfer_in', 16, 6)->default(0)->comment('转入');
            $table->decimal('transfer_out', 16, 6)->default(0)->comment('转出');
            $table->decimal('adjustment', 16, 6)->default(0)->comment('调整');
            $table->timestamps();

            $table->unique(['user_id', 'platform_code', 'date'], 'user_platform_date_unique');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_platform_daily_reports');
    }
}
