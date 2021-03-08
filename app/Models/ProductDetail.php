<?php

namespace App\Models;

use App\Helper\Oss;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

/**
 * Class ProductDetail
 * @package App\Models
 *
 * @property int id
 * @property int product_id 产品ID
 * @property array content 内容
 * @property string create_time 创建时间
 * @property string update_time 更新时间
 */
class ProductDetail extends Model
{
    protected $table = 'product_details';

    const CREATED_AT = 'create_time';
    const UPDATED_AT = 'update_time';

    const TYPE_TEXT  = '1'; //文本
    const TYPE_IMAGE = '2'; //图片

    public function getContentAttribute($value)
    {
        return !empty($value) ? json_decode($value, true) : [];
    }

    public static function updateDetail($productId, $images)
    {
        $contents = [];
        foreach ($images as $image) {
            $contents[] = ['type' => self::TYPE_IMAGE, 'content' => $image];
        }

        $detail = self::where('product_id', $productId)->first();
        if (!$detail instanceof self) {
            $detail             = new self();
            $detail->product_id = $productId;
        }
        $detail->content = json_encode($contents, JSON_UNESCAPED_UNICODE);
        $detail->save();
        return $detail;
    }

    public static function getImages($productId)
    {
        $detail = self::where('product_id', $productId)->first();
        if (!$detail instanceof self) {
            return false;
        }

        $images = [];
        foreach ($detail->content as $item) {
            if (isset($item['type']) && isset($item['content']) && self::TYPE_IMAGE == $item['type']) {
                $images[] = $item['content'];
            }
        }
        return $images;
    }

}
