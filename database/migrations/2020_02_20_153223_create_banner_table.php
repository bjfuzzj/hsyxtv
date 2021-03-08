<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBannerTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('banner', function (Blueprint $table) {
            $table->increments('id');
            $table->string('type',10)->default('')->comment('类型');
            $table->string('content',500)->default('')->comment('展示内容');
            $table->integer('clicktype')->default(0)->comment('点击效果0无点击效果,1点击跳转图片,2点击跳转链接');
            $table->string('clickcontent')->default('')->comment('点击效果的值');
            $table->timestamps();
            $table->index(['type'], 'idx_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('banner');
    }
}
