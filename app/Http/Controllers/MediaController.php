<?php
/*
 * @Author: your name
 * @Date: 2021-05-07 20:13:46
 * @LastEditTime: 2021-05-22 13:30:33
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Http/Controllers/MediaController.php
 */

namespace App\Http\Controllers;

use App\Models\Media;
use Illuminate\Http\Request;

class MediaController extends Controller
{
    public function getList(Request $request)
    {
        $ids = $request->input('ids',[]);
        if (empty($ids)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $medias = Media::find($ids);
        if($medias->isEmpty()){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        return $this->outSuccessResultApi($medias);
    }
}
