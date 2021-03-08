<?php
/*
 * @Author: your name
 * @Date: 2021-02-01 22:06:47
 * @LastEditTime: 2021-02-01 22:43:01
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2021_02_01_220647_create_score_log_table.php
 */

use Doctrine\DBAL\Schema\Schema as SchemaSchema;
use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateScoreLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('score_log', function (Blueprint $table) {
            $table->increments('id');
            $table->bigInteger('order_id')->unsigned()->default(0)->comment('订单ID');
            $table->integer('user_id')->default(0)->comment('用户id');
            $table->integer('score')->default(0)->comment('数量');
            $table->boolean('operator_type')->default(0)->comment('操作人员类型 1:用户 2:系统 3:管理员');
            $table->integer('operator_id')->unsigned()->default(0)->comment('操作人员ID');
            $table->string('operator_name', 128)->default('')->comment('操作人员姓名');
            $table->string('desc')->comment('备注');
            $table->timestamp('created_at')->nullable()->comment('创建时间');
            $table->index(['order_id'], 'idx_order_id');
            $table->index(['user_id'], 'idx_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('score_log');
    }
}
