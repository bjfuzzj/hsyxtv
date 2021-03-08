<?php

namespace App\Http\Controllers;

use App\Helper\Codec;
use App\Helper\PushMessage;
use App\Helper\WxPayment;
use App\Helper\WxPushMessage;
use App\Models\GXCoupon;
use App\Models\Order;
use App\Models\OrderLog;
use App\Models\OrderLogistics;
use App\Models\OrderPayRecord;
use App\Models\OrderRefundRecord;
use App\Models\Product;
use App\Models\User;
use App\Models\WxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Carbon;
use App\Models\Group;

class PayController extends Controller
{

    /**
     * @api {post} /api/pay/wx_pay 发起支付
     * @apiName wxPay
     * @apiGroup Pay
     *
     * @apiParam {String} _token 个人身份token
     * @apiParam {Number} order_id 订单ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Object} data.pay_data 支付数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function wxPay(Request $request)
    {
        $params = $this->validate($request, [
            '_token'   => 'required|string',
            'order_id' => 'required|integer',
        ], [
            '*' => '发起支付失败[-1]'
        ]);
        Log::info(__CLASS__ . ':' . __FUNCTION__, $params);

        $order = Order::find($params['order_id']);
        if (!$order instanceof Order) {
            return $this->outErrorResult(500, '发起支付失败[-2]');
        }

        $openId  = WxUser::getOpenId($params['_token']);
        $payData = $order->doPay($openId);
        if (empty($payData)) {
            return $this->outErrorResult(500, '发起支付失败[-3]');
        }
        return $this->outSuccessResultApi([
            'pay_data' => $payData
        ]);
    }

    // 微信支付回调
    public function wxPayBack()
    {
        return WxPayment::getInstance()->payBack(function ($data) {
            try {
                $payRecord = OrderPayRecord::where('out_trade_no', $data['out_trade_no'])->first();
                if (!$payRecord instanceof OrderPayRecord) {
                    Log::error('支付回调失败-支付记录不存在', $data);
                    return true;
                }
                $totalFee = round($data['total_fee'] / 100, 2);
                if (0 !== bccomp($payRecord->amount, $totalFee, 2)) {
                    Log::error('支付回调失败-支付金额不正确', $data);
                    return true;
                }
                $order = Order::find($payRecord->order_id);
                if (!$order instanceof Order) {
                    Log::error('支付回调失败-订单不存在', $data);
                    return true;
                }
                $product = Product::withTrashed()->find($order->product_id);
                if (!$product instanceof Product) {
                    Log::error('支付回调失败-产品不存在', $data);
                    return true;
                }

                $log = "支付记录：{$payRecord->id} 支付金额：{$payRecord->amount}";
                if (WxPayment::RESULT_SUCCESS == $data['result_code']) {
                    $log    = '支付成功 ' . $log;
                    $result = $payRecord->paySuccess($order, $product, $data);
                } else {
                    $log    = '支付失败 ' . $log;
                    $result = $payRecord->payFail($data);
                }
                if (!$result) {
                    Log::error('支付回调失败-处理结果失败', $data);
                    return true;
                }

                // 添加操作记录
                OrderLog::addLog($order->id, $log, OrderLog::TYPE_SYSTEM);

                $order->afterPaySuccess();
            } catch (\Exception $e) {
                Log::error('支付回调失败-异常 errMsg: ' . $e->getMessage(), $data);
                return true;
            }
            return true;
        });
    }

    /**
     * @api {get} /api/pay/success 支付成功
     * @apiName index
     * @apiGroup Pay
     *
     * @apiParam {Number} order_id 订单ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.product_token 产品Token
     * @apiSuccess {String} data.product_title 产品标题
     * @apiSuccess {Float} data.pay_amount 支付金额
     * @apiSuccess {String} data.share_title 分享标题
     * @apiSuccess {String} data.share_image 分享图片
     * @apiSuccess {String} data.show_popup 是否显示弹窗 0:不展示，1:展示
     * @apiSuccess {String} data.popup_text 弹窗文案
     * @throws \Illuminate\Validation\ValidationException
     */
    public function paySuccess(Request $request)
    {
        $params = $this->validate($request, [
            'order_id' => 'required|integer',
        ], [
            '*' => '加载数据失败[-1]'
        ]);

        $order = Order::with('product')->find($params['order_id']);
        if (!$order instanceof Order) {
            return $this->outErrorResultApi(500, '加载数据失败[-2]');
        }
        if (!$order->product instanceof Product) {
            return $this->outErrorResultApi(500, '加载数据失败[-3]');
        }
        $product = $order->product;
        $nickName = $order->user->nick_name??'';
        $data = [
            'product_token'  => Codec::encodeId($order->product_id),
            'product_title'  => $product->title,
            'product_type'   => $order->product_type,
            'group_id'       => $order->group_id,
            'pay_amount'     => sprintf('%.2f', $order->amount_payable),
            'share_title'    => $nickName.$product->share_title,
            'share_image'    => $product->share_image,
            'show_popup'     => 0,
            'popup_text'     => 0,
            'show_group_btn' => 0,
        ];
        if($product->isGroupType()){
            $groupId = $order->group_id;
            if(!empty($groupId)){
                $group = Group::find($groupId);
                if($group instanceof Group && $group->status == Group::STATUS_ING){
                    $data['show_group_btn'] = 1;
                }
            }
        }

        $orderCnt = Order::where('user_id', $order->user_id)
            ->whereNotIn('status', [Order::STATUS_CANCEL, Order::STATUS_EXPIRED, Order::STATUS_UNPAID])
            ->where('id', '<>', $order->id)
            ->where('product_type', Product::TYPE_NORMAL)
            ->count();
        $user = User::find($order->user_id);
        if ($product->isNormalSale() && $orderCnt > 0 && $user instanceof User && !$user->isDistributorUser()) {
            $data['show_popup'] = 1;
            $data['popup_text'] = "恭喜您成为泡泡伴侣的粉丝\n联系客服享受更多优惠";
        }
        return $this->outSuccessResultApi($data);
    }


     // 微信退款回调
     public function wxRefundBack()
     {
         return WxPayment::getInstance()->refundBack(function ($data) {
             try {
                 $refundRecord = OrderRefundRecord::where('out_refund_no', $data['out_refund_no'])->first();
                 if (!$refundRecord instanceof OrderRefundRecord) {
                     Log::error('退款回调失败-退款记录不存在', $data);
                     return true;
                 }
                 if (!$refundRecord->isRefundingStatus()) {
                     Log::error('退款回调失败-退款记录前置状态不正常', $data);
                     return true;
                 }
 
                 $refundFee = round($data['refund_fee'] / 100, 2);
                 if (0 !== bccomp($refundRecord->amount, $refundFee, 2)) {
                     Log::error('退款回调失败-退款金额不正确', $data);
                     return true;
                 }
 
                 $log = "退款记录：{$refundRecord->id} 退款金额：{$refundRecord->amount}";
                 if ('SUCCESS' == $data['refund_status']) {
                     $log    = '退款成功 ' . $log;
                     $result = $refundRecord->refundSuccess($data['success_time']);
                 } else {
                     $log    = '退款失败 ' . $log;
                     $result = $refundRecord->refundFail();
                 }
 
                 if (!$result) {
                     Log::error('退款回调失败-处理结果失败', $data);
                     return true;
                 }
                 Log::info($log);
                 return true;
             } catch (\Exception $e) {
                 Log::error('退款回调失败 errMsg: ' . $e->getMessage() . ' data:' . json_encode($data, JSON_UNESCAPED_UNICODE));
                 return true;
             }
         });
     }

}
