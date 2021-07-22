<?php
/*
 * @Author: your name
 * @Date: 2021-05-07 20:13:46
 * @LastEditTime: 2021-07-22 14:38:58
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /tv/app/Http/Controllers/MediaController.php
 */

namespace App\Http\Controllers;

use App\Models\Media;
use App\Models\SubMedia;
use EasyWeChat\Kernel\Messages\Media as MessagesMedia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;

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


    public function getRecommend(Request $request)
    {
        $id = $request->input('id',0);
        $limit = $request->input('limit',6);
        $media = Media::find($id);
        if(!$media instanceof Media){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        $type = $media->type ?? '';
        #sql:select   {名称},{海报竖图},{id编码},  url, createdatetime from {媒资库} where published='y' and d_id>=(SELECT floor(RAND() * (SELECT MAX(d_id) FROM {媒资库}))) ORDER BY d_id LIMIT 6;
        $sql = "select * from media where published='y' and d_id!={$id} and  type = {$type} and d_id >=(SELECT floor(RAND() * (SELECT MAX(d_id) FROM media where type={$type} and d_id!={$id} ))) ORDER BY d_id LIMIT $limit";
        $medias = DB::select($sql);
        return $this->outSuccessResultApi($medias);
        
    }


    
}
