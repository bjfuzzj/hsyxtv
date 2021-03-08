<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Supplier extends Model
{
    const UPDATED_AT = 'edit_time';
    const CREATED_AT = 'create_time';
    protected $table = 'supplier';

    const SETTLE_TYPE_1 = 1;//日结
    const SETTLE_TYPE_2 = 2;//周结
    const SETTLE_TYPE_3 = 3;//半月结
    const SETTLE_TYPE_4 = 4;//月结

    //交易方式
    const DEAL_TYPE_1 = 1; //银行转账
    const DEAL_TYPE_2 = 2; //信用卡

    //结算方式
    public static $settle_type_desc = [
        self::SETTLE_TYPE_1 => '日结',
        self::SETTLE_TYPE_2 => '周结',
        self::SETTLE_TYPE_3 => '半月结',
        self::SETTLE_TYPE_4 => '月结',
    ];

    //交易方式
    public static $deal_type_desc = [
        self::DEAL_TYPE_1 => '银行转账',
        self::DEAL_TYPE_2 => '信用卡',
    ];

    public static function getSupplierOptions()
    {
        return self::get(['name', 'id'])->toArray();
    }

}
