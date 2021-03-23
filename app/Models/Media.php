<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
    protected $table      = "media";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];


    const KIND_MOVIE = 1;
    const KIND_TV    = 2;


//#1,电影
//#2,电视剧

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
