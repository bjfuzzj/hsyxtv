<?php
/*
 * @Author: your name
 * @Date: 2020-12-22 17:32:45
 * @LastEditTime: 2020-12-22 18:01:01
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2020_12_22_173245_create_coupons_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCouponsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('coupon_templates', function (Blueprint $table) {
            $table->increments('id');
            $table->string('name')->default('')->comment('优惠券名称');
            $table->unsignedTinyInteger('type')->default(1)->comment('优惠券类型 1:直减 2:满减 3:折减');
            $table->unsignedTinyInteger('promote_type')->default(1)->comment('推广类型 1:平台 2:指定商品');
            $table->unsignedTinyInteger('status')->default(1)->comment('状态');
            $table->decimal('cut_value')->default(0.00)->comment('减多少；直减和满减为金额，折减为折扣');
            $table->decimal('cost')->default(0.00)->comment('消费多少金额可以使用，用于满减类型');
            $table->unsignedInteger('total')->default(0)->comment('优惠券数量');
            $table->unsignedInteger('used_cnt')->default(0)->comment('已领取优惠券数量');
            $table->unsignedInteger('person_limit_num')->default(0)->comment('每个人领取多少张');
            $table->unsignedTinyInteger('valid_type')->default(1)->comment('有效期类型 1：限定日期有效 2: 领取n天有效');
            $table->unsignedInteger('valid_day')->default(0)->comment('优惠券有效期天数');
            $table->timestamp('valid_start_time')->nullable()->comment('优惠券有效开始时间');
            $table->timestamp('valid_end_time')->nullable()->comment('优惠券有效结束时间');
            $table->timestamp('receive_start_time')->nullable()->comment('领券开始时间');
            $table->timestamp('receive_end_time')->nullable()->comment('领券结束时间');
            $table->string('creator_name')->default('')->comment('活动创建者名称');
            $table->unsignedInteger('creator_id')->default(0)->comment('活动创建者ID');
            $table->timestamps();
        });

        Schema::create('coupons', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID');
            $table->unsignedInteger('template_id')->default(0)->comment('优惠券模板ID');
            $table->unsignedTinyInteger('status')->default(0)->comment('状态 1:未使用 2:已使用 3:已失效');
            $table->string('name')->default('')->comment('优惠券名称');
            $table->unsignedTinyInteger('type')->default(1)->comment('优惠券类型 1:直减 2:满减 3:折减');
            $table->decimal('cut_value')->default(0.00)->comment('减多少；直减和满减为金额，折减为折扣');
            $table->decimal('cost')->default(0.00)->comment('消费多少金额可以使用，用于满减类型');
            $table->timestamp('valid_end_time')->nullable()->comment('有效截止时间');
            $table->string('invalid_cause')->default('')->comment('失效原因');
            $table->timestamp('use_time')->nullable()->comment('使用时间');
            $table->unsignedInteger('order_id')->default(0)->comment('订单ID');
            $table->timestamps();
            $table->index(['user_id'], 'idx_user_id');
            $table->index(['template_id'], 'idx_template_id');
        });

        Schema::create('coupon_template_product_ref', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedInteger('template_id')->default(0)->comment('优惠券模板ID');
            $table->unsignedInteger('product_id')->default(0)->comment('商品 ID');
            $table->timestamps();
            $table->softDeletes();
            $table->index(['template_id', 'product_id'], 'idx_template_product');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('coupon_templates');
        Schema::dropIfExists('coupons');
        Schema::dropIfExists('coupon_template_product_ref');
    }
}
