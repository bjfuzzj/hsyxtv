<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

/**
 * 订单物流
 * Class OrderLogistics
 * @package App
 *
 * @property int id
 * @property int order_id 订单ID
 * @property string receiver_name 收货人姓名
 * @property string receiver_mobile 收货人电话
 * @property string receiver_province 收货人省份
 * @property string receiver_city 收货人城市
 * @property string receiver_district 收货人地区
 * @property string receiver_address 收货人详细地址
 * @property int quantity 发货数量
 * @property string express_no 快递单号
 * @property string express_name 快递公司
 * @property int status 状态 1:待发货 2:已部分发货 3:已发货 4:确认收货
 * @property int is_default 是否是默认地址 1:是 0:否
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 * @property string deleted_at 删除时间
 */
class OrderLogistics extends Model
{
    use SoftDeletes;

    protected $table = 'order_logistics';

    protected $appends = ['status_desc'];

    //状态 1:待发货 2:已部分发货 3:已发货 4:确认收货 5:发货中
    const STATUS_WAIT      = 1;
    const STATUS_PART_SENT = 2;
    const STATUS_SENT      = 3;
    const STATUS_RECEIVED  = 4;
    const STATUS_SENDING   = 5;
    public static $statusDesc = [
        self::STATUS_WAIT      => '待发货',
        self::STATUS_PART_SENT => '已部分发货',
        self::STATUS_SENT      => '已发货',
        self::STATUS_RECEIVED  => '已收货',
        self::STATUS_SENDING   => '发货中'
    ];

    // 是否是默认地址
    const DEFAULT_YES = 1;
    const DEFAULT_NO = 1;
    public function getExpressNoAttribute($value)
    {
        return $value ? explode(',', $value) : [];
    }

    public function getStatusDescAttribute()
    {
        return self::$statusDesc[$this->status] ?? '';
    }

    public static function add($orderId, $params)
    {
        $self                    = new self();
        $self->order_id          = $orderId;
        $self->receiver_name     = $params['receiver_name'] ?? '';
        $self->receiver_mobile   = $params['receiver_mobile'] ?? '';
        $self->receiver_province = $params['receiver_province'] ?? '';
        $self->receiver_city     = $params['receiver_city'] ?? '';
        $self->receiver_district = $params['receiver_district'] ?? '';
        $self->receiver_address  = $params['receiver_address'] ?? '';
        $self->quantity          = $params['quantity'] ?? '';
        $self->express_no        = isset($params['express_no']) ? trim($params['express_no'], ',') : '';
        $self->express_name      = $params['express_name'] ?? '';

        if(!empty($self->express_no)){
            $sentNum = count(explode(',', $self->express_no));
        }else{
            $sentNum = 0;
        }
        if (0 == $sentNum) {
            $self->status = self::STATUS_WAIT;
        } else if ($sentNum < $self->quantity) {
            $self->status = self::STATUS_PART_SENT;
        } else {
            $self->status = self::STATUS_SENT;
        }
        $self->save();
        return $self;
    }

   
    public function getEncryptMobile()
    {
        if (empty($this->receiver_mobile)) {
            return '';
        }
        return substr_replace($this->receiver_mobile, '****', 3, 4);
    }
    public function getReceiverFullAddress()
    {
        return $this->receiver_province . $this->receiver_city . $this->receiver_district . $this->receiver_address;
    }

    public static function getSendQuantity($orderId)
    {
        return self::where('order_id', $orderId)->sum('quantity');
    }

    public function isCanDelete()
    {
        return self::STATUS_WAIT == $this->status;
    }

    public function updateData($data)
    {
        $this->express_no        = !empty($data['express_no']) ? $data['express_no'] : '';
        $this->express_name      = $data['express_name'] ?? '';
        $this->receiver_name     = $data['receiver_name'] ?? '';
        $this->receiver_mobile   = $data['receiver_mobile'] ?? '';
        $this->receiver_province = $data['receiver_province'] ?? '';
        $this->receiver_city     = $data['receiver_city'] ?? '';
        $this->receiver_district = $data['receiver_district'] ?? '';
        $this->receiver_address  = $data['receiver_address'] ?? '';
        if (!empty($data['quantity'])) {
            $this->quantity = $data['quantity'];
        }

        if (empty($this->express_no)) {
            $this->status = self::STATUS_WAIT;
        } else {
            if(is_array($this->express_no)){
                $sentNum  = count($this->express_no);
            }else{
                $sentNum = count(explode(',', $this->express_no));
            }
            if ($sentNum >= $this->quantity) {
                $this->status = self::STATUS_SENT;
            } else {
                $this->status = self::STATUS_PART_SENT;
            }
        }
        return $this->save();
    }

    public function setDefault()
    {
        $this->is_default = self::DEFAULT_YES;
        return  $this->save();
    }
}
