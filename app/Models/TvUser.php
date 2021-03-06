<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TvUser extends Model
{
    protected $table      = "tvuser";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
