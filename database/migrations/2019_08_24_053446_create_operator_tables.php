<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperatorTables extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operators', function (Blueprint $table) {
            $table->increments('id');
            $table->string('user_name', 32)->default('')->comment('用户名');
            $table->string('password')->default('')->comment('密码');
            $table->string('real_name', 18)->default('')->comment('真实姓名');
            $table->string('mobile', 18)->default('')->comment('手机号');
            $table->string('email', 128)->default('')->comment('邮箱');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态 1:启用 2:停用');
            $table->unsignedInteger('creator_id')->default(0)->comment('创建者ID');
            $table->string('creator_name', 18)->default(0)->comment('创建者姓名');
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
        Schema::dropIfExists('operators');
    }
}
