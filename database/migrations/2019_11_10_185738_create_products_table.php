<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateProductsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('products', function (Blueprint $table) {
            $table->increments('id')->comment('产品ID');
            $table->string('title')->default('')->comment('产品名称');
            $table->tinyInteger('type')->unsigned()->default(1)->comment('产品类型, 1:预售 2:正常 ');
            $table->tinyInteger('status')->unsigned()->default(1)->comment('产品状态, 1:待发布, 2:已上架, 3:已下架');
            $table->string('images', 1000)->default('')->comment('产品图');
            $table->string('detail_images', 2000)->default('')->comment('详情图片');
            $table->decimal('price', 10, 2)->default(0.00)->comment('价格');
            $table->decimal('original_price', 10, 2)->default(0.00)->comment('原价');
            $table->decimal('cost', 10, 2)->default(0.00)->comment('成本');
            $table->integer('stock')->unsigned()->default(0)->comment('库存');
            $table->integer('rank')->unsigned()->default(0)->comment('排序rank值');
            $table->integer('buy_min_num')->unsigned()->default(0)->comment('单次购买最少数量');
            $table->integer('buy_max_num')->unsigned()->default(0)->comment('单次购买最大数量');
            $table->integer('buy_step')->unsigned()->default(0)->comment('加减数量步数');
            $table->integer('buy_limit_num')->unsigned()->default(0)->comment('每人限购数量');
            $table->integer('sold_num')->unsigned()->default(0)->comment('销量');
            $table->string('tags')->default('')->comment('产品描述标签');
            $table->timestamp('advance_end_time')->nullable()->comment('预售结束时间');
            $table->string('share_title')->default('')->comment('分享标题');
            $table->string('share_image')->default('')->comment('小程序分享图');
            $table->string('share_poster')->default('')->comment('小程序海报');
            $table->timestamps();
            $table->softDeletes();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('products');
    }
}
