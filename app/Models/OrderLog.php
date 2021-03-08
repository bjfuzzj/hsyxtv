<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 订单操作日志
 * Class OrderLog
 * @package App
 *
 * @property int id
 * @property int order_id 订单ID
 * @property int operator_type 操作人员类型 1:用户 2:系统 3:管理员
 * @property int operator_id 操作人员Id
 * @property string operator_name 操作人员姓名
 * @property string desc 备注
 * @property string created_at 创建时间
 */
class OrderLog extends Model
{
    protected $table = 'order_logs';

    const UPDATED_AT = null;

    // 操作人员类型 1:用户 2:系统 3:管理员
    const TYPE_USER   = 1;
    const TYPE_SYSTEM = 2;
    const TYPE_ADMIN  = 3;

    public static function addLog($orderId, $desc, $oprType, $oprId = 0, $oprName = '')
    {
        $self                = new self();
        $self->order_id      = $orderId;
        $self->operator_type = $oprType;
        $self->operator_id   = $oprId;
        $self->operator_name = $oprName;
        $self->desc          = $desc;
        $self->save();
        return $self;
    }

}
