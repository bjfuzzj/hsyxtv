<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

/**
 * 参数验签
 * 1.防伪装攻击
 * 2.防篡改攻击
 * 3.防重放攻击
 *
 * Class SignAuthenticate
 * @package App\Http\Middleware
 */
class SignAuthenticate
{
    /**
     * 接口请求时间最大间隔时间（秒）
     * @var int
     */
    protected $maxInterval = 600;

    /**
     * 跳过参数验签的白名单路由（前后去掉'/'）
     * @var array
     */
    protected $whiteList = [
        'api/wxapp/login'
    ];

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $switch = env('PARAMS_SIGN_SWITCH', false);
        $secret = env('PARAMS_SIGN_SECRET', '');

        // 没有开启验签，跳过参数验签
        if (!$switch) {
            return $next($request);
        }

        // 白名单路由，跳过参数验签
        $path = $request->path();
        $path = trim($path, DIRECTORY_SEPARATOR);
        if (!empty($path) && in_array($path, $this->whiteList)) {
            return $next($request);
        }

        $params = $request->all();
        $sign   = $request->header('sign');
        if (empty($sign)) {
            $this->errorLog($request, 'sign不存在');
            return response()->json(['errorCode' => 4001, 'errorMsg' => '请求失败[1]', 'data' => []]);
        }
        if (empty($params['_v']) || empty($params['_nonce']) || empty($params['_t']) || empty($params['_token'])) {
            $this->errorLog($request, '缺少参数');
            return response()->json(['errorCode' => 4002, 'errorMsg' => '请求失败[2]', 'data' => []]);
        }
        $nowTime = $_SERVER['REQUEST_TIME'] ?? now()->getTimestamp();
        if (!isset($params['_t']) || abs($params['_t'] - $nowTime) > $this->maxInterval) {
            $this->errorLog($request, '接口请求时间超出最大间隔时间 nowTime:' . $nowTime);
            return response()->json(['errorCode' => 4003, 'errorMsg' => '请求失败[3]', 'data' => []]);
        }
        ksort($params, SORT_STRING);

        $signStr = '';
        foreach ($params as $k => $v) {
            $signStr .= $k . '=' . $v . "&";
        }
        $signStr   .= 'secret=' . $secret;
        $signValue = sha1($signStr);
        if ($sign != $signValue) {
            $this->errorLog($request, '签名不正确 ' . $signValue);
            return response()->json(['errorCode' => 4004, 'errorMsg' => '请求失败[4]', 'data' => []]);
        }
        return $next($request);
    }

    public function errorLog(Request $request, $errorMsg)
    {
        $path   = $request->path();
        $params = $request->all();
        $ip     = !empty($_SERVER['SERVER_ADDR']) ? ip2long($_SERVER['SERVER_ADDR']) : '';
        Log::error("==参数验签失败== IP: {$ip} errorLog:{$errorMsg} path:{$path} params:" . print_r($params, 1));
    }
}
