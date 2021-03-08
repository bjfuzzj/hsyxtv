<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class SubscribeMessage
 * @package App\Models
 *
 * @property int id
 * @property string open_id 小程序open_id
 * @property string template_id 用于发送模板消息
 * @property int status 状态 1:未使用 2:已使用
 * @property string data 发送模板消息内容
 * @property string result 发送结果
 * @property string used_time 使用时间
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class SubscribeMessage extends Model
{
    protected $table = 'subscribe_messages';

    // 状态 1:未使用 2: 已使用
    const STATUS_NOT_USE = 1;
    const STATUS_USED    = 2;

    public static function batchAdd($openId, $tmplIds)
    {
        $data = [];
        $now  = now()->toDateTimeString();
        foreach ($tmplIds as $tmplId) {
            $data[] = [
                'open_id'     => $openId,
                'template_id' => $tmplId,
                'status'      => self::STATUS_NOT_USE,
                'created_at'  => $now,
                'updated_at'  => $now
            ];
        }
        return self::insert($data);
    }

    public static function get($openId)
    {
        if (empty($openId)) {
            return null;
        }

        $form = self::where('open_id', $openId)->where('status', self::STATUS_NOT_USE)->first();
        if (!$form instanceof self) {
            return null;
        }

        $form->status = self::STATUS_USED;
        $form->save();
        return $form;
    }

    public function saveResult($sendData, $result)
    {
        $this->data   = json_encode($sendData, JSON_UNESCAPED_UNICODE);
        $this->result = json_encode($result, JSON_UNESCAPED_UNICODE);
        return $this->save();
    }

}
