<?php

namespace App\Helper;

use EasyWeChat;
use Log;

class WxPayment
{
    const RETURN_SUCCESS = 'SUCCESS';
    const RETURN_FAIL    = 'FAIL';
    const RESULT_SUCCESS = 'SUCCESS';
    const RESULT_FAIL    = 'FAIL';

    private $app;

    private static $instance;

    public static function getInstance()
    {
        if (!self::$instance instanceof self) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    public function __construct()
    {
        $this->app = EasyWeChat::payment();
    }

    /**
     * 是否是沙箱模式
     * @return mixed
     */
    public function inSandbox()
    {
        return $this->app->inSandbox();
    }

    /**
     * 统一下单
     *
     * @param string $body 商品描述
     * @param string $outTradeNo 商户订单号
     * @param float $totalFee 订单金额
     * @param string $openId 用户openid
     * @return mixed
     * @throws \RuntimeException
     */
    public function unify($body, $outTradeNo, $totalFee, $openId)
    {
        $data = array(
            'body'         => $body,
            'openid'       => $openId,
            'out_trade_no' => $outTradeNo,
            'total_fee'    => $totalFee,
            'trade_type'   => 'JSAPI',
        );

        $this->addLog('提交统一下单', $data);
        $result = $this->app->order->unify($data);
        if (!$this->isSuccess($result)) {
            $this->addLog('统一下单返回结果失败', $result);
            throw new \RuntimeException('统一下单返回结果失败');
        }
        $this->addLog('统一下单返回结果成功', $result);
        return $result;
    }

    /**
     * 获取小程序支付数据
     * @param $prepayId
     * @return array
     */
    public function getMiniProgramPayData($prepayId)
    {
        $this->addLog('获取支付数据', $prepayId);
        $payData = $this->app->jssdk->bridgeConfig($prepayId);
        $this->addLog('支付数据结果', $payData);
        return $payData;
    }

    /**
     * 支付回调
     * @param \Closure $callback
     * @return mixed
     */
    public function payBack(\Closure $callback)
    {
        $response = $this->app->handlePaidNotify(function ($data, $fail) use ($callback) {
            $this->addLog('支付回调', $data);
            if (!isset($data['return_code']) || self::RETURN_SUCCESS != $data['return_code']) {
                return $fail('通信失败，请稍后再通知我');
            }

            $callback($data);
            return true;
        });
        return $response;
    }

    /**
     * 发起退款
     * @param string $outRefundNo 商户退款单号
     * @param float $totalFee 订单总金额
     * @param float $refundFee 退款金额
     * @param string $transactionId 微信支付订单号
     * @param string $outTradeNo 商户支付订单号
     * @param string $refund_desc 退款原因
     * @throws \RuntimeException
     */
    public function refund($outRefundNo, $totalFee, $refundFee, $transactionId = '', $outTradeNo = '', $refund_desc = '')
    {
        $params = \func_get_args();
        $this->addLog('发起退款', $params);
        if (empty($transactionId) && empty($outTradeNo)) {
            $this->addLog('发起退款缺少参数', $params);
            throw new \RuntimeException('缺少参数【微信订单号或商户订单号】');
        }
        $notifyUrl = env('WECHAT_PAYMENT_REFUND_NOTIFY_URL');
        if (empty($notifyUrl)) {
            $this->addLog('发起退款缺少回调地址', $params);
            throw new \RuntimeException('缺少参数【回调地址】');
        }
        $config = [
            'notify_url'  => $notifyUrl,
            'refund_desc' => $refund_desc
        ];
        if (!empty($transactionId)) {
            $result = $this->app->refund->byTransactionId($transactionId, $outRefundNo, $totalFee, $refundFee, $config);
        } else {
            $result = $this->app->refund->byOutTradeNumber($outTradeNo, $outRefundNo, $totalFee, $refundFee, $config);
        }
        if (!$this->isSuccess($result)) {
            $this->addLog('退款返回结果失败', $result);
            throw new \RuntimeException('退款返回结果失败');
        }
        $this->addLog('发起退款返回结果', $result);
        return $result;
    }

       /**
     * 退款回调
     * @param \Closure $callback
     * @return mixed
     */
    public function refundBack(\Closure $callback)
    {
        $response = $this->app->handleRefundedNotify(function ($data, $reqInfo, $fail) use ($callback) {
            Log::info('退款回调数据' . json_encode($data, JSON_UNESCAPED_UNICODE));
            Log::info('退款回调解密后数据' . json_encode($reqInfo, JSON_UNESCAPED_UNICODE));
            if (!isset($data['return_code']) || self::RETURN_SUCCESS != $data['return_code']) {
                return $fail('通信失败，请稍后再通知我');
            }

            $callback($reqInfo);
            return true;
        });
        return $response;
    }
    
    /**
     * 企业付款到零钱
     * @param string $partnerTradeNo 商户订单号，需保持唯一性(只能是字母或者数字，不能包含有符号)
     * @param string $openId 用户openid
     * @param int $amount 企业付款金额，单位分
     * @param string $desc 企业付款操作说明信息。必填
     * @return array
     */
    public function transferToBalance($partnerTradeNo, $openId, $amount, $desc)
    {
        //NO_CHECK：不校验真实姓名, FORCE_CHECK：强校验真实姓名
        $data = [
            'partner_trade_no' => $partnerTradeNo,
            'openid'           => $openId,
            'amount'           => $amount,
            'desc'             => $desc,
            'check_name'       => 'NO_CHECK'
        ];

        $this->addLog('企业付款到零钱', $data);
        $result = $this->app->transfer->toBalance($data);
        $this->addLog('企业付款到零钱结果', $result);
        return $result;
    }

    private function isSuccess($result)
    {
        return isset($result['return_code']) && WxPayment::RETURN_SUCCESS == $result['return_code']
            && isset($result['result_code']) && WxPayment::RESULT_SUCCESS == $result['result_code'];
    }

    private function addLog($tag, $msg)
    {
        if (!is_string($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        Log::info("微信支付-[{$tag}]  [PID]:" . getmypid() . ' ' . $msg);
    }

}
