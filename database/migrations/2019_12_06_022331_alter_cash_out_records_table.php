<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AlterCashOutRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('cashout_records', function (Blueprint $table) {
            $table->integer('auditor_id')->unsigned()->default(0)->comment('审核员ID')->after('payment_time');
            $table->string('auditor_name')->default('')->comment('审核员名称')->after('auditor_id');
            $table->timestamp('audit_time')->nullable()->comment('审核时间')->after('auditor_name');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('cashout_records', function (Blueprint $table) {
            $table->dropColumn(['auditor_id', 'auditor_name', 'audit_time']);
        });
    }
}
