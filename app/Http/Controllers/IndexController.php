<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-08-17 12:39:36
 * @LastEditTime: 2022-11-21 18:51:39
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Http/Controllers/IndexController.php
 * 寻找内心的安静
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\RemoteRequest;
use Illuminate\Support\Facades\Log;
use App\Models\LiveLanMu;
use App\Models\TvUser;
use App\Models\UserModuleRecord;
use App\Models\Media;

class IndexController extends Controller
{
    public function showCode(Request $request)
    {
        $params = $this->validate($request, [
            'userid'   => 'required|string',
        ], [
            '*' => '获取失败，请重试[-1]'
        ]);
        $sendParams = [];
        $sendParams['qrCode'] = 'dfuser_' . $params['userid'];
        $sendParams['signature'] = md5($sendParams['qrCode'] . '20220817');
        $sendParams['temporary'] = 1;
        $res = RemoteRequest::post('https://dxy.quwango.com/getQrcode', $sendParams);

        $url = '';
        if ($res['status'] == 200) {
            $url = $res['data'];
        }

        return view('show', ['url' => $url]);
    }

    //动态首页
    public function liveIndex(Request $request)
    {
        $tvUserId = $request->route('tv_user_id', 0);
        $tvUser = TvUser::find($tvUserId);
        if (!$tvUser instanceof TvUser) {
            abort(404);
        }
        $tvUser->username = $tvUser->username ?? '首页';
        //首页模块
        $liveIndex = LiveLanMu::find(1);
        if (!$liveIndex instanceof LiveLanMu) {
            abort(404);
        }
        $this->setDefaultValue($liveIndex, $tvUserId);
        $resData = [];
        $resData['tv_user'] = $tvUser;
        $resData['liveIndex'] = $liveIndex;
        //logo
        // $resData['logo_pic'] = $liveIndex->logo_pic;
        //栏目边框
        $liveLanmuList = LiveLanMu::where('published', 'y')->where('status', LiveLanMu::STATUS_ONLINE)->where('typeindex', $liveIndex->typeindex)->orderBy('shunxu', 'asc')->get();
        $resData['lanmu_list'] = $liveLanmuList;
        //其他栏目的图片预加载
        $otherImages = [];
        foreach ($liveLanmuList as $liveLanmu) {
            if ($liveLanmu->d_id > 1) {
                for ($i = 1; $i < 8; $i++) {
                    $temp_pic_url = 'pic_' . $i;
                    if (!empty($liveLanmu->$temp_pic_url)) {
                        $otherImages[] = $liveLanmu->$temp_pic_url;
                    }
                }
            }
        }
        $resData['other_images'] = $otherImages;
        return view('live.index', ['resData' => $resData]);
    }



