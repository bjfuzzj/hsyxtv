<?php
/*
 * @Author: your name
 * @Date: 2020-12-02 18:58:44
 * @LastEditTime: 2020-12-07 21:03:18
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Helper/AliSms.php
 */

namespace App\Helper;

use Overtrue\EasySms\EasySms;
use Illuminate\Support\Facades\Log;

/**
 * 阿里云发送短信
 * Class AliSms
 * @package App\Helper
 */
class AliSms
{
    private $sms;
    const TEMP_ID_ACCOUNT_CHANGE = 'SMS_181495602'; // 账户余额变动


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
        $config = config('easy_sms');
        if (empty($config)) {
            throw new \RuntimeException('阿里云短信配置不正确');
        }

        $this->sms = new EasySms($config);
    }

    private function send($mobile, $data)
    {
        $rest = $this->sms->send($mobile, $data);
        if (empty($rest['aliyun']['status']) || 'success' != $rest['aliyun']['status']) {
            Log::error('阿里云短信发送异常 rest:' . print_r($rest, 1), \func_get_args());
            return false;
        }
        return true;
    }

    /*
     * 账户余额变动
     * 您有一条新${amount}元的收入，请进入“一起同柑”小程序查看。
     */
    public function sendAccountChange($mobile, $amount)
    {
        return $this->send($mobile, [
            'content'  => sprintf('您有一条新%s元的收入，请进入“泡泡伴侣”小程序查看。', $amount),
            'template' => self::TEMP_ID_ACCOUNT_CHANGE,
            'data'     => [
                'amount' => $amount
            ]
        ]);
    }
}
