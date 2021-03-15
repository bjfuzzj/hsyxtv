<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WatchList extends Model
{
    protected $table      = "watchlist";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
