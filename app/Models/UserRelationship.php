<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;

/**
 * 用户上下级关系
 * Class UserRelationship
 * @package App
 * @property int id
 * @property int parent_id 上级分销员用户ID
 * @property int wx_user_id 微信用户ID
 * @property int user_id 用户ID，未成为分销员为0
 * @property string created_at
 */
class UserRelationship extends Model
{
    protected $table = 'user_relationships';
     // 添加关系
     public static function add($wxUserId, $parentId)
     {
         $self             = new self();
         $self->wx_user_id = $wxUserId;
         $self->parent_id  = $parentId;
         $self->save();
         return $self;
     }
 
     public function bindUserId($userId)
     {
         $this->user_id = $userId;
         return $this->save();
     }
 
     public static function getTeamPeopleNum($userId)
     {
         return self::where('parent_id', $userId)->count();
     }
 
     public static function getTeamList($userId, $params)
     {
         $type = $params['type'] ?? 'all';
         $page = $params['page'] ?? 1;
         $size = $params['size'] ?? 20;
 
         $query = DB::table('user_relationships as ur')
             ->leftJoin('distributor_details as d', function ($join) {
                 $join->on('ur.parent_id', '=', 'd.user_id')->on('ur.user_id', '=', 'd.source_user_id');
             })
             /*
             ->leftJoin('user_relationships as urship',function($join2){
                 $join2->on('ur.user_id','=','urship.parent_id');
             })
             */
             ->selectRaw('ur.id, sum(d.rebate_amount) as total_rebate')
             ->where('ur.parent_id', $userId);
         if ('month' == $type) {
             $beginDate = date('Y-m-01');
             $endDate   = today()->addMonth()->format('Y-m-01');
             $query->where('d.created_at', '>=', $beginDate)->where('d.created_at', '<', $endDate);
         }
         $paginateRes = $query->groupBy('ur.id')->orderByDesc('total_rebate')->orderByDesc('id')->simplePaginate($size);
 
         $rebateAmounts = [];
         foreach ($paginateRes->items() as $item) {
             $rebateAmounts[$item->id] = $item->total_rebate;
         }
         if (empty($rebateAmounts)) {
             return ['list' => [], 'page' => $page, 'has_more' => 0];
         }
 
         $queryRes = DB::table('user_relationships as ur')
             ->join('wx_users as wu', 'ur.wx_user_id', '=', 'wu.id')
             ->selectRaw('ur.id, wu.nick_name, wu.avatar, ur.created_at')
             ->whereIn('ur.id', array_keys($rebateAmounts))
             ->get();
         $result   = [];
         foreach ($queryRes as $item) {
             $result[$item->id] = collect($item)->toArray();
         }
 
         $teamList = [];
         foreach ($rebateAmounts as $id => $totalRebate) {
             if (!isset($result[$id])) {
                 continue;
             }
 
            //$headImage = 'https://img.etonggan.com/80f6450f6a74d4f3e4112b8391c7062f.jpeg';
             $headImage = 'https://img.66gou8.com/6f43a3e05fcb30768ec7efa149299a2d.jpeg';
             if (!empty($result[$id]['avatar'])) {
                 $headImage = $result[$id]['avatar'];
             }
 
             $teamList[] = [
                 'nick_name'    => $result[$id]['nick_name'],
                 'head_image'   => $headImage,
                 'total_rebate' => is_null($totalRebate) ? 0 : $totalRebate,
                 'join_time'    => Carbon::parse($result[$id]['created_at'])->format('Y-m-d H:i')
             ];
         }
 
         return [
             'list'     => $teamList,
             'page'     => $page,
             'has_more' => $paginateRes->hasMorePages() ? 1 : 0
         ];
     }
}
