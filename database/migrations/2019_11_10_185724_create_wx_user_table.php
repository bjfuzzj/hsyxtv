<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wx_users', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id', 128)->default('')->comment('微信openId');
            $table->string('union_id', 128)->default('')->comment('微信unionId');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedTinyInteger('source')->default(0)->comment('1:小程序 2:公众号');
            $table->string('nick_name', 64)->default('')->comment('微信昵称');
            $table->string('avatar')->default('')->comment('微信头像');
            $table->unsignedTinyInteger('gender')->default(0)->comment('性别');
            $table->string('country', 64)->default('')->comment('国家');
            $table->string('province', 64)->default('')->comment('省');
            $table->string('city', 128)->default('')->comment('城市');
            $table->string('district', 128)->default('')->comment('地区');
            $table->string('language', 32)->default('')->comment('语言');
            $table->string('reg_ip', 32)->default('')->comment('用户首次注册IP');
            $table->unsignedTinyInteger('is_subscribe')->default(0)->comment('是否关注公众号');
            $table->timestamp('subscribe_time')->nullable()->comment('关注公众号时间');
            $table->string('subscribe_scene', 64)->default('')->comment('关注公众号来源');
            $table->timestamps();
            $table->unique(['open_id'], 'uniq_open_id');
            $table->index(['union_id'], 'idx_union_id');
            $table->index(['user_id'], 'idx_user_id');
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
        Schema::dropIfExists('wx_users');
    }
}
