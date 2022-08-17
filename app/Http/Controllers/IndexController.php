<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-08-17 12:39:36
 * @LastEditTime: 2022-08-17 14:40:47
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Http/Controllers/IndexController.php
 * 寻找内心的安静
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Helper\RemoteRequest;
use Illuminate\Support\Facades\Log;

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
        Log::info(print_r($res,1));
        
        return view('show');
    }
}
