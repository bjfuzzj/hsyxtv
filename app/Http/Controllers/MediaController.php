<?php
/*
 * @Author: your name
 * @Date: 2021-05-07 20:13:46
 * @LastEditTime: 2021-07-22 10:50:31
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Http/Controllers/MediaController.php
 */

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\SubMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class MediaController extends Controller
{
    public function getList(Request $request)
    {
        $ids = $request->input('ids',[]);
        // Log::info(print_r($ids,1));
        if (empty($ids)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        // $idsStr = @join(',',$ids);
        // $medias = Media::find($ids);
        $medias = Media::whereIn('d_id',$ids)->orderByRaw('FIELD(d_id, '.implode(", " , $ids).')')->get();
        if($medias->isEmpty()){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        $result = [];
        foreach($medias as $media){
            // Log::info($media->d_id);
            $temMedia['id_code'] = $media->id_code;
            $temMedia['name'] = $media->name;
            $temMedia['actor'] = $media->actor;
            $temMedia['director'] = $media->director;
            $temMedia['total_num'] = $media->total_num;
            $temMedia['intro'] = $media->intro;
            $temMedia['url_1'] = 'https://tv.yiqiqw.com/'.$media->url_1;
            $temMedia['poster_vertical'] = $media->poster_vertical;
            $result[] = $temMedia;
            
        }
        return $this->outSuccessResultApi($result);
    }

    public function getDetail(Request $request)
    {
        $id = $request->input('id',0);
        // Log::info(print_r($ids,1));
        if (empty($id)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $media = Media::find($id);
        if(!$media instanceof Media){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        $result = [];
        $result['media'] = $result['subMedia'] = [];
        $result['media'] = $media;
        
        $subMedias = SubMedia::where('media_id',$media->d_id)->orderBy('now_num','asc')->get();

        foreach($subMedias as $subMedia){
            // $temMedia['id_code'] = $media->id_code;
            // $temMedia['name'] = $media->name;
            // $temMedia['actor'] = $media->actor;
            // $temMedia['director'] = $media->director;
            // $temMedia['total_num'] = $media->total_num;
            // $temMedia['intro'] = $media->intro;
            // $temMedia['url_1'] = 'https://tv.yiqiqw.com/'.$media->url_1;
            // $temMedia['poster_vertical'] = $media->poster_vertical;
            $result['subMedia'][] = $subMedia;
            
        }
        return $this->outSuccessResultApi($result);
    }


    
}
