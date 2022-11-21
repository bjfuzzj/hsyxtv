<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-11-21 15:37:51
 * @LastEditTime: 2022-11-21 17:03:47
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/UserModuleRecord.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserModuleRecord extends Model
{
    protected $table      = "user_module_record";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';

    const SOURCE_TYPE_LOGO = 1; // 1:logo
    const SOURCE_TYPE_VIDEO = 2; // 2:视频
    const SOURCE_TYPE_BG = 3; // 3:背景


    const DEFAULT_BG = 'https://v.static.yiqiqw.com/pic/7a9218853e6ef44ad4aaff130b180204.jpg';


    
}
