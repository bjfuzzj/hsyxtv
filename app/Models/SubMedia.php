<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubMedia extends Model
{
    protected $table      = "sub_media";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
