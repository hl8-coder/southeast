<?php

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmExcludeUsersTable extends Migration
{
    private $table = 'crm_exclude_users';
    private $comment = 'crm黑名单';
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->table, function (Blueprint $table) {
            $table->increments('id');
            $table->integer('user_id')->index()->comment('user id 或者 affiliate id');
            $table->string('user_name')->index()->comment('user 或者 代理 名称');
            $table->string('affiliate_code')->index()->nullable()->comment('代理 code');
            $table->string('affiliated_code')->index()->nullable()->comment('上级代理 code');
            $table->boolean('is_affiliate')->default(false)->comment('是否为代理');
            $table->integer('action_admin_id')->index()->comment('操作者ID');
            $table->string('action_admin_name')->comment('操做者名称');
            $table->timestamp('review_at')->nullable()->comment('审核时间');
            $table->string('review_by')->nullable()->comment('审核人');
            $table->boolean('status')->default(false)->comment('启用状态');
            $table->timestamps();
        });

        DB::statement("alter table {$this->table} add unique (`user_name`, `is_affiliate`)");

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
