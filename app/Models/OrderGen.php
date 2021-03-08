<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Carbon\Carbon;

class OrderGen extends Model
{
    protected $table = 'order_gen';

    const UPDATED_AT = null;

    public static function generateOrderId()
    {
        $self = new self();
        $self->save();

        $idGenStart = env('ORDER_ID_GEN_START', 1396270102);
        $idGenStep  = env('ORDER_ID_GEN_STEP', 1);
        $timeSeed   = time() - $idGenStart;
        $timeSeed   = (int)($timeSeed / $idGenStep + $self->id);
        return date('ymd') . $timeSeed;
    }

}
