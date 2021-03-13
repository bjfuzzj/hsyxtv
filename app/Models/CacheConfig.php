<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheConfig extends Model
{
    protected $table      = 'cacheconfig';
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const DEL_TYPE_ALL     = 'delall';
    const DEL_TYPE_DEFAULT = 'default';

    const STATUS_ONLINE  = 1;
    const STATUS_OFFLINE = 0;

    const TYPE_ADD     = 1; //增加包
    const TYPE_DEL     = 2; //删除包
    const TYPE_DEL_ALL = 3; //删除类型
    const TYPE_LIMIT   = 4; //限速类型


    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';


    public function isAddType()
    {
        return self::TYPE_ADD == $this->type;
    }

    public function isDelType()
    {
        return self::TYPE_DEL == $this->type;
    }

    public function isDelAllType()
    {
        return self::TYPE_DEL_ALL == $this->type;
    }

    public function isLimitType()
    {
        return self::TYPE_LIMIT == $this->type;
    }


}
