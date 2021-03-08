<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductAttributeOption
 * @package App\Models
 *
 * @property int id
 * @property int attribute_id 属性ID
 * @property string name 选项名称
 * @property int creator_id 创建者ID
 * @property string created_at 创建时间
 */
class ProductAttributeOption extends Model
{
    const UPDATED_AT = null;

    protected $table = 'product_attribute_options';

    public static function add($attrId, $name, $creatorId)
    {
        $self               = new self();
        $self->attribute_id = $attrId;
        $self->name         = $name;
        $self->creator_id   = $creatorId;
        $self->save();
        return $self;
    }

}
