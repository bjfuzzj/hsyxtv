<?php
/*
 * @Author: your name
 * @Date: 2021-05-07 20:13:46
 * @LastEditTime: 2021-10-12 20:09:18
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
use App\Models\TvUser;
use App\Models\WatchList;
use App\Models\Detail;
use App\Models\VDetail;

class MediaController extends Controller
{
    public function getList(Request $request)
    {
        $ids = $request->input('ids',[]);
        $source = $request->input('source','h');
        $indexName = $request->input('indexName','');

        if (empty($ids)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $medias = Media::whereIn('d_id',$ids)->orderByRaw('FIELD(d_id, '.implode(", " , $ids).')')->get();
        if($medias->isEmpty()){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        //竖屏
        if($source == 'v'){
            $vdetail = VDetail::where('indexname',$indexName)->first();
            $vpage = "";
            if($vdetail instanceof VDetail){
                $vpage = $vdetail->url_1;
            }
        } else {
            $detail = Detail::where('indexname',$indexName)->first();
            $page = '';
            if($detail instanceof Detail){
                $page = $detail->url_1;
            }
        }

        $result = [];
        foreach($medias as $media){
            if($source == 'v'){
                if(empty($vpage)){
                    $pageName = "https://tv.yiqiqw.com/vshow/{$media['id_code']}.html";
                }else{
                    $pageName = $vpage."?id={$media['d_id']}";
                }
            } else {
                if(empty($page)){
                    $pageName = "https://tv.yiqiqw.com/show/{$media['id_code']}.html";
                }else{
                    $pageName = $page."?id={$media['d_id']}";
                }
            }
            
            //1 教育 2 党教 3 普法
            // if($media['type'] == 1){
            //     if($source == 'v'){
            //         $pageName = "./vdetail-2.php?id={$media['d_id']}";
            //     }else{
            //         $pageName = "./detail-2.php?id={$media['d_id']}";
            //     }
            // }
            // //教育
            // elseif($media['type'] == 3){
            //     if($source == 'v'){
            //         $pageName = "./vdetail-1.php?id={$media['d_id']}";
            //     }else{
            //         $pageName = "./detail-1.php?id={$media['d_id']}";
            //     }
                
            // }
            // //2 党建
            // else{
            //     if($source == 'v'){
            //         $pageName = "https://tv.yiqiqw.com/vshow/{$media['id_code']}.html";
            //     }else{
            //         $pageName = "https://tv.yiqiqw.com/show/{$media['id_code']}.html";
            //     }
                
            // }

            
            $temMedia['id_code'] = $media->id_code;
            $temMedia['name'] = $media->name;
            $temMedia['actor'] = $media->actor;
            $temMedia['director'] = $media->director;
            $temMedia['total_num'] = $media->total_num;
            $temMedia['intro'] = $media->intro;
            $temMedia['type'] = $media->type;
            $temMedia['page_name'] = $pageName;
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

    //猜你喜欢数据
    public function getRecommend(Request $request)
    {
        $id = $request->input('id',0);
        $limit = $request->input('limit',6);
        $source = $request->input('source','h');
        $indexName = $request->input('indexName','');

        
        $media = Media::find($id);
        if(!$media instanceof Media){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        $vpage = $page =  "";
        //竖屏
        if($source == 'v'){
            $vdetail = VDetail::where('indexname',$indexName)->first();
            $vpage = "";
            if($vdetail instanceof VDetail){
                $vpage = $vdetail->url_1;
            }
        } else {
            $detail = Detail::where('indexname',$indexName)->first();
            $page = '';
            if($detail instanceof Detail){
                $page = $detail->url_1;
            }
        }


        $type = $media->type ?? 0;
        #sql:select   {名称},{海报竖图},{id编码},  url, createdatetime from {媒资库} where published='y' and d_id>=(SELECT floor(RAND() * (SELECT MAX(d_id) FROM {媒资库}))) ORDER BY d_id LIMIT 6;
        $sql = "select * from media where published='y' and d_id!={$id} and  type = {$type} and d_id >=(SELECT floor(RAND() * (SELECT MAX(d_id) FROM media where type={$type} and d_id!={$id} ))) ORDER BY d_id LIMIT $limit";
        $medias = DB::select($sql);

        foreach($medias as &$media){
            if($source == 'v'){
                if(empty($vpage)){
                    $pageName = "https://tv.yiqiqw.com/vshow/{$media->id_code}.html";
                }else{
                    $pageName = $vpage."?id={$media->d_id}";
                }
            } else {
                if(empty($page)){
                    $pageName = "https://tv.yiqiqw.com/show/{$media->id_code}.html";
                }else{
                    $pageName = $page."?id={$media->d_id}";
                }
            }
            // //1 教育 2 党教 3 普法
            //   //1 教育
            // if($media->type == 1){
            //     if($source == 'v'){
            //         $pageName = "./vdetail-2.php?id={$media->d_id}";
            //     }else{
            //         $pageName = "./detail-2.php?id={$media->d_id}";
            //     }
            // }
            // //普法
            // elseif($media->type == 3){
            //     if($source == 'v'){
            //         $pageName = "./vdetail-1.php?id={$media->d_id}";
            //     }else{
            //         $pageName = "./detail-1.php?id={$media->d_id}";
            //     }
            // }
            // //2 党建
            // else{
            //     if($source == 'v'){
            //         $pageName = "https://tv.yiqiqw.com/vshow/{$media->id_code}.html";
            //     }else{
            //         $pageName = "https://tv.yiqiqw.com/show/{$media->id_code}.html";
            //     }
                
            // }
            $media->page_name = $pageName;
        }
        return $this->outSuccessResultApi($medias);
        
    }






    //用户观看记录
    public function getWatchList(Request $request)
    {
        $userId = $request->input('user_id',0);
        $source = $request->input('source','h');
        $type = $request->input('type','');
        $indexName = $request->input('indexName','');
        
        $tvUser = TvUser::find($userId);
        if(!$tvUser instanceof TvUser){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }


        $vpage = $page =  "";
        //竖屏
        if($source == 'v'){
            $vdetail = VDetail::where('indexname',$indexName)->first();
            $vpage = "";
            if($vdetail instanceof VDetail){
                $vpage = $vdetail->url_1;
            }
        } else {
            $detail = Detail::where('indexname',$indexName)->first();
            $page = '';
            if($detail instanceof Detail){
                $page = $detail->url_1;
            }
        }
        

        // $sql = "select b.d_id,a.srcid,b.name,b.id_code,b.url_1,b.poster_vertical,b.type from watchlist a  inner join media b on a.srcid=b.d_id and a.userid = '".$userId."' and a.isdel=0 and b.type='".$type."'";
        $sql = "select b.d_id,a.srcid,b.name,b.id_code,b.url_1,b.poster_vertical,b.type from watchlist a  inner join media b on a.srcid=b.id_code and a.userid = '".$userId."' and a.isdel=0 order by a.createdatetime desc";
        $medias = DB::select($sql);

        foreach($medias as &$media){
              
              //1 教育  == 2  2 党教   3 普法  == 1
            // if($media->type == 1){
            //     if($source == 'v'){
            //         $pageName = "./vdetail-2.php?id={$media->d_id}";
            //     }else{
            //         $pageName = "./detail-2.php?id={$media->d_id}";
            //     }
            // }
            // //普法
            // elseif($media->type == 3){
            //     if($source == 'v'){
            //         $pageName = "./vdetail-1.php?id={$media->d_id}";
            //     }else{
            //         $pageName = "./detail-1.php?id={$media->d_id}";
            //     }
            // }
            // //2 党建
            // else{
            //     if($source == 'v'){
            //         $pageName = "https://tv.yiqiqw.com/vshow/{$media->id_code}.html";
            //     }else{
            //         $pageName = "https://tv.yiqiqw.com/show/{$media->id_code}.html";
            //     }
                
            // }

            if($source == 'v'){
                if(empty($vpage)){
                    $pageName = "https://tv.yiqiqw.com/vshow/{$media->id_code}.html";
                }else{
                    $pageName = $vpage."?id={$media->d_id}";
                }
            } else {
                if(empty($page)){
                    $pageName = "https://tv.yiqiqw.com/show/{$media->id_code}.html";
                }else{
                    $pageName = $page."?id={$media->d_id}";
                }
            }
            $media->page_name = $pageName;
        }
        return $this->outSuccessResultApi($medias);
        
    }


    
}
