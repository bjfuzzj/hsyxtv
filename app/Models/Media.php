<?php
/*
 * @Author: bjfuzzj
 * @Date: 2021-03-23 17:02:51
 * @LastEditTime: 2022-05-15 10:28:30
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/Media.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table      = "media";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];


    const KIND_MOVIE = 1;
    const KIND_TV    = 2;


    const TYPE_1 = 1;//教育
    const TYPE_2 = 2;//党建
    const TYPE_3 = 3;//普法
    const TYPE_4 = 4;//医院


//#1,电影
//#2,电视剧

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
