<?php
/*
 * @Author: bjfuzzj
 * @Date: 2021-03-09 21:09:24
 * @LastEditTime: 2024-08-01 15:53:27
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/DGroup.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DGroup extends Model
{
    const DEFAULT_ID = 1;
    const HUBEI_GROUP_ID = 23;
    const ANGE_GROUP_ID = 77;
    protected $table      = "dgroup";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const ALONE_INDEX   = 2;//单独首页
    const DEFAULT_INDEX = 1;//单独首页

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';


    public function isAloneIndex()
    {
        return self::ALONE_INDEX == $this->alone_index;
    }
}
