<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmResourcesTable extends Migration
{
    private $table = 'crm_resources';
    private $comment = '市场资源列表';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->string('full_name')->nullable()->comment('机主全名');
            $table->string('country_code')->comment('国家代号');
            $table->string('phone')->comment('电话号码')->index();
            $table->integer('admin_id')->nullable()->index()->comment('bo user的 admin id');
            $table->string('admin_name')->nullable()->index()->comment('bo user的 admin name');
            $table->unsignedInteger('tag_admin_id')->nullable()->comment('标记管理员id');
            $table->string('tag_admin_name')->nullable()->comment('标记管理员');
            $table->timestamp('tag_at')->nullable()->comment('标记详细时间');
            $table->boolean('is_auto')->default(false)->comment('是否为系统自动指派');
            $table->boolean('status')->default(false)->comment('订单状态');
            $table->string('register')->nullable()->comment('注册情况');
            $table->boolean('call_status')->nullable()->comment('联络状态，默认未呼叫');
            $table->unsignedInteger('last_save_case_admin_id')->nullable()->comment('最后操作管理员ID');
            $table->string('last_save_case_admin_name')->nullable()->comment('最后操作管理员名称');
            $table->timestamp('last_save_case_at')->nullable()->comment('最后操作时间');
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
        Schema::dropIfExists($this->table);
    }
}
