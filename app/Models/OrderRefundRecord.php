<?php

namespace App\Models;

use App\Helper\WxPayment;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

/**
 * Class OrderRefundRecord
 *
 * @package App\Models
 * @property int id 订单ID
 * @property int order_id 订单编号
 * @property int pay_record_id 支付记录ID
 * @property float amount 支付金额
 * @property string out_refund_no 订单退款流水号
 * @property string escrow_refund_no 第三方退款流水号
 * @property int refund_status 退款状态
 * @property string refund_time 退款时间
 * @property string created_at 创建时间
 * @property string update_at 更新时间
 */
class OrderRefundRecord extends Model
{
    protected $table = 'order_refund_records';

    protected $appends = ['status_desc'];

    // 状态：1:申请退款 2:退款中 3:退款完成 4:退款失败
    const STATUS_APPLY     = 1;
    const STATUS_REFUNDING = 2;
    const STATUS_REFUNDED  = 3;
    const STATUS_FAIL      = 4;
    public static $statusDesc = array(
        self::STATUS_APPLY     => '申请退款',
        self::STATUS_REFUNDING => '退款中',
        self::STATUS_REFUNDED  => '退款完成',
        self::STATUS_FAIL      => '退款失败',
    );

    public function order()
    {
        return $this->belongsTo(Order::class, 'order_id');
    }

    public function payRecord()
    {
        return $this->belongsTo(OrderPayRecord::class, 'pay_record_id');
    }

    public function getStatusDescAttribute()
    {
        return self::$statusDesc[$this->refund_status] ?? '';
    }

    // 生成交易流水号
    private static function generateTradeNo()
    {
        $max14Bit = 16383;
        $max24Bit = 16777215;
        $time     = substr(floor(microtime(true) * 1000), -7);
        $base     = decbin($max24Bit + $time);
        $base     .= str_pad(decbin(random_int(0, $max14Bit)), 14, '0', STR_PAD_LEFT);
        return date('YmdH') . (int)bindec($base);
    }

    // 创建退款记录
    public static function add(OrderPayRecord $payRecord, $amount)
    {
        $self                = new self();
        $self->order_id      = $payRecord->order_id;
        $self->pay_record_id = $payRecord->id;
        $self->amount        = $amount;
        $self->out_refund_no = self::generateTradeNo();
        $self->refund_status = self::STATUS_APPLY;
        $self->save();
        return $self;
    }

    // 发起退款
    public function doRefund()
    {
        $log = __CLASS__ . ':' . __FUNCTION__ . ' 发起退款 id:' . $this->id;
        Log::info($log);

        $order = Order::find($this->order_id);
        if (!$order instanceof Order) {
            Log::error($log . ' 订单不存在');
            return false;
        }

        $payRecord = OrderPayRecord::find($this->pay_record_id);
        if (!$payRecord instanceof OrderPayRecord) {
            Log::error($log . ' 支付记录不存在');
            return false;
        }

        DB::beginTransaction();
        try {
            $totalFee  = round($payRecord->amount * 100);
            $refundFee = round($this->amount * 100);

            $payRes = WxPayment::getInstance()->refund($this->out_refund_no, $totalFee, $refundFee, '', $payRecord->out_trade_no);
            if (empty($payRes['refund_id'])) {
                Log::error($log . ' 退款结果不正确  res:' . json_encode($payRes, JSON_UNESCAPED_UNICODE));
                return false;
            }

            $this->escrow_refund_no = $payRes['refund_id'];
            $this->refund_status    = self::STATUS_REFUNDING;
            $this->save();

            // 修改订单应退款金额
            $order->amount_refundable = bcadd($order->amount_refundable, $this->amount, 2);
            if (0 === bccomp($order->amount_refundable, $order->amount_paid, 2)) {
                $order->status = Order::STATUS_REFUND;
            }
            $order->save();

            // 部分退款，重新计算分销奖励；全部退款，则分销奖励置无效
            // if (bccomp($order->amount_paid, $order->amount_refundable, 2) > 0) {
            //     DistributorReward::recalculateByExpressOrder($order);
            // } else {
            //     DistributorReward::setInvalidByOrder($order->id, '订单已退款');
            // }

            // 部分退款，重新计算分销奖励；全部退款，则分销奖励置无效
            if (bccomp($order->amount_paid, $order->amount_refundable, 2) > 0) {
                DistributorDetail::recalculateByOrder($order);
            } else {
                DistributorDetail::setInvalidByOrder($order->id, '订单已退款');
            }

            DB::commit();
            return true;
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error($log . ' 异常 errMsg:' . $e->getMessage());
            return false;
        }
    }

    // 退款成功
    public function refundSuccess($refundTime)
    {
        $order = Order::find($this->order_id);
        if (!$order instanceof Order) {
            Log::error(__CLASS__ . ':' . __FUNCTION__ . ' 订单不存在 id:' . $this->id);
            return false;
        }

        DB::beginTransaction();
        try {

            $this->refund_status = self::STATUS_REFUNDED;
            $this->refund_time   = $refundTime;
            $this->save();

            $order->amount_refunded = bcadd($order->amount_refunded, $this->amount, 2);
            $order->save();

            DB::commit();
            return true;
        } catch (\Exception $e) {
            Log::error(__CLASS__ . ':' . __FUNCTION__ . ' 异常 id:' . $this->id . ' errMsg:' . $e->getMessage());
            DB::rollBack();
            return false;
        }
    }

    // 退款失败
    public function refundFail()
    {
        $this->refund_status = self::STATUS_FAIL;
        return $this->save();
    }

    public function isRefundingStatus()
    {
        return self::STATUS_REFUNDING == $this->refund_status;
    }

}
