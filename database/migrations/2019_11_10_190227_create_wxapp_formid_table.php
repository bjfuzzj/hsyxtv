<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateWxappFormidTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('wxapp_formid', function (Blueprint $table) {
            $table->increments('id');
            $table->string('open_id', 64)->default('')->comment('微信open_id');
            $table->string('form_id', 128)->default('')->comment('formId 用于发送模板消息');
            $table->enum('type', array('FORM', 'PAY'))->default('FORM')->comment('类型 FORM:表单 PAY:支付');
            $table->boolean('status')->default(1)->comment('状态 1:有效 2:无效');
            $table->dateTime('validity_time')->nullable()->comment('有效时间');
            $table->string('data', 2000)->default('')->comment('发送模板消息内容');
            $table->string('result')->default('')->comment('发送结果');
            $table->dateTime('used_time')->nullable()->comment('使用时间');
            $table->timestamps();
            $table->index(['open_id', 'validity_time'], 'idx_openid');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('wxapp_formid');
    }
}
