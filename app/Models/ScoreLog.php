<?php
/*
 * @Author: your name
 * @Date: 2021-02-01 23:24:23
 * @LastEditTime: 2021-02-01 23:59:27
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Models/ScoreLog.php
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ScoreLog extends Model
{
    
    protected $table = 'score_log';

    const TYPE_USER   = 1;
    const TYPE_SYSTEM = 2;
    const TYPE_ADMIN  = 3;

    public static function addLog($orderId,$userId,$score,$operatorType,$operatorId = 0,$operatorName,$desc){
        $log                = new self();
        $log->order_id      = $orderId;
        $log->user_id       = $userId;
        $log->score         = $score;
        $log->operator_type = $operatorType;
        $log->operator_id   = $operatorId;
        $log->operator_name = $operatorName;
        $log->desc          = $desc;
        $log->save();
        return $log->id;
    }
}
