<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Arr;

/**
 * Class ProductSku
 * @package App\Models
 *
 * @property int id
 * @property int product_id 产品ID
 * @property string name sku名称
 * @property string image sku图片
 * @property float price 价格
 * @property float stock 库存
 * @property string option_key 选项key值
 * @property string data sku数据
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 * @property string deleted_at 删除时间
 */
class ProductSku extends Model
{
    use SoftDeletes;

    protected $table = 'product_sku';

    // 是否删除 0:否 1:是
    const DELETE_NO  = 0;
    const DELETE_YES = 1;

    public static function batchAdd($productId, $skuList)
    {
        $hasIds = self::where('product_id', $productId)->pluck('id', 'option_key')->toArray();

        $insertData = [];
        $updateData = [];
        foreach ($skuList as $sku) {
            $sku = json_decode($sku);

            $optionKeys = [];
            $nameArr    = [];
            if (isset($sku->data)) {
                foreach ($sku->data as $option) {
                    $optionKeys[] = $option->v_id;
                    $nameArr[]    = $option->v;
                }
            }

            $optionKey = implode('_', $optionKeys);
            $item      = [
                'product_id' => $productId,
                'name'       => implode('/', $nameArr),
                'image'      => $sku->image ?? '',
                'price'      => $sku->price ?? 0,
                'stock'      => $sku->stock ?? 0,
                'option_key' => implode('_', $optionKeys),
                'data'       => $sku->data ? json_encode($sku->data, JSON_UNESCAPED_UNICODE) : '',
                'updated_at' => now()->toDateTimeString(),
                'deleted_at' => null
            ];
            if (isset($hasIds[$optionKey])) {
                $updateData[$hasIds[$optionKey]] = $item;
                unset($hasIds[$optionKey]);
            } else {
                $item['created_at'] = now()->toDateTimeString();
                $insertData[]       = $item;
            }
        }

        if (!empty($insertData)) {
            self::insert($insertData);
        }
        if (!empty($updateData)) {
            foreach ($updateData as $id => $skuData) {
                self::where('id', $id)->update($skuData);
            }
        }
        if (!empty($hasIds)) {
            self::where('id', $hasIds)->delete();
        }
        return true;
    }

    public static function getAttributeList($productId)
    {
        $queryRes = self::where('product_id', $productId)->get();

        $attributeList = [];
        $skuList       = [];
        foreach ($queryRes as $sku) {
            $skuArr  = $sku->toArray();
            $options = json_decode($sku->data, true);
            foreach ($options as $idx => $option) {
                if (!isset($attributeList[$option['k_id']])) {
                    $attributeList[$option['k_id']] = [
                        'id'      => $option['k_id'],
                        'name'    => $option['k'],
                        'options' => [],
                    ];
                }
                $attributeList[$option['k_id']]['options'][$option['v_id']] = [
                    'id'    => $option['v_id'],
                    'name'  => $option['v'],
                    'image' => $option['img'],
                ];

                $key                  = $idx + 1;
                $skuArr["k{$key}_id"] = $option['k_id'];
                $skuArr["k{$key}"]    = $option['k'];
                $skuArr["v{$key}_id"] = $option['v_id'];
                $skuArr["v{$key}"]    = $option['v'];
            }
            $skuList[] = $skuArr;
        }

        $attributeList = array_values($attributeList);
        foreach ($attributeList as $key => $attribute) {
            $attributeList[$key]['options'] = array_values($attributeList[$key]['options']);
        }
        return compact('attributeList', 'skuList');
    }


}
