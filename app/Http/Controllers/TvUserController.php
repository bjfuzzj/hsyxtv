<?php

namespace App\Http\Controllers;

use App\Models\CacheConfig;
use App\Models\DGroup;
use Illuminate\Http\Request;
use App\Models\Idgenter;
use App\Helper\Codec;
use Illuminate\Support\Facades\Log;
use App\Models\TvUser;
use App\Models\UpgradeConfig;

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
            $user   = TvUser::firstOrCreate(['mac' => $mac], ['group_id' => DGroup::DEFAULT_ID]);
            $userId = $user->d_id;
            $result = [
                'userid'           => Codec::encodeId($userId),
                'groupid'          => $user->group_id,
                'session'          => $this->genTransferId($userId),
                'portal'           => 'https://tv.yiqiqw.com/index.html',
                'upgrade'          => 'https://tv.yiqiqw.com/upgrade',
                'upgrade_interval' => '300',
                'cache'            => 'https://tv.yiqiqw.com/cache',
                'cache_interval'   => '300',
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
        $result['adds'] = $result['dels'] = '';
        $result['type'] = CacheConfig::DEL_TYPE_DEFAULT;

        $allConfigs = CacheConfig::where('status', CacheConfig::STATUS_ONLINE)->get();
        foreach ($allConfigs as $config) {
            if ($config->isAddType()) {
                $adds = @json_decode($config->content, 1);
                if (!emtpy($adds)) {
                    $result['adds'] = $adds;
                }
            } elseif ($config->isDelType()) {
                $dels = @json_decode($config->content, 1);
                if (!emtpy($dels)) {
                    $result['dels'] = $dels;
                }
            } elseif ($config->isDelAllType()) {
                $delTypeStr = trim($config->content);
                if (!emtpy($delTypeStr)) {
                    $result['type'] = $delTypeStr;
                }
            }
        }

//        $result['adds'] = [
//            [
//                'id'   => '1234567890',
//                'type' => 'mp4',
//                'md5'  => 'OAAFAFAFAAAAQ',
//                'url'  => 'https://tv.yiqiqw.com/apps/1.mp4',
//            ],
//            [
//                'id'   => '1230',
//                'type' => 'mp4',
//                'md5'  => 'OAAAAAAAFAAAAQ',
//                'url'  => 'https://tv.yiqiqw.com/apps/2.mp4',
//            ]
//        ];
//        $result['dels'] = [
//            [
//                'id'   => '1234567890',
//                'type' => 'mp4'
//            ],
//            [
//                'id'   => '124444490',
//                'type' => 'ts'
//            ]
//        ];
//        $result['type'] = 'delall';

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
