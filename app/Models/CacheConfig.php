<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CacheConfig extends Model
{
    protected $table      = 'cacheconfig';
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
