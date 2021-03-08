<?php

namespace App\Http\Controllers;

use App\Helper\Codec;
use App\Models\Activity;
use App\Models\Group;
use App\Models\GroupTeam;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use App\Models\Product;
use App\Helper\MiniProgram;
use EasyWeChat\Kernel\Messages\MiniProgramPage;
use Illuminate\Support\Facades\Log;
use App\Helper\CacheKey;
use Illuminate\Support\Facades\Cache;

class BackGroupController extends Controller
{
    public function index(Request $request)
    {
        $activityId = $request->input('id', '');
        $status     = $request->input('status', '');
        $size       = $request->input('size', 50);
        $page       = $request->input('page', 1);
        $query      = Activity::orderByDesc('id');
        if (!empty($activityId)) {
            $query = $query->where('id', $activityId);
        }
        if (!empty($name)) {
            $query = $query->where('name','like', '%'.$name.'%');
        }
        if (!empty($status)) {
            $query = $query->where('status', $status);
        }
        if (!empty($valid_start_time)) {
            $query = $query->where('buy_start_time', '>=', $valid_start_time);
        }
        if (!empty($valid_end_time)) {
            $query = $query->where('buy_end_time', '<=', $valid_end_time);
        }
        $rewardList = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($rewardList as $item) {
            $recordList[] = [
                'id'             => $item->id,
                'name'           => $item->name,
                'status'         => $item->status,
                'status_desc'    => $item->status_desc,
                'product_id'     => $item->product_id,
                'product_name'   => $item->product->title,
                'full_amount'    => $item->full_amount,
                'original_price' => $item->original_price,
                'price'          => $item->price,
                'valid_day'      => $item->valid_day,
                'buy_start_time' => Carbon::parse($item->buy_start_time)->toDateTimeString(),
                'buy_end_time'   => Carbon::parse($item->buy_end_time)->toDateTimeString(),
            ];
        }
        return $this->outSuccessResult([
            'records'            => $recordList,
            'statusOptions'      => Activity::$statusOptions,
            'current_page'       => $rewardList->currentPage(),
            'per_page'           => $rewardList->perPage(),
            'total'              => $rewardList->total(),
        ]);

    }

    public function edit(Request $request)
    {
        $activityId = $request->input('id', '');
        $activity = Activity::find($activityId);
        if(empty($activityId) || !$activity instanceof Activity) {
            $activity = [];
        }
        return $this->outSuccessResult([
            'activity' => $activity,
            'statusOptions'   => Activity::$statusOptions,
        ]);
    }


    public function doSave(Request $request)
    {
        $params     = $this->validate($request, [
            'name'           => 'required|string',
            'product_id'     => 'required|integer',
            'full_amount'    => 'required|integer',
            'original_price' => 'nullable|string',
            'price'          => 'nullable|string',
            'valid_day'      => 'required|integer',
            'buy_start_time' => 'nullable|string',
            'buy_end_time'   => 'nullable|string',
            'id'             => 'nullable|integer',
            'status'         => 'nullable|integer',
        ], [
            '.*'                => '保存优惠券模板失败[-1]',
        ]);
        $activity    = $params;
        $res = Activity::add($activity);
        if ($res['code'] == 'fail') {
            return $this->outErrorResult(-1, $res['msg']);
        }
        return $this->outSuccessResult();
    }

    public function changeStatus(Request $request)
    {
        $params     = $this->validate($request, [
            'status' => 'required|in:' . implode(',', array_keys(Activity::$statusDesc)),
            'id'     => 'required|integer',
        ], [
            '.*'     => '更新拼团活动失败[-1]',
        ]);
        $status = $params['status'];
        $id     = $params['id'];
        $activity = Activity::find($id);
        if(!$activity instanceof Activity) {
            return $this->outErrorResult(-1, '更新拼团活动失败[-2]');
        }
        $activity->status = $status;
        $activity->save();
        return $this->outSuccessResult([
            'id'=>$activity->id
        ]);
    }


