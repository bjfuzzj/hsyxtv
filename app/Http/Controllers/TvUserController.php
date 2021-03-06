<?php

namespace App\Http\Controllers;

use App\Models\CacheConfig;
use App\Models\DGroup;
use Illuminate\Http\Request;
use App\Models\Idgenter;
use App\Helper\Codec;
use App\Models\AdTheme;
use Illuminate\Support\Facades\Log;
use App\Models\TvUser;
use App\Models\UpgradeConfig;
use App\Models\WatchList;
use Illuminate\Support\Carbon;
use App\Models\Detail;
use App\Models\Media;

class TvUserController extends Controller
{


    public function login(Request $request)
    {
        $params = $this->validate($request, [
            'mac'   => 'required|string',
            't'     => 'required|string',
            'token' => 'required|string',
            'v'     => 'nullable|string',

        ], [
            '*' => '登录失败，请重试[-1]'
        ]);

        $mac   = $params['mac'];
        $t     = $params['t'];
        $token = $params['token'];
        $v = $params['v'] ?? '';


        if (empty($mac) || empty($t) || empty($token)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $sign = md5(config('app.login_key') . $mac . $t);
        if ($sign != strtolower($token)) {
            return $this->outErrorResultApi(500, '参数错误[1]');
        } else {
            $now = Carbon::now();
            $expire_time = $now->addYears(1);
            $salt = $this->getSalt();
            $passwd = env('DEFAULT_PASS','');
            $passwd = md5($passwd.$salt);

            if(!empty($v) && strpos($v,'6.0') !== false ){
                $user     = TvUser::firstOrCreate(['mac' => $mac], ['group_id' => DGroup::HUBEI_GROUP_ID, 'expire' => $expire_time,'vername'=>$v,'salt'=>$salt,'passwd'=>$passwd]);
            }else{
                $user     = TvUser::firstOrCreate(['mac' => $mac], ['group_id' => DGroup::DEFAULT_ID, 'expire' => $expire_time,'salt'=>$salt,'passwd'=>$passwd]);
            }
            
            $userId       = $user->d_id;
            $portal       = "https://tv.yiqiqw.com/index.html";
            $mp1_notify   = '';
            $mp1_interval = 10;
            $mp1          = "https://tv.yiqiqw.com/time_task";
            $mode         = 'normal';
            $mix_ad_time  = 600;

            //更新 salt - passwd
            if(empty($user->salt) && empty($user->passwd)) {
                $user->salt = $salt;
                $user->passwd = $passwd;
                $user->save();
            }

            //查看分组
            $group = DGroup::find($user->group_id);
            if ($group instanceof DGroup) {
                if ($group->isAloneIndex()) {
                    $portal = $group->index_src;
                }
                $mode        = $group->mode;
                $mix_ad_time = $group->mix_ad_time;
            }
            $expireTime = Carbon::parse($user->expire);
            $expire = $expireTime->timestamp;
            $result = [
                'userid'           => $userId,
                'groupid'          => $user->group_id,
                'session'          => $this->genTransferId($userId),
                'portal'           => $portal,
                'upgrade'          => 'https://tv.yiqiqw.com/upgrade',
                'upgrade_interval' => '300',
                'cache'            => 'https://tv.yiqiqw.com/cache',
                'cache_interval'   => '300',
                'mp1'              => $mp1,
                'mp1_notify'       => $mp1_notify,
                'mp1_interval'     => $mp1_interval,
                'mode'             => $mode,
                'mix_ad_time'      => $mix_ad_time,
                'ext_enable' => 0,
                'expire' => $expire,
                'expire_url'=>'http://h.qr61.cn/optFHZ/qyLgXgN',
                'expire_pic'=>'https://v.static.yiqiqw.com/pic/f99aebe7f31038a2d13026dd7762e63b.png',
                'expire_pic_md5'=>'313bbd27cd34dc8061676680104d3c5f',
                'mqtt'=>'tcp://39.97.164.69:1883',
                'portal_v'=>''
            ];
        }
        return $this->outSuccessResultApi($result);
    }


    public function upgrade(Request $request)
    {
        $params = $this->validate($request, [
            'userid'  => 'required|string',
            'mac'     => 'nullable|string',
            'session' => 'required|string',
            'device'  => 'required|string',
            'romutc'  => 'nullable|string',
            'romdes'  => 'nullable|string',
            'pkgname' => 'required|string',
            'vername' => 'required|string',
            'vercode' => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $userId = $params['userid'];
        $tvUser = TvUser::find($userId);
        if ($tvUser instanceof TvUser) {
            $tvUser->device = $params['device'];
            $tvUser->romutc = $params['romutc'];
            if (!empty($params['romdes'])) {
                $tvUser->romdes = $params['romdes'];
                if(strpos($params['romdes'],'rk3399_all-eng 7.1.2 NHG47K eng.gxg') !== false && empty($tvUser->group_id)){
                    $tvUser->group_id = 7;
                }
            }
            $tvUser->romutc  = $params['romutc'];
            $tvUser->pkgname = $params['pkgname'];
            $tvUser->vername = $params['vername'];
            $tvUser->vercode = $params['vercode'];
            $tvUser->save();
        }

        $result         = [];
        $result['adds'] = $result['dels'] = $result['img'] = $result['bootv'] = [];
        $result['type'] = UpgradeConfig::DEL_TYPE_DEFAULT;
        $allConfigs     = UpgradeConfig::where('status', UpgradeConfig::STATUS_ONLINE)->get();
        foreach ($allConfigs as $config) {
            if ($config->isAddType()) {
                $adds = @json_decode($config->content, 1);
                if (!empty($adds)) {
                    $result['adds'] = $adds;
                }
            } elseif ($config->isDelType()) {
                $dels = @json_decode($config->content, 1);

                if (!empty($dels)) {
                    $result['dels'] = $dels;
                }
            } elseif ($config->isImgType()) {
                $img = @json_decode($config->content, 1);
                if (!empty($img)) {
                    $result['img'] = $img;
                }
            } elseif ($config->isVideoType()) {
                $bootv = @json_decode($config->content, 1);
                if (!empty($bootv)) {
                    $result['bootv'] = $bootv;
                }
            } elseif ($config->isDelAllType()) {
                $delTypeStr = trim($config->content);
                if (!empty($delTypeStr)) {
                    $result['type'] = $delTypeStr;
                }
            }
        }

        //        $result['apps']  = [
        //            [
        //                'pkgName' => 'com.xlab.hello',
        //                'clsName' => '.MainActivity',
        //                'verName' => '1.1.100',
        //                'verCode' => '100',
        //                'md5'     => 'OAAFAFAFAQQ',
        //                'url'     => 'https://tv.yiqiqw.com/apps/hello.apk',
        //            ],
        //            [
        //                'pkgName' => 'com.xlab.world',
        //                'clsName' => '.MainActivity',
        //                'verName' => '6.2.100',
        //                'verCode' => '200',
        //                'md5'     => 'OAAFAFAFAAAAQ',
        //                'url'     => 'https://tv.yiqiqw.com/apps/hello.apk',
        //            ],
        //        ];
        //        $result['dels']  = [
        //            [
        //                'pkgName' => 'com.xlab.hello'
        //            ],
        //            [
        //                'pkgName' => 'com.xlab.world'
        //            ],
        //        ];
        //        $result['img']   = [
        //            'imgdes' => 'ampere test key',
        //            'imgutc' => '123456',
        //            'md5'    => 'OAAFAFAFAAAAQ',
        //            'url'    => 'https://tv.yiqiqw.com/apps/update.zip',
        //        ];
        //        $result['bootv'] = [
        //            'md5' => 'OAAFAFAFAAAAQ',
        //            'url' => 'https://tv.yiqiqw.com/apps/bootvide.mp4',
        //        ];
        //        $result['type']  = 'default';

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
        $result['adds'] = $result['dels'] = [];
        $result['type'] = CacheConfig::DEL_TYPE_DEFAULT;

        $allConfigs = CacheConfig::where('status', CacheConfig::STATUS_ONLINE)->get();
        foreach ($allConfigs as $config) {
            if ($config->isAddType()) {
                $adds = @json_decode($config->content, 1);
                if (!empty($adds)) {
                    $result['adds'] = $adds;
                }
            } elseif ($config->isDelType()) {
                $dels = @json_decode($config->content, 1);
                if (!empty($dels)) {
                    $result['dels'] = $dels;
                }
            } elseif ($config->isDelAllType()) {
                $delTypeStr = trim($config->content);
                if (!empty($delTypeStr)) {
                    $result['type'] = $delTypeStr;
                }
            } elseif ($config->isLimitType()) {
                $limitStr = trim($config->content);
                if (!empty($limitStr)) {
                    $result['limit'] = $limitStr;
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


    public function timeTask(Request $request)
    {
        $params = $this->validate($request, [
            'userid'  => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $userId = $params['userid'];
        $user = TvUser::find($userId);
        $result = [];
        if (!$user instanceof TvUser) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        //查看分组
        $group = DGroup::find($user->group_id);
        if($group instanceof DGroup){
            if(!empty($group->content)){
                $content = trim($group->content);
                if (!empty($content)) {
                    $result = @json_decode($group->content, 1);
                }
            }else{
                //优先取广告机内容
                if(!empty($group->theme_id)){
                    $adTheme = AdTheme::find($group->theme_id);
                    if($adTheme instanceof AdTheme){
                        $content = trim($adTheme->content);
                        if (!empty($content)) {
                            $result = @json_decode($adTheme->content, 1);
                        }
                    }
                }
            }
        }

        // if ($group instanceof DGroup) {
        //     $content = trim($group->content);
        //     if (!empty($content)) {
        //         $result = @json_decode($group->content, 1);
        //     }
        // }
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


    public function addWatchHistory(Request $request)
    {
        Log::info('addwatch===>' . print_r($request->all(), 1));
        $params = $this->validate($request, [
            'userid' => 'required|string',
            //            'session' => 'required|string',
            'codeid' => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $watch  = WatchList::where('userid', $params['userid'])->where('srcid', $params['codeid'])->first();
        if ($watch instanceof WatchList) {
            if ($watch->isDel()) {
                $watch->setNormal();
            }
        } else {
            $tvUser = TvUser::find($params['userid']);
            if ($tvUser instanceof TvUser) {
                $watch         = new WatchList();
                $watch->userid = $params['userid'];
                $watch->srcid  = $params['codeid'];
                $watch->isdel  = WatchList::STATUS_NORMAL;
                $watch->save();
            }
        }
        return $this->outSuccessResultApi([]);
    }

    public function delWatchHistory(Request $request)
    {
        Log::info('delwatch===>' . print_r($request->all(), 1));
        $params = $this->validate($request, [
            'userid' => 'required|string',
            //            'session' => 'required|string',
            'codeid' => 'required|string',
        ], [
            '*' => '参数出错，请重试[-1]'
        ]);
        $codeId = $params['codeid'];
        // $codeid = is_numeric($params['codeid'])
        $watch  = WatchList::where('userid', $params['userid'])->where('srcid', $codeId)->first();
        if ($watch instanceof WatchList) {
            if ($watch->isNormal()) {
                $watch->setDel();
            }
        }

        if(is_numeric($params['codeid'])){
            $media = Media::find($params['codeid']);
            if($media instanceof Media){
                $codeId = $media->id_code;
            }
            $watch  = WatchList::where('userid', $params['userid'])->where('srcid', $codeId)->first();
            if ($watch instanceof WatchList) {
                if ($watch->isNormal()) {
                    $watch->setDel();
                }
            }
        } else {
            $media = Media::where('id_code',$params['codeid'])->first();
            if($media instanceof Media){
                $codeId = $media->d_id;
            }
            $watch  = WatchList::where('userid', $params['userid'])->where('srcid', $codeId)->first();
            if ($watch instanceof WatchList) {
                if ($watch->isNormal()) {
                    $watch->setDel();
                }
            }
        }


        return $this->outSuccessResultApi([]);
    }


    public function getWatchHistory(Request $request)
    {
        $userId = $request->input('userid', 0);
        $lastId = $request->input('lastId', 0);
        $size   = $request->input('size', 20);
        $indexName = $request->input('indexName', '');
        if (empty($userId)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $query = WatchList::where('isdel', WatchList::STATUS_NORMAL)->orderBy('d_id');
        if ($lastId > 0) {
            $query->where('d_id', '>', $lastId);
        }

        $queryRes  = $query->simplePaginate($size);
        $resultRes = $queryRes->items();
        $watchList = [];

        //foreach ($resultRes as $singleWatch) {
        for ($i = 0; $i < 10; $i++) {
            $list           = [];
            $list['title']  = '标题' . $i;
            $list['url']    = 'https://tv.yiqiqw.com/show/rSv.html';
            $list['imgsrc'] = 'https://v.static.yiqiqw.com/pic/7f5c0376021d2604849e98a93c9b0deb.jpeg';
            $list['codeid'] = 'rSv';
            $watchList[]    = $list;
        }

        return $this->outSuccessResult([
            'hasMore' => $queryRes->hasMorePages() ? 1 : 0,
            'list'    => $watchList
        ]);
    }


    private function getSalt()
    {
        return substr(md5(mt_rand()), 0, 8);
    }
}
