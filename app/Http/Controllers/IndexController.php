<?php
/*
 * @Author: bjfuzzj
 * @Date: 2022-08-17 12:39:36
 * @LastEditTime: 2022-08-17 13:59:10
 * @LastEditors: bjfuzzj
 * @Description: 
 * @FilePath: /tv/app/Http/Controllers/IndexController.php
 * 寻找内心的安静
 */

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class IndexController extends Controller
{
    public function showCode(Request $request)
    {
        // $params = $this->validate($request, [
        //     'mac'   => 'required|string',
        //     't'     => 'required|string',
        //     'token' => 'required|string',
        //     'v'     => 'nullable|string',
        //     'sn'     => 'nullable|string',

        // ], [
        //     '*' => '登录失败，请重试[-1]'
        // ]);

        // $mac   = $params['mac'];
        // $t     = $params['t'];
        // $token = $params['token'];
        // $v = $params['v'] ?? '';
        // $sn = $params['sn'] ?? '';
        return view('show');
    }
}
