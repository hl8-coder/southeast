<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCrmBoAdminsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('crm_bo_admins', function (Blueprint $table) {
            $table->increments('id');
            $table->string('admin_name')->unique()->comment('管理者账号');
            $table->unsignedInteger('admin_id')->unique()->comment('管理者ID');
            $table->unsignedTinyInteger('is_auto_for_welcome')->default(0)->comment('是否为自动分派 WELCOME');
            $table->unsignedTinyInteger('is_auto_for_retention')->default(0)->comment('是否为自动分派 Retention');
            $table->boolean('status')->default(\App\Models\CrmBoAdmin::STATUS_ACTIVE)->comment('是否启用');
            $table->unsignedSmallInteger('sort')->default(0);
            $table->string('start_worked_at')->nullable()->comment('上班时间, 存 H:I 格式');
            $table->string('end_worked_at')->nullable()->comment('下班时间, 存 H:I 格式');
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
        Schema::dropIfExists('crm_bo_admins');
    }
}
