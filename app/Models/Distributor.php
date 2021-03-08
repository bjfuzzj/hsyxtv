<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Distributor extends Model
{
    protected $table = "distributor";

    public function getUser(){
        return $this->hasOne('App\Models\User', 'user_id', 'user_id');
    }
}
