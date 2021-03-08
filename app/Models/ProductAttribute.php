<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * Class ProductAttribute
 * @package App\Models
 *
 * @property int id
 * @property string name 属性名称
 * @property int creator_id 创建者ID
 * @property string created_at 创建时间
 */
class ProductAttribute extends Model
{
    const UPDATED_AT = null;

    protected $table = 'product_attributes';

    public static function add($name, $creatorId)
    {
        $self             = new self();
        $self->name       = $name;
        $self->creator_id = $creatorId;
        $self->save();
        return $self;
    }

}
