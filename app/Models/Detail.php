<?php
/*
 * @Author: your name
 * @Date: 2021-08-02 21:15:35
 * @LastEditTime: 2021-08-02 21:17:46
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Models/Detail.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Detail extends Model
{
    protected $connection = 'mysql_web';
    protected $table      = "detail";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
