<?php

namespace App\Helper;

use EasyWeChat;
use Illuminate\Support\Facades\Log;

class MiniProgram
{
    private $app;

    const PAGE_PRODUCT_HOME   = 'pages/index/index';
    const PAGE_PRODUCT_DETAIL = 'pages/product/detail';
    const PAGE_GROUP_DETAIL   = 'pages/group/detail';

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
        $this->app = EasyWeChat::miniProgram();
    }

    public function getAccessToken()
    {
        return $this->app->access_token->getToken();
    }

    /**
     * 强制更新access_token
     * @return mixed
     */
    public function updateAccessToken()
    {
        return $this->app->access_token->getToken(true);
    }

    /**
     * 登录
     * @param $code
     * @return mixed
     */
    public function login($code)
    {
        return $this->app->auth->session($code);
    }

    /**
     * 消息解密
     */
    public function decryptData($session, $iv, $encryptData)
    {
        return $this->app->encryptor->decryptData($session, $iv, $encryptData);
    }

    /**
     * 生成小程序码，可接受页面参数较短，生成个数不受限。
     * @param string $scene 页面参数，最大32个可见字符，只支持数字，大小写英文以及部分特殊字符：!#$&'()*+,/:;=?@-._~
     * @param string $page 小程序页面路径，默认为小程序首页
     * @param array $optional 其他配置项，例如width
     * @param bool $uploadImg 是否上传图片到云服务器, 默认上传到云服务器
     * @return string 图片上传到云服务则返回图片链接，不上传返回本地存储的临时路径
     */
    public function getWxACodeUnLimit($scene, $page = '', array $optional = [], $uploadImg = true)
    {
        if (!empty($page)) {
            $optional['page'] = $page;
        }
        $response = $this->app->app_code->getUnlimit($scene, $optional);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            try {
                $filePath = env('TEMP_UPLOAD_PATH', '/tmp/upload/');
                $filename = $response->saveAs($filePath, uniqid(__FUNCTION__, false) . '.png');

                $file = $filePath . $filename;
                if (!$uploadImg) {
                    return $file;
                }
                $oss  = new Oss(OSS::BUCKET_NAME_YIQITONGGAN);
                $rest = $oss->localUpload($file, $file);
                return isset($rest['info']['url']) ? Oss::addImageStyle(Oss::STYLE_NAME_DEFAULT, $rest['info']['url']) : '';
            } catch (\Throwable $e) {
                $this->errorLog(__FUNCTION__, '保存图片失败 errorMsg:' . $e->getMessage());
            }
        }
        return '';
    }




    /**
     * 生成小程序码
     * 接口A: 适用于需要的码数量较少的业务场景
     * @param string $page 小程序页面路径，默认为小程序首页
     * @param array $optional 其他配置项，例如width
     * @param bool $uploadImg 是否上传图片到云服务器, 默认上传到云服务器
     * @return string 图片上传到云服务则返回图片链接，不上传返回本地存储的临时路径
     */
    public function getWxACode($page = '', array $optional = [], $uploadImg = true)
    {
        $response = $this->app->app_code->get($page, $optional);
        if ($response instanceof \EasyWeChat\Kernel\Http\StreamResponse) {
            try {
                $filePath = env('TEMP_UPLOAD_PATH', '/tmp/upload/');
                $filename = $response->saveAs($filePath, uniqid(__FUNCTION__, false) . '.png');

                $file = $filePath . $filename;
                if (!$uploadImg) {
                    return $file;
                }
                $oss  = new Oss(OSS::BUCKET_NAME_YIQITONGGAN);
                $rest = $oss->localUpload($file, $file);
                return isset($rest['info']['url']) ? Oss::addImageStyle(Oss::STYLE_NAME_DEFAULT, $rest['info']['url']) : '';
            } catch (\Throwable $e) {
                $this->errorLog(__FUNCTION__, '保存图片失败 errorMsg:' . $e->getMessage());
            }
        }
        return '';
    }

    public function send($templateId, $toUser, $page, $formId, $data, $emphasisKeyword = '')
    {
        $sendData = [
            'touser'           => $toUser,
            'template_id'      => $templateId,
            'page'             => $page,
            'form_id'          => $formId,
            'data'             => $data,
            'emphasis_keyword' => $emphasisKeyword
        ];
        return $this->app->template_message->send($sendData);
    }

    private function errorLog($tag, $msg)
    {
        if (!is_string($msg)) {
            $msg = json_encode($msg, JSON_UNESCAPED_UNICODE);
        }
        $ip = !empty($_SERVER['SERVER_ADDR']) ? ip2long($_SERVER['SERVER_ADDR']) : '';
        Log::error("小程序-[{$tag}] IP:{$ip} PID:" . getmypid() . ' ' . $msg);
    }

    public function sendSubscribeMessage($templateId, $toUser, $page, $data)
    {
        $sendData = [
            'touser'      => $toUser,
            'template_id' => $templateId,
            'page'        => $page,
            'data'        => $data,
        ];
        return $this->app->subscribe_message->send($sendData);
    }

    public function getLiveRooms(){
        $cacheKey = CacheKey::CK_ZHIBO_ROOM;
        $roomData   = Cache::get($cacheKey);
        if (empty($roomData)) {
            //$roomData = $this->app->live->getRooms();
            $access = $this->getAccessToken();
            $url = 'https://api.weixin.qq.com/wxa/business/getliveinfo?access_token='.$access['access_token'];
            $pararms = ['start'=>0,'limit'=>10];
            $data = RemoteRequest::postBody($url,json_encode($pararms,1));
            if($data['errcode'] === 0){
                $roomData = $data['room_info'];
            }
            Cache::put($cacheKey, $roomData, 1800); // 缓存30分钟
        }
        Log::info(print_r($roomData,1));
        return $roomData;
        
    }


    public function getRooms(int $start = 0, int $limit = 10)
    {
        $params = [
            'start' => $start,
            'limit' => $limit,
        ];

        return $this->httpPostJson('wxa/business/getliveinfo', $params);
    }

}
