<?php
/*
 * @Author: your name
 * @Date: 2021-01-03 17:00:05
 * @LastEditTime: 2021-01-03 17:03:03
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/database/migrations/2021_01_03_170005_add_is_show_to_coupon_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddIsShowToCouponTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupon_templates', function (Blueprint $table) {
            $table->integer('is_show')->default(0)->comment('是否首页展示')->after('person_limit_num');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('coupon_templates', function (Blueprint $table) {
            $table->dropColumn('is_show');
        });
    }
}
