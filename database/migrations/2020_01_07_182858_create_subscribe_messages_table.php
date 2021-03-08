<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSubscribeMessagesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('subscribe_messages', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id', 64)->default('')->comment('微信open_id');
            $table->string('template_id', 128)->default('')->comment('模板id');
            $table->boolean('status')->default(1)->comment('状态 1:未使用 2:已使用');
            $table->string('data', 2000)->default('')->comment('发送模板消息内容');
            $table->string('result')->default('')->comment('发送结果');
            $table->dateTime('used_time')->nullable()->comment('使用时间');
            $table->timestamps();
            $table->index(['open_id'], 'idx_openid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('subscribe_messages');
    }
}
