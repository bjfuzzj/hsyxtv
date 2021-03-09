<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DGroup extends Model
{
    const DEFAULT_ID = 1;
    protected $table      = "dgroup";
    protected $primaryKey = 'd_id';
}