    public function list(Request $request)
    {
        $activityId = $request->input('activity_id', '');
        $productId  = $request->input('product_id', '');
        $userId     = $request->input('user_id', '');
        $status     = $request->input('status', '');
        $size       = $request->input('size', 50);
        $page       = $request->input('page', 1);
        $query      = Group::orderByDesc('id');
        if (!empty($activityId)) {
            $query = $query->where('activity_id', $activityId);
        }
        if (!empty($productId)) {
            $query = $query->where('product_id', $productId);
        }
        if (!empty($userId)) {
            $query = $query->where('founder_id', $userId);
        }
        if (!empty($status)) {
            $query = $query->where('status', $status);
        }
        if (!empty($payment_begin_time)) {
            $query = $query->where('start_time', '>=', $payment_begin_time);
        }
        if (!empty($payment_end_time)) {
            $query = $query->where('end_time', '<=', $payment_end_time);
        }
        $rewardList = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($rewardList as $item) {
            $recordList[] = [
                'id'          => $item->id,
                'activity_id' => $item->activity_id,
                'product_id'  => $item->product_id,
                'product_name'  => $item->product->title,
                'user_id'     => $item->founder_id,
                'name'        => $item->activity->name,
                'nick_name'   => $item->founder->nick_name,
                'full_amount' => $item->full_amount,
                'price'       => $item->price,
                'status_desc' => $item->status_desc,
                'start_time'  => Carbon::parse($item->start_time)->toDateTimeString(),
                'end_time'    => Carbon::parse($item->end_time)->toDateTimeString(),
            ];
        }
        return $this->outSuccessResult([
            'records'       => $recordList,
            'statusOptions' => Group::$statusOptions,
            'current_page'  => $rewardList->currentPage(),
            'per_page'      => $rewardList->perPage(),
            'total'         => $rewardList->total(),
        ]);
    }


    public function getTeam(Request $request)
    {
        $groupId = $request->input('group_id', '');
        $activityId = $request->input('activity_id', '');
        $productId  = $request->input('product_id', '');
        $userId     = $request->input('user_id', '');
        $orderId     = $request->input('order_id', '');
        $size       = $request->input('size', 50);
        $page       = $request->input('page', 1);
        $query      = GroupTeam::orderByDesc('id');
        if (!empty($groupId)) {
            $query = $query->where('group_id', $groupId);
        }
        if (!empty($activityId)) {
            $query = $query->where('activity_id', $activityId);
        }
        if (!empty($productId)) {
            $query = $query->where('product_id', $productId);
        }
        if (!empty($userId)) {
            $query = $query->where('user_id', $userId);
        }
        if (!empty($order_id)) {
            $query = $query->where('order_id', $orderId);
        }
        $rewardList = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($rewardList as $item) {
            $recordList[] = [
                'id'            => $item->id,
                'activity_id'   => $item->activity_id,
                'group_id'      => $item->group_id,
                'activity_name' => $item->activity->name,
                'product_id'    => $item->product_id,
                'order_id'      => $item->order_id,
                'product_name'  => $item->product->title,
                'user_id'       => $item->user_id,
                'nick_name'     => $item->user->nick_name,
                'role_desc'     => $item->role_desc,
                'create_time'   => Carbon::parse($item->created_at)->toDateTimeString(),
                'update_time'   => Carbon::parse($item->updated_at)->toDateTimeString(),
            ];
        }
        return $this->outSuccessResult([
            'records'       => $recordList,
            'current_page'  => $rewardList->currentPage(),
            'per_page'      => $rewardList->perPage(),
            'total'         => $rewardList->total(),
        ]);
    }

    public function getImage(Request $request)
    {
        $params     = $this->validate($request, [
            'id'     => 'required|integer',
        ], [
            '.*'     => '获取拼团二维码失败[-1]',
        ]);
        $id     = $params['id'];
        $group = Group::find($id);
        if(!$group instanceof Group) {
            return $this->outErrorResult(-1, '获取拼团二维码失败[-2]');
        }

        $url = '';
        $cacheKey = CacheKey::CK_GROUP_URL.$group->id; 
        $url   = Cache::get($cacheKey);
        if (empty($url)) {
            $page = MiniProgram::PAGE_GROUP_DETAIL."?product_token=".Codec::encodeId($group->product_id)."&group_id=".$group->id; 
            $url = MiniProgram::getInstance()->getWxACode($page, ['width' => 300], true);
            Cache::put($cacheKey, $url, 3600*24*30);
        }
        return $this->outSuccessResult([
            'url'=>$url
        ]);
    }
}
