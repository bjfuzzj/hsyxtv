<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Idgenter;
use App\Helper\Codec;

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
            $userId = Codec::encodeId(123);
            $result = [
                'userid'  => $userId,
                'groupid' => 123,
                'session' => $this->genTransferId(),
                'portal'  => 'https://tv.yiqiqw.com/',
                'upgrade' => 'https://tv.yiqiqw.com/',
                'cache'   => 'https://tv.yiqiqw.com/',
            ];
        }
        return $this->outSuccessResultApi($result);
    }

    public function genTransferId()
    {
        [$usec, $sec] = explode(" ", microtime());
        $millisecond = round($usec * 1000);
        $millisecond = str_pad($millisecond, 3, '0', STR_PAD_RIGHT);

        $incrId = IdGenter::getId();
        $incrId = str_pad($incrId, 6, '0', STR_PAD_RIGHT);
        return substr($incrId, 0, 6) . date('YmdHis', time()) . $millisecond . mt_rand(1000, 9999) . mt_rand(10000, 99999) . md5(microtime(true));
    }


}
