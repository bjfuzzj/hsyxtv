<?php
/*
 * @Author: your name
 * @Date: 2020-10-24 18:55:35
 * @LastEditTime: 2020-12-22 21:37:59
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /sg/songguo/app/Models/CouponTemplateProductRef.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class CouponTemplateProductRef
 * @package App\Models
 *
 * @property int id ID
 * @property int template_id 优惠券模板ID
 * @property int product_id 商品ID
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 * @property string deleted_at 删除时间
 */
class CouponTemplateProductRef extends Model
{
    use SoftDeletes;

    protected $table = 'coupon_template_product_ref';

    public static function create($productId, $templateId){
        $couponTemplateProductRef = new self();
        $couponTemplateProductRef->template_id = $templateId;
        $couponTemplateProductRef->product_id = $productId;
        return $couponTemplateProductRef->save();
    }

}
