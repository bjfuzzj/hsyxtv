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
    const TYPE_INDEX   = 6; //登录字段配置
    const TYPE_NOTICE  = 7; //消息通知配置

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';

    public function isIndexType()
    {
        return self::TYPE_INDEX == $this->type;
    }

    public function isAddType()
    {
        return self::TYPE_ADD == $this->type;
    }

    public function isDelType()
    {
        return self::TYPE_DEL == $this->type;
    }

    public function isImgType()
    {
        return self::TYPE_IMG == $this->type;
    }

    public function isVideoType()
    {
        return self::TYPE_VIDEO == $this->type;
    }

    public function isDelAllType()
    {
        return self::TYPE_DEL_ALL == $this->type;
    }
}
