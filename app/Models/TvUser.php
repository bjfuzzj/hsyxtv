<?php
/*
 * @Author: bjfuzzj
 * @Date: 2021-03-08 14:10:27
 * @LastEditTime: 2022-03-10 11:30:10
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/TvUser.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvUser extends Model
{
    protected $table      = "tvuser";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';


    const WHITE_YES = 1;
    const WHITE_NO = 0;
}
