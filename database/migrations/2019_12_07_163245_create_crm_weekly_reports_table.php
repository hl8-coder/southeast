<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmWeeklyReportsTable extends Migration
{
    private $table = 'crm_weekly_reports';
    private $comment = 'crm 每周统计报表';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('week')->index()->comment('第几周');
            $table->string('week_start_at')->nullable()->comment('统计开始时间');
            $table->string('week_end_at')->nullable()->comment('统计结束时间');
            $table->tinyInteger('type')->index()->nullable()->comment('crm order 订单类型');
            $table->integer('total_orders')->default(0)->comment('周期订单总数');
            $table->integer('total_calls')->default(0)->comment('周期订单呼叫总数');
            $table->integer('total_type_orders')->default(0)->comment('周期同类型订单总量');
            $table->integer('total_type_calls')->default(0)->comment('周期同类型呼叫总量');
            $table->integer('person_total_orders')->default(0)->comment('个人周期订单总数');
            $table->integer('person_total_calls')->default(0)->comment('个人周期呼叫总数');
            $table->integer('person_total_type_orders')->default(0)->comment('个人周期同类型订单总量');
            $table->integer('person_total_type_calls')->default(0)->comment('个人周期同类型订单总量');
            $table->integer('successful')->index()->default(0)->comment('统计周期个人总成功呼叫'); // 这里应该注释为个人
            $table->integer('fail')->index()->default(0)->comment('统计周期个人总失败呼叫');
            $table->integer('voice_mail')->default(0)->comment('语音邮箱');
            $table->integer('success')->default(0)->comment('营销成功');
            $table->integer('hand_up')->default(0)->comment('呼叫挂断');
            $table->integer('no_pick_up')->default(0)->comment('呼叫挂断');
            $table->integer('invalid_number')->default(0)->comment('无效号码');
            $table->integer('not_own_number')->default(0)->comment('非号码持有人');
            $table->integer('call_back')->default(0)->comment('回拨');
            $table->integer('not_answer')->default(0)->comment('无应答');
            $table->integer('not_interested_in')->default(0)->comment('不感兴趣');
            $table->integer('other')->default(0)->comment('其他');
            $table->integer('register')->index()->default(0)->comment('注册人数');
            $table->integer('ftd_member')->nullable()->comment('通话后首充人数');
            $table->decimal('ftd_amount', 16, 6)->nullable()->comment('通话后首充金额总额');
            $table->decimal('adjustment_amount', 16, 6)->nullable()->comment('调额总额');
            $table->integer('admin_id')->index()->nullable()->comment('admin id');
            $table->string('admin_name')->index()->nullable()->comment('admin name');
            $table->timestamps();
        });

        DB::statement("alter table {$this->table} comment '{$this->comment}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->table);
    }
}
