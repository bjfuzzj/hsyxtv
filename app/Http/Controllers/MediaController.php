<?php
/*
 * @Author: your name
 * @Date: 2021-05-07 20:13:46
 * @LastEditTime: 2021-05-22 13:38:55
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
        $result = [];
        foreach($medias as $media){
            $temMedia['id_code'] = $media->id_code;
            $temMedia['name'] = $media->name;
            $temMedia['url_1'] = $media->url_1;
            $temMedia['poster_vertical'] = $media->poster_vertical;
            $result[] = $temMedia;
            
        }
        return $this->outSuccessResultApi($result);
    }
}
