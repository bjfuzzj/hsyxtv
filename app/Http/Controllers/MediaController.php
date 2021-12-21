<?php
/*
 * @Author: your name
 * @Date: 2021-05-07 20:13:46
 * @LastEditTime: 2021-12-21 15:15:29
 * @LastEditors: bjfuzzj
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
use App\Models\LanMu;
use App\Models\VDetail;
use App\Models\ZhiBo;

class MediaController extends Controller
{

    public function search(Request $request)
    {
        $search_name = $request->input('search_name','');
        $category_id = $request->input('category_id',0);
        $page = $request->input('page',1);
        $size = $request->input('size',10);

        $page = (int)$page;
        $size = (int)$size;
        if($size>=20) $size = 20;


        
        // $size = 6;
        if(empty($search_name) || !ctype_alnum($search_name) || !is_numeric($category_id))
        {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
  

        $result = [];
        if(!empty($category_id)){

            $result = [];
            $query = Media::where('search_name','like',$search_name.'%');
    
            // $queryRes  = $query->simplePaginate($size);
            $queryRes  = $query->paginate($size);
            $resultRes = $queryRes->items();


            // $madiaList = Media::where('search_name','like',$search_name.'%')->get();
            
            foreach($resultRes as $media){
                $temMedia = [];
                $pageName = "https://tv.yiqiqw.com/show/{$media['id_code']}.html";
                $temMedia['id_code'] = $media->id_code;
                $temMedia['id'] = $media->d_id;
                $temMedia['name'] = $media->name;
                $temMedia['page_name'] = $pageName;
                $temMedia['poster_vertical'] = $media->poster_vertical;
                $temMedia['search_name'] = $media->search_name;
                $result[] = $temMedia;
            }
            return $this->outSuccessResultApi([
                'hasMore' => $queryRes->hasMorePages() ? 1 : 0,
                'current_page' => $queryRes->currentPage(),
                'per_page'     => $queryRes->perPage(),
                'total'        => $queryRes->total(),
                'last_page'    => $queryRes->lastPage(),
                'list'    => $result
            ]);
        }
        
        return $this->outSuccessResultApi($result);
    }

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
                    $pageName = "https://tv.yiqiqw.com/vshow/{$media->id_code}.html";
                }else{
                    $pageName = $vpage."?id={$media->d_id}";
                }
            } else {
                if(empty($page)){
                    //$pageName = "https://tv.yiqiqw.com/show/{$media->id_code}.html";
                    $pageName = "./detail.php?id={$media->d_id}";
                }else{
                    $pageName = $page."?id={$media->d_id}";
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
            $temMedia['id'] = $media->d_id;
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





    public function getSubDetail(Request $request)
    {
        $id = $request->input('id',0);
        if (empty($id)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $subMedia = SubMedia::find($id);
        if(!$subMedia instanceof SubMedia){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        $subMedia = $subMedia->toArray();
        $media = Media::find($subMedia['media_id']);
        if($media instanceof Media){
            $subMedia['sub_id_code'] = $media->id_code;
        }
        return $this->outSuccessResultApi($subMedia);
    }

    public function getSubDetailByNum(Request $request)
    {
        $id = $request->input('id',0);
        $num = $request->input('num',0);
        if (empty($id) || empty($num)) {
            return $this->outErrorResultApi(500, '内部错误[1]');
        }
        $subMedia = SubMedia::where('media_id',$id)->where('now_num',$num)->first();
        if(!$subMedia instanceof SubMedia){
            return $this->outErrorResultApi(500, '内部错误[2]');
        }
        $subMedia = $subMedia->toArray();
        // $media = Media::find($subMedia['media_id']);
        // if($media instanceof Media){
        //     $subMedia['sub_id_code'] = $media->id_code;
        // }
        return $this->outSuccessResultApi($subMedia);
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
                    //$pageName = "https://tv.yiqiqw.com/show/{$media->id_code}.html";
                    $pageName = "./detail.php?id={$media->d_id}";
                }else{
                    $pageName = $page."?id={$media->d_id}";
                }
            }
            $media->page_name = $pageName;
        }
        return $this->outSuccessResultApi($medias);
        
    }


     //获取首页模块数据
     public function getIndexModule(Request $request)
     {
        $result = [];
        $categoryData = [];
        $moduleList = LanMu::where('status',LanMu::STATUS_ONLINE)->orderBy('shunxu','asc')->get();
        $i = 0;
        foreach($moduleList as $module){
            $first = [];
            $first = $this->getFirst($module);
            $module_date = [];
            $module_date['first'] = $first;
            $module_date['other'] = [];
            $module_date['other'] = $this->getOther($module);
            $categoryData[$i] = $module_date;
            $i++;
            Log::info(print_r($module_date,1));
        }
        
        return $this->outSuccessResultApi($categoryData);
     }

     private function getOther($module) {
        $other = [];
        $tj_one_ids = $module->tj_one_ids??[];
        $tj_two_ids = $module->tj_one_ids??[];

        if(!empty($tj_one_ids)){
            $tj_one_ids = @explode(',',$module->tj_one_ids) ?? [];
            $oneData= [];
            $oneData['title'] = $module->tj_one;
            $oneData['data'] = [];
            $medias = Media::whereIn('d_id',$tj_one_ids)->orderByRaw('FIELD(d_id, '.implode(", " , $tj_one_ids).')')->get();
            if(!$medias->isEmpty()){
                foreach($medias as $media) {
                    $temp = [];
                    $temp['type'] = 'poster';
                    $temp['openType'] = 'self';
                    $temp['openUrl'] = "./detail.php?id={$media->d_id}";
                    $temp['posterUrl'] = $media->vertical_url??'';
                    $temp['name'] = $media->name??'';
                    array_push($oneData['data'],$temp);
                }
            }
            array_push($other,$oneData);
        }

        if(!empty($tj_two_ids)){
            $tj_two_ids = @explode(',',$module->tj_two_ids) ?? [];
            $twoData= [];
            $twoData['title'] = $module->tj_two;
            $twoData['data'] = [];
            
            $medias = Media::whereIn('d_id',$tj_two_ids)->orderByRaw('FIELD(d_id, '.implode(", " , $tj_two_ids).')')->get();
            if(!$medias->isEmpty()){
                foreach($medias as $media) {
                    $temp = [];
                    $temp['type'] = 'poster';
                    $temp['openType'] = 'self';
                    $temp['openUrl'] = "./detail.php?id={$media->d_id}";
                    $temp['posterUrl'] = $media->vertical_url??'';
                    $temp['name'] = $media->name??'';
                    array_push($twoData['data'],$temp);
                }
            }
            array_push($other,$twoData);
        }
        return $other;
    }

    private function getFirst($module){
        $first = [];
        for ($i=0;$i<8;$i++) {
            $ilink = 'link_'.($i+1);
            $ipic = 'pic_'.($i+1);

            if($i==0 && $module->type == 'play'){
                $first[$i]['type'] = 'play';
                $first[$i]['playUrl'] = '';
                $zhiBoId = $module->pindaoid ?? 0;
                $zhibo = ZhiBo::find($zhiBoId);
                if($zhibo instanceof ZhiBo){
                    $first[$i]['playUrl'] = $zhibo->bfdizhi ?? '';
                    $first[$i]['openUrl'] =  "./play_live_page-{$zhibo->d_id}.html";
                }
                $first[$i]['posterUrl'] = '';
            } else {
                $first[$i]['type'] = 'poster';
                $first[$i]['playUrl'] = '';
                $first[$i]['posterUrl'] = $module->$ipic ?? '';
                $first[$i]['openUrl'] = $module->$ilink ?? '';
            }
            $first[$i]['openType'] = 'self';
            $first[$i]['name'] = '';
        }
        return $first;
    }
    
}
