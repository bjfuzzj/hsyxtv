<?php
/*
 * @Author: bjfuzzj
 * @Date: 2021-12-20 15:57:20
 * @LastEditTime: 2021-12-20 15:59:24
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/LanMu.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LanMu extends Model
{
    protected $connection = 'mysql_web_47';
    protected $table      = "lanmu";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';

    const STATUS_ONLINE = 1;
    const STATUS_OFFLINE = 0;
}
