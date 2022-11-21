<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-11-20 23:52:48
 * @LastEditTime: 2022-11-20 23:53:09
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Models/LiveLanMu.php
 * 寻找内心的安静
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LiveLanMu extends Model
{
    protected $table      = "lanmu";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';

    const STATUS_ONLINE = 1;
    const STATUS_OFFLINE = 0;
}
