<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-11-02 23:48:55
 * @LastEditTime: 2022-11-03 00:03:43
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/TongJiData.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TongJiData extends Model
{
    protected $table      = "tongji_data";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const TYPE_1 = 1;//登录行为
    const TYPE_2 = 2;//

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';

    public static function addTongji($params)
    {
        $userId = $params['user_id']??0;
        $type = $params['type']??1;
        $tongji = new self();
        $tongji->user_id = $userId;
        $tongji->type = $type;
        return $tongji->save();
    }
}
