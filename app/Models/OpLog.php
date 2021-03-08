<?php
/*
 * @Author: your name
 * @Date: 2020-12-28 14:46:36
 * @LastEditTime: 2020-12-28 14:46:47
 * @LastEditors: your name
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Models/OpLog.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 操作日志表
 * Class OperatorLog
 * @package App\Models
 *
 * @property int id
 * @property int source_type 来源对象
 * @property int source_id 来源对象ID
 * @property string oper_type 操作员类型
 * @property int oper_id 操作人员ID
 * @property string desc 备注
 * @property string created_at 创建时间
 */
class OpLog extends Model
{
    protected $table = 'operator_logs';

    protected $appends = ['oper_type_desc'];

    /**
     * 不需要 updated_at 字段
     */
    const UPDATED_AT = null;

    // 操作人员类型 1:用户 2:管理员 99:系统
    const OPER_TYPE_USER   = 1;
    const OPER_TYPE_ADMIN  = 2;
    const OPER_TYPE_SYSTEM = 99;
    public static $operTypes = array(
        self::OPER_TYPE_USER   => '求美者',
        self::OPER_TYPE_SYSTEM => '系统',
        self::OPER_TYPE_ADMIN  => '管理员',
    );

    public function getOperTypeDescAttribute()
    {
        return self::$operTypes[$this->oper_type] ?? '';
    }

    public static function add($sourceType, $sourceId, $desc, $operType, $operId = 0)
    {
        $self              = new self();
        $self->source_type = $sourceType;
        $self->source_id   = $sourceId;
        $self->desc        = $desc;
        $self->oper_type   = $operType;
        $self->oper_id     = $operId;
        $self->save();
        return $self;
    }

}
