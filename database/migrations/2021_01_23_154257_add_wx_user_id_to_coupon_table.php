<?php
/*
 * @Author: your name
 * @Date: 2021-01-23 15:42:57
 * @LastEditTime: 2021-01-23 15:44:53
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2021_01_23_154257_add_wx_user_id_to_coupon_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddWxUserIdToCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->unsignedInteger('wx_user_id')->default(0)->comment('微信ID')->after('user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupons', function (Blueprint $table) {
            $table->dropColumn('wx_user_id');
        });
    }
}
