<?php
/*
 * @Author: your name
 * @Date: 2020-12-28 14:44:02
 * @LastEditTime: 2020-12-28 14:44:37
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2020_12_28_144402_create_operator_log_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateOperatorLogTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('operator_logs', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('source_type')->default(0)->comment('来源对象类型');
            $table->unsignedBigInteger('source_id')->default(0)->comment('来源对象ID');
            $table->unsignedTinyInteger('oper_type')->default(1)->comment('操作人员类型 1:用户 2:系统 3:管理员');
            $table->unsignedInteger('oper_id')->default(0)->comment('操作人员ID');
            $table->string('desc', 2000)->default('')->comment('备注');
            $table->timestamp('created_at')->nullable();
            $table->index(['source_type', 'source_id'], 'idx_source');
            $table->index(['oper_type', 'oper_id'], 'idx_oper');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('operator_logs');
    }
}
