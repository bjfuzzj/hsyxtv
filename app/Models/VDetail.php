<?php
/*
 * @Author: your name
 * @Date: 2021-08-02 21:27:57
 * @LastEditTime: 2021-08-02 21:28:18
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Models/VDetail.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VDetail extends Model
{
    protected $connection = 'mysql_web';
    protected $table      = "vdetail";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
