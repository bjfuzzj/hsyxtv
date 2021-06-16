<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdTheme extends Model
{
    protected $table      = "ad_theme";
    protected $primaryKey = 'd_id';
    protected $guarded    = [];

    const UPDATED_AT = 'createdatetime';
    const CREATED_AT = 'savedatetime';
}
