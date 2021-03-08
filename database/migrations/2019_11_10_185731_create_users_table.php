<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->increments('id')->comment('用户Id');
            $table->string('union_id', 128)->default('')->comment('微信unionId');
            $table->string('nick_name', 64)->default('')->comment('微信昵称');
            $table->string('real_name', 64)->default('')->comment('真实姓名');
            $table->string('head_image')->default('')->comment('头像');
            $table->string('mobile', 32)->default('')->comment('手机号');
            $table->timestamp('mobile_bind_time')->nullable()->comment('绑定手机号时间');
            $table->string('id_card', 32)->default('')->comment('身份证号码');
            $table->unsignedTinyInteger('is_distributor')->default(0)->comment('是否是分销员 0:不是 1:是');
            $table->unsignedTinyInteger('distributor_level')->default(0)->comment('分销员等级 0:无 1:金牌买手 2:钻石卖家');
            $table->unsignedTinyInteger('is_advance_sale')->default(0)->comment('是否是预售用户 0:不是 1:是');
            $table->unsignedTinyInteger('advance_sale_level')->default(0)->comment('分销员预售等级 0: 无 1:皇冠会员 2:钻石会员 3:合伙人');
            $table->unsignedInteger('integrals')->default(0)->comment('积分');
            $table->timestamps();
            $table->index(['union_id'], 'idx_union_id');
            $table->index(['mobile'], 'idx_mobile');
            $table->index(['created_at'], 'idx_created_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('users');
    }
}
