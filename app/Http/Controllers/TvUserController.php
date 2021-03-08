<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idgenter;
use App\Helper\Codec;
use Illuminate\Support\Facades\Log;

class TvUserController extends Controller
{


    public function login(Request $request)
    {
        $params = $this->validate($request, [
            'mac'   => 'required|string',
            't'     => 'required|string',
            'token' => 'required|string',
        ], [
            '*' => '登录失败，请重试[-1]'
        ]);

        $mac   = $params['mac'];
        $t     = $params['t'];
        $token = $params['token'];

        if (empty($mac) || empty($t) || empty($token)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $sign = md5(config('app.login_key') . $mac . $t);
        if ($sign != strtolower($token)) {
            return $this->outErrorResultApi(500, '参数错误[1]');
        } else {
            $userId = 123;
            $result = [
                'userid'  => Codec::encodeId($userId),
                'groupid' => 123,
                'session' => $this->genTransferId($userId),
                'portal'  => 'https://tv.yiqiqw.com/index.html',
                'upgrade' => 'https://tv.yiqiqw.com/upgrade',
                'cache'   => 'https://tv.yiqiqw.com/cache',
            ];
        }
        return $this->outSuccessResultApi($result);
    }


    public function upgrade(Request $request)
    {
        $params = $this->validate($request, [
            'userid'  => 'required|string',
            'session' => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $userId = $params['userid'];
        $result = [
            [
                'name'        => '数据广播',
                'packName'    => 'cn.xlab.dvn',
                'versionName' => '1.0',
                'versionCode' => '1',
                'base64Str'   => 'OAAFAFAFAQQ',
            ],
            [
                'name'        => '终端管控示',
                'packName'    => 'cn.xlab.netmonitor',
                'versionName' => '1.4',
                'versionCode' => '5',
                'base64Str'   => 'OAAFAFAFAQQ',
            ]
        ];
        return $this->outSuccessResultApi($result);

    }

    public function cache(Request $request)
    {
        $params = $this->validate($request, [
            'userid'  => 'required|string',
            'session' => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $userId = $params['userid'];
        $result = [
            'https://img.meidaifu.com/FinalVideo_1613025176.689774.mp4',
            'https://img.meidaifu.com/FinalVideo_1612973051.278023.mp4',
            'https://img.meidaifu.com/tsFinalVideo_1613025176.689774.ts'
        ];
        return $this->outSuccessResultApi($result);

    }

    public function genTransferId($userId)
    {
        [$usec, $sec] = explode(" ", microtime());
        $millisecond = round($usec * 1000);
        $millisecond = str_pad($millisecond, 3, '0', STR_PAD_RIGHT);

//        $incrId = IdGenter::getId();
        $incrId = str_pad($userId, 6, '0', STR_PAD_RIGHT);
        return substr($incrId, 0, 6) . date('YmdHis', time()) . $millisecond . mt_rand(1000, 9999) . mt_rand(10000, 99999);
    }


}
