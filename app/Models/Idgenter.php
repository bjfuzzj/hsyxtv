<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Idgenter extends Model
{
    protected $table = 'idgenter';


    public static function getId()
    {
        return DB::table('idgenter')->insertGetId(
            ['created_at' => date('Y-m-d H:i:s')]
        );
    }
}
