<?php
/*
 * @Author: your name
 * @Date: 2021-06-01 00:19:38
 * @LastEditTime: 2021-06-01 00:20:00
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Models/VMedia.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VMedia extends Model
{
    protected $table      = "vmedia";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];


    const KIND_MOVIE = 1;
    const KIND_TV    = 2;

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