    //动态公司页，logo 进入后的页面
    public function liveCompany(Request $request)
    {
        $tvUserId = $request->route('tv_user_id', 0);
        $tvUser = TvUser::find($tvUserId);
        if (!$tvUser instanceof TvUser) {
            abort(404);
        }
        //->where('source_type', UserModuleRecord::SOURCE_TYPE_LOGO)
        //
        $resData = [];
        $videoList = [];
        $mediaIds = [];
        $bg = UserModuleRecord::DEFAULT_BG;
        $moduleRes = UserModuleRecord::where('tv_user_id', $tvUserId)->get();
        if(!$moduleRes->isEmpty()){
            foreach($moduleRes as $userModule) {
                if( UserModuleRecord::SOURCE_TYPE_BG == $userModule->source_type ){
                    $bg = !empty($userModule->source_value) ? $userModule->source_value : $bg;
                }elseif(UserModuleRecord::SOURCE_TYPE_VIDEO == $userModule->source_type){
                    $sourceValue = $userModule->source_value;
                    $sourceArray = @explode(',',$sourceValue);
                    $tempVideo = [];
                    $tempVideo['poster'] = $sourceArray[0];
                    $tempVideo['meida_id'] = $sourceArray[1];
                    $mediaIds[] = $sourceArray[1];
                    $videoList[] = $tempVideo;
                }
            }
        }
        $titleList = Media::whereIn('d_id',$mediaIds)->pluck('name','d_id');
        $resData['bg']         = $bg;
        $resData['video_list'] = $videoList;
        $resData['title_list'] = $titleList;
        return view('live.company', ['resData' => $resData]);
    }


   
    private function setDefaultValue(&$liveIndex,$tvUserId)
    {
        //logo
        $module_pic = 'https://v.static.yiqiqw.com/pic/9582672d0487d65468a314cd1737b131.jpg';
        $module_link = 'http://dxy.yiqiqw.com/ztxqy-10.html';
        $moduleRes = UserModuleRecord::where('tv_user_id', $tvUserId)->where('source_type', UserModuleRecord::SOURCE_TYPE_LOGO)->first();
        if ($moduleRes instanceof UserModuleRecord) {
            $module_pic = $moduleRes->source_value;
            $module_link = route('liveCompany', ['tv_user_id' => $tvUserId]);
        }
        $liveIndex->logo_pic = isset($liveIndex->logo_pic) && !empty($liveIndex->logo_pic) ?  $liveIndex->logo_pic :  'https://v.static.yiqiqw.com/pic/f4fc235889cbcafa02b7b9dbf3b1996e.png';
        $liveIndex->pic_1 = $module_pic;
        $liveIndex->link_1 = $module_link;
        $liveIndex->pic_2 = isset($liveIndex->pic_2) && !empty($liveIndex->pic_2) ?  $liveIndex->pic_2 :  'https://v.static.yiqiqw.com/pic/6a7bf331930e972385b9019de31d5269.jpg';
        $liveIndex->link_2 = isset($liveIndex->link_2) && !empty($liveIndex->link_2) ?  $liveIndex->link_2 :  'https://v.static.yiqiqw.com/pic/b2aaaa9dc89273bcdbf8df637d1b6dcf.jpg';
        $liveIndex->pic_3 = isset($liveIndex->pic_3) && !empty($liveIndex->pic_3) ?  $liveIndex->pic_3 :  'https://v.static.yiqiqw.com/pic/023486bc2c17c8a71fb64aae07001f51.jpg';
        $liveIndex->link_3 = isset($liveIndex->link_3) && !empty($liveIndex->link_3) ?  $liveIndex->link_3 :  'https://v.static.yiqiqw.com/pic/0c7e2e3d108369714d25b9981c24324a.png';
        $liveIndex->pic_4 = isset($liveIndex->pic_4) && !empty($liveIndex->pic_4) ?  $liveIndex->pic_4 :  'https://v.static.yiqiqw.com/pic/b248221efc42df3a948742ad613bbd1d.jpg';
        $liveIndex->link_4 = isset($liveIndex->link_4) && !empty($liveIndex->link_4) ?  $liveIndex->link_4 :  'http://dxy.yiqiqw.com/fourzty-7.html';
        $liveIndex->pic_5 = isset($liveIndex->pic_5) && !empty($liveIndex->pic_5) ?  $liveIndex->pic_5 :  'https://v.static.yiqiqw.com/pic/1d2d91afa63a68543ba6ad44fdc7aa25.jpg';
        $liveIndex->link_5 = isset($liveIndex->link_5) && !empty($liveIndex->link_5) ?  $liveIndex->link_5 :  'http://dxy.yiqiqw.com/fourzty-8.html';
        $liveIndex->pic_6 = isset($liveIndex->pic_6) && !empty($liveIndex->pic_6) ?  $liveIndex->pic_6 :  'https://v.static.yiqiqw.com/pic/a47444bfbb4e9c34975784d3c665e30a.jpg';
        $liveIndex->link_6 = isset($liveIndex->link_6) && !empty($liveIndex->link_6) ?  $liveIndex->link_6 :  'https://www.12371.cn/2022/10/17/ARTI1665990592023497.shtml';
        $liveIndex->pic_7 = isset($liveIndex->pic_7) && !empty($liveIndex->pic_7) ?  $liveIndex->pic_7 :  'https://v.static.yiqiqw.com/pic/5e06d4c4ce2f2944a779aeb03630ce46.jpg';
        $liveIndex->link_7 = isset($liveIndex->link_7) && !empty($liveIndex->link_7) ?  $liveIndex->link_7 :  'https://www.12371.cn/2022/10/19/ARTI1666182797421631.shtml';
        return $liveIndex;
    }
}
