<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UpgradeConfig extends Model
{
    protected $table      = 'upgradeconfig';
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const STATUS_ONLINE  = 1;
    const STATUS_OFFLINE = 0;


    const DEL_TYPE_ALL     = 'delall';
    const DEL_TYPE_DEFAULT = 'default';

    const TYPE_ADD     = 1; //增加包
    const TYPE_DEL     = 2; //删除包
    const TYPE_IMG     = 3; //开机图
    const TYPE_VIDEO   = 4; //开机视频
    const TYPE_DEL_ALL = 5; //删除类型

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
