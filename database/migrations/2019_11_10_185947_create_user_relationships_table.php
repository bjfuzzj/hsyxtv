<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserRelationshipsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_relationships', function (Blueprint $table) {
            $table->increments('id');
            $table->unsignedBigInteger('parent_id')->default(0)->comment('上级分销员用户ID');
            $table->unsignedInteger('wx_user_id')->default(0)->comment('微信用户ID');
            $table->unsignedInteger('user_id')->default(0)->comment('用户ID，未成为分销员为0');
            $table->timestamps();
            $table->index(['parent_id'], 'idx_parent_id');
            $table->index(['user_id'], 'idx_user_id');
            $table->index(['wx_user_id'], 'idx_wx_user_id');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('user_relationships');
    }
}
