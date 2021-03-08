<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

/**
 * 分销百分比配置
 * Class DistributorPercentage
 * @package App
 *
 * @property int id
 * @property string key 分销比例Key
 * @property int percentage 分销返利百分比
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class DistributorPercentage extends Model
{
    protected $table = 'distributor_percentages';

    // 不同等级分享者返利百分比
    const KEY_FIRST_DISTRIBUTOR_SPREAD  = 'first_distributor_spread';
    const KEY_SECOND_DISTRIBUTOR_SPREAD = 'second_distributor_spread';

    // 上级分享者返利百分比
    const KEY_PARENT_DISTRIBUTOR_REBATE = 'parent_distributor_rebate';

    public static function getPercentage($key)
    {
        $percentage = self::where('key', $key)->value('percentage');
        return (int)$percentage;
    }

}
