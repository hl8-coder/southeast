<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateMenusTable extends Migration
{
    protected $sTableName = "menus";

    protected $sTableComment = "菜单";

    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create($this->sTableName, function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('名称');
            $table->string('code')->comment('唯一代码')->unique();
            $table->string('description')->default('')->comment('描述');
            $table->unsignedSmallInteger('parent_id')->nullable()->comment('父级id')->index();
            $table->unsignedSmallInteger('sort')->default(0)->comment('排序');
            $table->boolean('has_action')->default(true)->comment('是否有操作功能');
            $table->boolean('is_show')->default(true)->comment('是否在页面显示');
            $table->timestamps();
        });

        //添加資料表说明
        \DB::statement("ALTER TABLE {$this->sTableName} comment '{$this->sTableComment}'");
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists($this->sTableName);
    }
}
