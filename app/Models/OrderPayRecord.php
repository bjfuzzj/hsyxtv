<?php

namespace App\Models;

use App\Helper\WxPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * 订单支付记录
 * Class OrderPayRecord
 * @package App
 *
 * @property int id
 * @property int order_id 订单ID
 * @property float amount 支付金额
 * @property string out_trade_no 订单支付流水号
 * @property string transaction_id 微信交易流水号
 * @property string wx_prepay_id 微信prepay_id
 * @property int trade_status 支付状态：1:未支付 2:支付中 3:已支付 4:支付失败
 * @property string trade_time 交易时间
 * @property string result 支付结果详情
 * @property string created_at 创建时间
 * @property string updated_at 更新时间
 */
class OrderPayRecord extends Model
{
    protected $table = 'order_pay_records';

    // 支付状态：1:未支付 2:支付中 3:已支付 4:支付失败
    const STATUS_UNPAID = 1;
    const STATUS_IN_PAY = 2;
    const STATUS_PAID   = 3;
    const STATUS_FAILED = 4;
    public static $tradeStatusDesc = [
        self::STATUS_UNPAID => '未支付',
        self::STATUS_IN_PAY => '支付中',
        self::STATUS_PAID   => '已支付',
        self::STATUS_FAILED => '支付失败',
    ];

    const MAX14BIT = 16383;
    const MAX24BIT = 16777215;

    public function changePayAmount($amount_payable){
        $this->amount = $amount_payable;
        $this->out_trade_no = self::generateTradeNo();
        $this->save();
    }

    // 生成交易流水号
    private static function generateTradeNo()
    {
        $time = substr(floor(microtime(true) * 1000), -7);
        $base = decbin(self::MAX24BIT + $time);
        $base .= str_pad(decbin(mt_rand(0, self::MAX14BIT)), 14, '0', STR_PAD_LEFT);
        return date('YmdH') . (int)bindec($base);
    }

    public static function add(Order $order, $userId=0)
    {
        $payAmount = round($order->amount_payable - $order->amount_paid, 2);
        $self               = new self();
        $self->order_id     = $order->id;
        $self->amount       = $payAmount;
        $self->pay_user_id  = $userId;
        $self->out_trade_no = self::generateTradeNo();
        $self->trade_status = self::STATUS_UNPAID;
        $self->save();
        return $self;
    }

    // 发起支付
    public function doPay($openId, $body)
    {
        try {
            $totalFee  = round($this->amount * 100);
            $wxPayment = WxPayment::getInstance();
            $payRes    = $wxPayment->unify($body, $this->out_trade_no, $totalFee, $openId);
            $payData   = $wxPayment->getMiniProgramPayData($payRes['prepay_id']);

            $this->trade_status = self::STATUS_IN_PAY;
            $this->wx_prepay_id = $payRes['prepay_id'] ?? '';
            $this->save();
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . ':' . __FUNCTION__ . ' 发起支付失败 errMsg:' . $e->getMessage());
            return [];
        }
        return $payData;
    }

    public function paySuccess(Order $order, Product $product, $data)
    {
        DB::beginTransaction();
        try {
            $this->trade_status   = self::STATUS_PAID;
            $this->trade_time     = $data['time_end'];
            $this->transaction_id = $data['transaction_id'];
            $this->result         = json_encode($data, JSON_UNESCAPED_UNICODE);
            $this->save();

            $order->amount_paid = bcadd($order->amount_paid, $this->amount, 2);
            $order->status      = Order::STATUS_PAID;
            $order->paid_time   = $data['time_end'];
            if ($order->user_id != $this->pay_user_id) {
                $order->behalf_pay_user_id = $this->pay_user_id;
            }
            $order->save();

            

            DB::commit();
        } catch (\Exception $e) {
            Log::error('修改支付成功失败 errMsg:' . $e->getMessage(), $data);
            DB::rollBack();
            return false;
        }
        try {
            $product->addSoldNum($order->buy_num);
        } catch (\Exception $e) {
            Log::error('修改支付成功失败-2 errMsg:' . $e->getMessage(), $data);
        }
        return true;
    }

    public function payFail($data)
    {
        $this->trade_status   = self::STATUS_FAILED;
        $this->trade_time     = $data['time_end'];
        $this->transaction_id = $data['transaction_id'];
        $this->result         = json_encode($data, JSON_UNESCAPED_UNICODE);
        return $this->save();
    }

}
