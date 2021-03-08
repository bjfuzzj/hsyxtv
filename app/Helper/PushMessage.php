<?php

namespace App\Helper;

use App\Models\WxappFormId;
use Illuminate\Support\Facades\Log;

/**
 * 公众号发送模板消息
 * @package App\Helper
 */
class PushMessage
{
    // 后台模板ID
    const TEMP_ID_CASH_OUT_SUCCESS_NOTICE = '2symFtJM6f_TNViyNcj8m2ZL0vNBRk1XC4nPeQ18U_w'; // 提现成功通知
    const TEMP_ID_CASH_OUT_FAIL_NOTICE    = 'xQGfr5dzr5nVHjWkzyGV1qtwyEOddFs52LghhCuIstc'; // 提现失败通知


    // 前台模板ID
    const TEMP_ID_PAYMENT_SUCCESS_NOTICE     = 'Q9sJ6R1MuTCDH_JNJwFAc3GjlS7AHGMXVDXOpjRniXM'; // 订单支付成功通知
    const TEMP_ID_ORDER_STATUS_CHANGE_NOTICE = 'SM3rjqjJ4KZnf4kUKIEbPqhMJw7vEmgkOlkP2FCOkXY'; // 订单状态变动通知
    const TEMP_ID_ACCOUNT_CHANGE_REMIND      = 'rIdp-R5uByIUKoOxinDfQnfM_rSB2SJ01ohseRgbirM'; // 账户变动提醒

    // 通用字段颜色
    const FONT_COLOR_BLUE = '#576B96';

    // 尝试次数
    const TRY_NUM = 3;

    const PAGE_PRODUCT_DETAIL = 'pages/product/detail'; //产品详情
    const PAGE_ORDER_DETAIL   = 'pages/order/orderDetail'; //订单详情
    const PAGE_USER_INCOME    = 'pages/userIncome/userIncome'; //我的收入


    private static function send($templateId, $toUser, $page, $data, $emphasisKeyword = '')
    {
        try {
            $try = 0;
            while ($try++ < self::TRY_NUM) {
                $from = WxappFormId::getFormId($toUser);
                if (!$from instanceof WxappFormId) {
                    return true;
                }

                /**
                 * 40037    template_id不正确
                 * 41028    form_id不正确，或者过期
                 * 41029    form_id已被使用
                 * 41030    page不正确
                 * 45009    接口调用超过限额（目前默认每个帐号日调用限额为100万）
                 */
                $result = MiniProgram::getInstance()->send($templateId, $toUser, $page, $from->form_id,
                    $data, $emphasisKeyword);
                $from->saveResult(\func_get_args(), $result);
                if (41028 == $result['errcode'] || 41029 == $result['errcode']) {
                    continue;
                }
                return 0 == $result['errcode'];
            }
        } catch (\Throwable $e) {
            Log::error(__CLASS__ . '_' . __FUNCTION__ . ' errMsg:' . $e->getMessage(), \func_get_args());
        }
        return false;
    }

     /**
     * 订单支付成功通知
     *
     * @param string $toUser 接收者openid
     * @param string $page 小程序页面
     * @param string $orderNo 订单编号
     * @param string $productTitle 商品名称
     * @param string $price 订单金额
     * @param string $number 数量
     * @param string $payTime 支付时间
     * @param string $receiverAddress 收货地址
     * @return bool
     */
    public static function sendPaymentSuccessNotice($toUser, $page, $orderNo, $productTitle, $price, $number, $payTime, $receiverAddress)
    {
        $data = [
            'keyword1' => $orderNo,
            'keyword2' => $productTitle,
            'keyword3' => $price,
            'keyword4' => $number,
            'keyword5' => $payTime,
            'keyword6' => $receiverAddress
        ];
        return self::send(self::TEMP_ID_PAYMENT_SUCCESS_NOTICE, $toUser, $page, $data);
    }
    /**
     * 订单支付成功通知
     *
     * @param string $toUser 接收者openid
     * @param string $page 小程序页面
     * @param string $orderNo 订单编号
     * @param string $productTitle 商品名称
     * @param string $orderStatus 订单状态
     * @param string $desc 备注
     * @return bool
     */
    public static function sendOrderStatusChangeNotice($toUser, $page, $orderNo, $productTitle, $orderStatus, $desc)
    {
        $data = [
            'keyword1' => $orderNo,
            'keyword2' => $productTitle,
            'keyword3' => $orderStatus,
            'keyword4' => $desc,
        ];
        return self::send(self::TEMP_ID_ORDER_STATUS_CHANGE_NOTICE, $toUser, $page, $data);
    }

    /**
     * 账户变动提醒
     *
     * @param string $toUser 接收者openid
     * @param string $page 小程序页面
     * @param string $desc 备注
     * @param string $reason 变动原因
     * @return bool
     */
    public static function sendAccountChangeRemind($toUser, $page, $desc, $reason)
    {
        $data = [
            'keyword1' => $desc,
            'keyword2' => $reason,
        ];
        return self::send(self::TEMP_ID_ACCOUNT_CHANGE_REMIND, $toUser, $page, $data);
    }

    /**
     * 提现成功通知
     *
     * @param string $toUser 接收者openid
     * @param string $page 小程序页面
     * @param string $amount 提现金额
     * @param string $status 提现状态
     * @param string $type 到账类型
     * @return bool
     */
    public static function sendCashOutSuccessNotice($toUser, $page, $amount, $status, $type)
    {
        $data = [
            'keyword1' => $amount,
            'keyword2' => $status,
            'keyword3' => $type,
        ];
        return self::send(self::TEMP_ID_CASH_OUT_SUCCESS_NOTICE, $toUser, $page, $data, 'keyword1.DATA');
    }

    /**
     * 提现成功通知
     *
     * @param string $toUser 接收者openid
     * @param string $page 小程序页面
     * @param string $amount 提现金额
     * @param string $desc 备注
     * @return bool
     */
    public static function sendCashOutFailNotice($toUser, $page, $amount, $desc)
    {
        $data = [
            'keyword1' => $amount,
            'keyword2' => $desc,
        ];
        return self::send(self::TEMP_ID_CASH_OUT_FAIL_NOTICE, $toUser, $page, $data);
    }
}
