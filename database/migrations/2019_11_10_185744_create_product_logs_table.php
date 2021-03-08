<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductLogsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('product_logs', function (Blueprint $table) {
            $table->increments('id')->comment('自增ID');
            $table->integer('product_id')->unsigned()->default(0)->comment('产品ID');
            $table->integer('operator_id')->unsigned()->default(0)->comment('操作人员ID');
            $table->string('operator_name', 128)->default('')->comment('操作人员姓名');
            $table->string('desc')->comment('备注');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('product_logs');
    }
}
