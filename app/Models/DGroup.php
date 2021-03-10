<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DGroup extends Model
{
    const DEFAULT_ID = 1;
    protected $table      = "dgroup";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
