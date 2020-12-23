<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmDailyReportsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_daily_reports', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('week')->index()->comment('第几周');
            $table->string('date')->nullable()->comment('统计开始时间');
            $table->tinyInteger('type')->index()->nullable()->comment('crm order 订单类型');
            $table->integer('total_orders')->default(0)->comment('一天实时订单总数');
            $table->integer('total_calls')->default(0)->comment('一天实时呼叫总数');
            $table->integer('total_type_orders')->default(0)->comment('一天实时同类型类型订单总量');
            $table->integer('total_type_calls')->default(0)->comment('一天实时同类型类型呼叫总量');
            $table->integer('person_total_orders')->default(0)->comment('个人一天实时订单总数');
            $table->integer('person_total_calls')->default(0)->comment('个人一天实时呼叫总数');
            $table->integer('person_total_type_orders')->default(0)->comment('个人一天实时同类型类型订单总量');
            $table->integer('person_total_type_calls')->default(0)->comment('个人一天实时同类型类型呼叫总量');
            $table->integer('successful')->index()->default(0)->comment('统计周期个人总成功呼叫'); // 这里应该注释为个人
            $table->integer('fail')->index()->default(0)->comment('统计周期个人总失败呼叫'); // 同上
            $table->integer('success')->default(0)->comment('营销成功');
            $table->integer('voice_mail')->default(0)->comment('语音邮箱');
            $table->integer('hand_up')->default(0)->comment('呼叫挂断');
            $table->integer('no_pick_up')->default(0)->comment('呼叫挂断');
            $table->integer('invalid_number')->default(0)->comment('无效号码');
            $table->integer('not_own_number')->default(0)->comment('非号码持有人');
            $table->integer('call_back')->default(0)->comment('回拨');
            $table->integer('not_answer')->default(0)->comment('无应答');
            $table->integer('not_interested_in')->default(0)->comment('不感兴趣');
            $table->integer('other')->default(0)->comment('其他');
            $table->integer('admin_id')->index()->nullable()->comment('admin id');
            $table->string('admin_name')->index()->nullable()->comment('admin name');
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
        Schema::dropIfExists('crm_daily_reports');
    }
}
