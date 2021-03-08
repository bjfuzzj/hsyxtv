<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 19:03:38
 * @LastEditTime: 2020-12-03 22:38:24
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Helper/PushSubscribeMessage.php
 */

namespace App\Helper;

use App\Models\SubscribeMessage;
use Illuminate\Support\Facades\Log;

/**
 * 发送订单消息
 * @package App\Helper
 */
class PushSubscribeMessage
{

    // 模板ID
    const TEMP_ID_PAYMENT_SUCCESS_NOTICE  = 'w66q7bKcZSk3Aj1amED1yfOvO6hDGReTDDNtI5kDhNM'; // 付款成功通知
    const TEMP_ID_EXPRESS_DELIVERY_NOTICE = 'hZAVcSn6J5XBdT-8jf3SsBR2tG2wD7fRBaulZnl5HQo'; // 订单发货通知
    const TEMP_ID_CASHOUT_APPLY_NOTICE    = '7zY91KmZsE-anCthA34oWu8Lj9c3s3Nidd3Ze4qLx1k'; // 提现审核通知

    const PAGE_PRODUCT_DETAIL = 'pages/product/detail'; //产品详情
    const PAGE_ORDER_DETAIL   = 'pages/order/orderDetail'; //订单详情
    const PAGE_USER_INCOME    = 'pages/userIncome/userIncome'; //我的收入

    private static function send($templateId, $toUser, $page, $data)
    {
        try {
            $subscribeMessage = SubscribeMessage::get($toUser);
            if (!$subscribeMessage instanceof SubscribeMessage) {
                return true;
            }

            /**
             * 40003    touser字段openid为空或者不正确
             * 40037    订阅模板id为空不正确
             * 43101    用户拒绝接受消息，如果用户之前曾经订阅过，则表示用户取消了订阅关系
             * 47003    模板参数不准确，可能为空或者不满足规则，errmsg会提示具体是哪个字段出错
             * 41030    page路径不正确，需要保证在现网版本小程序中存在，与app.json保持一致
             */
            $result = MiniProgram::getInstance()->sendSubscribeMessage($templateId, $toUser, $page, $data);
            $subscribeMessage->saveResult(\func_get_args(), $result);
            return 0 == $result['errcode'];
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' errMsg:' . $e->getMessage(), \func_get_args());
        }
        return false;
    }

    // 订单支付成功通知
    public static function sendPaymentSuccessNotice($toUser, $page, $orderNo, $productTitle, $price, $desc)
    {
        /*
         * 商品名称{{thing3.DATA}}
         * 付款金额{{amount2.DATA}}
         * 订单号码{{character_string1.DATA}}
         * 温馨提示{{thing4.DATA}}
         */
        $data = [
            'thing3'            => ['value' => $productTitle],
            'amount2'           => ['value' => $price],
            'character_string1' => $orderNo,
            'thing7'            => $desc,
        ];
        return self::send(self::TEMP_ID_PAYMENT_SUCCESS_NOTICE, $toUser, $page, $data);
    }

}
