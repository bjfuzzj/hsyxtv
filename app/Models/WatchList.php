<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchList extends Model
{
    protected $table      = "watchlist";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    //userid
    //type 0 电影 1 电视剧
    //srcid 视频id
    //isdel 是否删除  1 删除 0 默认

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';


    const STATUS_NORMAL = 0;
    const STATUS_DEL    = 1;


    public function isDel()
    {
        return $this->isdel == self::STATUS_DEL;
    }

    public function isNormal()
    {
        return $this->isdel == self::STATUS_NORMAL;
    }

    public function setDel()
    {
        $this->isdel = self::STATUS_DEL;
        return $this->save();
    }

    public function setNormal()
    {
        $this->isdel = self::STATUS_NORMAL;
        return $this->save();
    }


}
