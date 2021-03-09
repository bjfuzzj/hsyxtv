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

        $result          = [];
        $result['apps']  = [
            [
                'pkgName' => 'com.xlab.hello',
                'clsName' => '.MainActivity',
                'verName' => '1.1.100',
                'verCode' => '100',
                'md5'     => 'OAAFAFAFAQQ',
                'url'     => 'https://tv.yiqiqw.com/apps/hello.apk',
            ],
            [
                'pkgName' => 'com.xlab.world',
                'clsName' => '.MainActivity',
                'verName' => '6.2.100',
                'verCode' => '200',
                'md5'     => 'OAAFAFAFAAAAQ',
                'url'     => 'https://tv.yiqiqw.com/apps/hello.apk',
            ],
        ];
        $result['dels']  = [
            [
                'pkgName' => 'com.xlab.hello'
            ],
            [
                'pkgName' => 'com.xlab.world'
            ],
        ];
        $result['img']   = [
            'imgdes' => 'ampere test key',
            'imgutc' => '123456',
            'md5'    => 'OAAFAFAFAAAAQ',
            'url'    => 'https://tv.yiqiqw.com/apps/update.zip',
        ];
        $result['bootv'] = [
            'md5' => 'OAAFAFAFAAAAQ',
            'url' => 'https://tv.yiqiqw.com/apps/bootvide.mp4',
        ];
        $result['type']  = 'default';

        return $this->outSuccessResultApi($result);

    }

    public function cache(Request $request)
    {
        $params         = $this->validate($request, [
            'userid'  => 'required|string',
            'session' => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $userId         = $params['userid'];
        $result         = [];
        $result['adds'] = [
            [
                'id'   => '1234567890',
                'type' => 'mp4',
                'md5'  => 'OAAFAFAFAAAAQ',
                'url'  => 'https://tv.yiqiqw.com/apps/1.mp4',
            ],
            [
                'id'   => '1230',
                'type' => 'mp4',
                'md5'  => 'OAAAAAAAFAAAAQ',
                'url'  => 'https://tv.yiqiqw.com/apps/2.mp4',
            ]
        ];
        $result['dels'] = [
            [
                'id' => '1234567890',
            ],
            [
                'id' => '124444490',
            ]
        ];
        $result['type'] = 'delall';

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
