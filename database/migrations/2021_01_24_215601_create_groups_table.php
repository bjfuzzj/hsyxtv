<?php
/*
 * @Author: your name
 * @Date: 2021-01-24 21:56:01
 * @LastEditTime: 2021-02-02 18:13:19
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2021_01_24_215601_create_groups_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateGroupsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('activity', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('活动名字');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('活动状态 0:失效,1:有效');
            $table->integer('product_id')->unsigned()->default(0)->comment('关联产品id');
            $table->integer('full_amount')->unsigned()->default(0)->comment('成团人数');
            $table->decimal('original_price', 10, 2)->default(0.00)->comment('原价');
            $table->decimal('price', 10, 2)->default(0.00)->comment('拼团价');
            $table->timestamp('buy_start_time')->nullable()->comment('拼团活动开始时间');
            $table->timestamp('buy_end_time')->nullable()->comment('拼团活动结束时间');
            $table->integer('valid_day')->unsigned()->default(0)->comment('成团有效时间天数');
            $table->timestamps();
        });
        
        Schema::create('group_activity', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->unsigned()->default(0)->comment('活动ID');
            $table->unsignedInteger('founder_id')->default(0)->comment('创建人user_id');
            $table->tinyInteger('status')->unsigned()->default(0)->comment('成团状态0:待成团,1:拼团中 2:拼团成功 3:拼团失败');
            $table->integer('product_id')->unsigned()->default(0)->comment('关联产品id');
            $table->integer('full_amount')->unsigned()->default(0)->comment('已拼人数');
            $table->decimal('price', 10, 2)->default(0.00)->comment('拼团价');
            $table->timestamp('start_time')->nullable()->comment('拼团活动开始时间');
            $table->timestamp('end_time')->nullable()->comment('拼团活动结束时间');
            $table->timestamps();
        });
        Schema::create('group_team', function (Blueprint $table) {
            $table->increments('id');
            $table->integer('activity_id')->unsigned()->default(0)->comment('活动ID');
            $table->integer('group_id')->default(0)->comment('拼团活动id');
            $table->bigInteger('order_id')->default(0)->comment('订单ID');
            $table->integer('product_id')->unsigned()->default(0)->comment('关联产品id');
            $table->integer('user_id')->unsigned()->default(0)->comment('用户id');
            $table->integer('role')->unsigned()->default(0)->comment('角色 0:普通 1:团长');
            $table->index(['group_id'], 'idx_group_id');
            $table->index(['product_id'], 'idx_product_id');
            $table->timestamps();
        });

        Schema::table('orders', function (Blueprint $table) {
            $table->integer('group_id')->default(0)->comment('活动id')->after('product_type');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('activity');
        Schema::dropIfExists('group_activity');
        Schema::dropIfExists('group_team');
        
    }
}
