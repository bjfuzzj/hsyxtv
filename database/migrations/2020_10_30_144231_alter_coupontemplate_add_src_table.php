<?php
/*
 * @Author: bjfuzzj
 * @Date: 2020-10-30 14:42:31
 * @LastEditTime: 2020-10-30 14:57:43
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /sg/songguo/database/migrations/2020_10_30_144231_alter_coupontemplate_add_src_table.php
 */

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCoupontemplateAddSrcTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('coupon_templates', function (Blueprint $table) {
            $table->string('src',300)->default('')->comment('图片地址')->after('receive_end_time');
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
            $table->dropColumn('src');
        });
    }
}
