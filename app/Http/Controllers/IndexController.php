<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-08-17 12:39:36
 * @LastEditTime: 2022-11-21 10:52:01
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
        $sendParams['qrCode'] = 'dfuser_'.$params['userid'];
        $sendParams['signature'] = md5($sendParams['qrCode'] . '20220817');
        $sendParams['temporary'] = 1;
        $res = RemoteRequest::post('https://dxy.quwango.com/getQrcode',$sendParams);

        $url = '';
        if($res['status'] == 200){
            $url = $res['data'];
        }
        
        return view('show',['url'=>$url]);
    }


    public function liveIndex(Request $request)
    {
        $tvUserId = $request->route('tv_user_id',0);
        //首页
        $liveIndex = LiveLanMu::find(1);
        $resData = [];
        //logo
        $resData['logo_pic'] = $liveIndex->logo_pic??'https://v.static.yiqiqw.com/pic/f4fc235889cbcafa02b7b9dbf3b1996e.png';
        //栏目边框
        $liveLanmuList = LiveLanMu::where('published','y')->where('status',LiveLanMu::STATUS_ONLINE)->where('typeindex',$liveIndex->typeindex)->orderBy('shunxu','asc')->get();
        $resData['lanmuList'] = $liveLanmuList;

        return view('live.index',['resData'=>$resData]);
    }
}
