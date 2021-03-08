<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;

/**
 * Class WxappFormId
 * @package App\Models
 *
 * @property int id
 * @property string open_id 小程序open_id
 * @property string form_id 用于发送模板消息
 * @property string type 类型 FORM:表单 PAY:支付
 * @property int status 状态 1:未使用 2:使用中 3: 已使用
 * @property string data 发送模板消息内容
 * @property string result 发送结果
 * @property string used_time 使用时间
 * @property string validity_time 有效时间
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class WxappFormId extends Model
{
    protected $table = 'wxapp_formid';

    // 状态 1:未使用 2:使用中 3: 已使用
    const STATUS_NOT_USE = 1;
    const STATUS_IN_USE  = 2;
    const STATUS_USED    = 3;

    // 类型 FORM:表单 PAY:支付
    const TYPE_FORM = 'FORM';
    const TYPE_PAY  = 'PAY';
    public static $types = array(
        self::TYPE_FORM => '表单',
        self::TYPE_PAY  => '支付',
    );

    // 有效期时间
    const VALIDITY_DAY = 7;

    public static function add($openId, $formId, $type)
    {
        $self          = new self();
        $self->open_id = $openId;
        $self->form_id = $formId;
        $self->type    = $type;
        $self->status  = self::STATUS_NOT_USE;

        // 有效时间减少1个小时，避免formid刚好在这段时间内失效
        $self->validity_time = now()->addDays(self::VALIDITY_DAY)->addHours(-1)->toDateTimeString();
        $self->save();
        return $self;
    }

    public static function getFormId($openId)
    {
        if (empty($openId)) {
            return null;
        }

        $form = self::where('open_id', $openId)->where('validity_time', '>', now()->toDateTimeString())
            ->where('status', self::STATUS_NOT_USE)->first();
        if (!$form instanceof self) {
            return null;
        }

        $form->status = self::STATUS_IN_USE;
        $form->save();
        return $form;
    }

    public function saveResult($sendData, $result)
    {
        $this->status = self::STATUS_USED;
        $this->data   = json_encode($sendData, JSON_UNESCAPED_UNICODE);
        $this->result = json_encode($result, JSON_UNESCAPED_UNICODE);
        return $this->save();
    }

}
