<?php
/*
 * @Author: bjfuzzj
 * @Date: 2020-12-02 18:06:10
 * @LastEditTime: 2020-12-17 20:30:23
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Http/Controllers/Controller.php
 */

namespace App\Http\Controllers;

use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class Controller extends BaseController
{
    use AuthorizesRequests, DispatchesJobs, ValidatesRequests;

    //输出 json 格式正确结果 后台使用
    public function outSuccessResult($data = [], $msg = '请求成功')
    {
        return response()->json(['errorCode' => 0, 'errorMsg' => $msg, 'data' => $data]);
    }

    //输出 json 格式错误结果 后台使用
    public function outErrorResult($code, $msg)
    {
        return response()->json(['errorCode' => $code, 'errorMsg' => $msg, 'data' => []]);
    }


    //输出 json 格式正确结果前台使用
    public function outSuccessResultApi(array $data = [])
    {
        return response()->json(['status' => 200, 'msg' => '', 'data' => $data]);
    }

    //输出 json 格式错误结果
    public function outErrorResultApi($code, $msg, array $data = [])
    {
        return response()->json(['status' => $code, 'msg' => $msg, 'data' => $data]);
    }

    
}
