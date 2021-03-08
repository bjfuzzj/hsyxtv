<?php
/*
 * @Author: bjfuzzj
 * @Date: 2020-09-16 11:01:06
 * @LastEditTime: 2021-01-21 16:36:27
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /sg/songguo/app/Http/Controllers/BackCouponController.php
 */

namespace App\Http\Controllers;

use App\Models\Coupon;
use Illuminate\Http\Request;
use App\Models\CouponTemplate;
use App\Models\CouponTemplateProductRef;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Log;

class BackCouponController extends Controller
{
    public function index(Request $request)
    {
        // $coupons = CouponTemplate::all();
        // return $this->outSuccessResult([
        //     'coupons' => $coupons,
        // ]);


        $couponTemplateId = $request->input('template_id', '');
        $type = $request->input('type', '');
        $status = $request->input('status', '');
        $promoteType = $request->input('promote_type', '');
        $validType = $request->input('valid_type', '');
        $size   = $request->input('size', 50);
        $page   = $request->input('page', 1);
        $query = CouponTemplate::orderByDesc('id');
        if (!empty($couponTemplateId)) {
            $query = $query->where('id', $couponTemplateId);
        }
        if (!empty($name)) {
            $query = $query->where('name','like', '%'.$name.'%');
        }
        if (!empty($status)) {
            $query = $query->where('status', $status);
        }
        if (!empty($type)) {
            $query = $query->where('type', $type);
        }
        if (!empty($promoteType)) {
            $query = $query->where('promote_type', $promoteType);
        }
        if (!empty($validType)) {
            $query = $query->where('valid_type', $validType);
        }
        if (!empty($valid_start_time)) {
            $query = $query->where('valid_start_time', '>=', $valid_start_time);
        }
        if (!empty($valid_end_time)) {
            $query = $query->where('valid_end_time', '<=', $valid_end_time);
        }
        $rewardList = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($rewardList as $item) {
            $recordList[] = [
                'id'                => $item->id,
                'name'              => $item->name,
                'type'              => $item->type,
                'status'            => $item->status,
                'promote_type'      => $item->promote_type,
                'valid_type'        => $item->valid_type,
                'type_desc'         => $item->type_desc,
                'status_desc'       => $item->status_desc,
                'promote_type_desc' => $item->promote_type_desc,
                'valid_type_desc'   => $item->valid_type_desc,
                'cut_value'         => $item->cut_value,
                'cost'              => $item->cost,
                'total'             => $item->total,
                'used_cnt'          => $item->used_cnt,
                'valid_end_time'    => $item->valid_end_time,
                'invalid_cause'     => $item->invalid_cause ?? '',
                'is_show'           => $item->is_show ?? 0,
                'valid_start_time'  => Carbon::parse($item->valid_start_time)->toDateTimeString(),
                'valid_end_time'    => Carbon::parse($item->valid_end_time)->toDateTimeString(),
            ];
        }
        return $this->outSuccessResult([
            'records'            => $recordList,
            'statusOptions'      => CouponTemplate::$statusOptions,
            'typeOptions'        => CouponTemplate::$typeOptions,
            'promoteTypeOptions' => CouponTemplate::$promoteTypeOptions,
            'current_page'       => $rewardList->currentPage(),
            'per_page'           => $rewardList->perPage(),
            'total'              => $rewardList->total(),
        ]);


    }

    public function edit(Request $request)
    {
        $couponTemplateId = $request->input('id', '');
        $couponTemplate = CouponTemplate::find($couponTemplateId);
        if(empty($couponTemplateId) || !$couponTemplate instanceof CouponTemplate) {
            $couponTemplate = [];
        }else{
            $productIds = CouponTemplateProductRef::where('template_id',$couponTemplateId)->pluck('product_id');
            if(!empty($productIds->all())){
                $couponTemplate['product_ids'] = join(',',$productIds->all());
            }
        }
        
        return $this->outSuccessResult([
            'coupon_template' => $couponTemplate,
            'statusOptions'   => CouponTemplate::$statusOptions,
        ]);
    }

    // public function doSave(Request $request)
    // {
    //     $params     = $this->validate($request, [
    //         'coupons'                    => 'required|array',
    //         'coupons.*.name'             => 'required|string',
    //         'coupons.*.type'             => 'required|in:' . implode(',', array_keys(CouponTemplate::$typeDesc)),
    //         'coupons.*.promote_type'     => 'required|in:' . implode(',', array_keys(CouponTemplate::$promoteTypeDesc)),
    //         'coupons.*.product_ids'      => 'nullable|string',
    //         'coupons.*.cut_value'        => 'nullable|string',
    //         'coupons.*.cost'             => 'nullable|string',
    //         'coupons.*.total'            => 'nullable|integer',
    //         'coupons.*.person_limit_num' => 'nullable|integer',
    //         'coupons.*.valid_type'       => 'required|in:' . implode(',', array_keys(CouponTemplate::$validTypeDesc)),
    //         'coupons.*.valid_day'        => 'nullable|integer',
    //         'coupons.*.valid_start_time' => 'nullable|string',
    //         'coupons.*.valid_end_time'   => 'nullable|string',
    //         'coupons.*.id'               => 'nullable|integer',
    //     ], [
    //         '.*'                => '保存优惠券模板失败[-1]',
    //     ]);
    //     $coupons    = $params['coupons'];

    //     $res = CouponTemplate::batchAdd($coupons);
    //     if (!$res) {
    //         return $this->outErrorResult(-1, '保存banner失败[-2]');
    //     }
    //     return $this->outSuccessResult();
    // }

    public function doSave(Request $request)
    {
        $params     = $this->validate($request, [
            'name'             => 'required|string',
            'type'             => 'required|in:' . implode(',', array_keys(CouponTemplate::$typeDesc)),
            'promote_type'     => 'required|in:' . implode(',', array_keys(CouponTemplate::$promoteTypeDesc)),
            'product_ids'      => 'nullable|string',
            'cut_value'        => 'nullable|string',
            'cost'             => 'nullable|string',
            'total'            => 'nullable|integer',
            'used_cnt'         => 'nullable|integer',
            'person_limit_num' => 'nullable|integer',
            'valid_type'       => 'required|in:' . implode(',', array_keys(CouponTemplate::$validTypeDesc)),
            'valid_day'        => 'nullable|integer',
            'valid_start_time' => 'nullable|string',
            'valid_end_time'   => 'nullable|string',
            'id'               => 'nullable|integer',
            'status'           => 'nullable|integer',
            'src'              => 'nullable|string',
            'is_show'          => 'nullable|integer',
        ], [
            '.*'                => '保存优惠券模板失败[-1]',
        ]);
        $coupon    = $params;
        $res = CouponTemplate::add($coupon);
        if (!$res) {
            return $this->outErrorResult(-1, '保存优惠券模板失败[-2]');
        }
        return $this->outSuccessResult();
    }

    public function changeStatus(Request $request){
        $params     = $this->validate($request, [
            'status' => 'required|in:' . implode(',', array_keys(CouponTemplate::$statusDesc)),
            'id'     => 'required|integer',
        ], [
            '.*'     => '更新优惠券模板失败[-1]',
        ]);
        $status = $params['status'];
        $id     = $params['id'];
        $couponTemplate = CouponTemplate::find($id);
        if(!$couponTemplate instanceof CouponTemplate) {
            return $this->outErrorResult(-1, '更新优惠券模板失败[-2]');
        }
        $couponTemplate->status = $status;
        $couponTemplate->save();
        return $this->outSuccessResult([
                'id'=>$couponTemplate->id
        ]);
    }


    public function detail(Request $request)
    {
        $couponTemplateId = $request->input('template_id', '');
        $userId           = $request->input('user_id', '');
        $orderId          = $request->input('oid_like', '');
        $name             = $request->input('name', '');
        $status           = $request->input('status', '');
        $type             = $request->input('type', '');
        $size             = $request->input('size', 50);
        $page             = $request->input('page', 1);
        $query            = Coupon::orderByDesc('id')->with(['user','order']);
        if (!empty($couponTemplateId)) {
            $query = $query->where('template_id', $couponTemplateId);
        }
        if (!empty($userId)) {
            $query = $query->where('user_id', $userId);
        }
        if (!empty($order_id)) {
            $query = $query->where('order_id','like', '%'.$orderId.'%');
        }
        
        if (!empty($name)) {
            $query = $query->where('name','like', '%'.$name.'%');
        }
        if (!empty($status)) {
            $query = $query->where('status', $status);
        }
        if (!empty($type)) {
            $query = $query->where('type', $type);
        }
        if (!empty($payment_begin_time)) {
            $query = $query->where('order_time', '>=', $payment_begin_time);
        }
        if (!empty($payment_end_time)) {
            $query = $query->where('order_time', '<=', $payment_end_time);
        }
        $rewardList = $query->paginate($size, ['*'], 'page', $page);
        $recordList = [];
        foreach ($rewardList as $item) {
            $recordList[] = [
                'id'             => $item->id,
                'order_id'       => $item->order_id,
                'user_id'        => $item->user_id,
                'template_id'    => $item->template_id,
                'name'           => $item->name,
                'type_desc'      => $item->type_desc,
                'cut_value'      => $item->cut_value,
                'cost'           => $item->cost,
                'status_desc'    => $item->status_desc,
                'valid_end_time' => $item->valid_end_time,
                'invalid_cause'  => $item->invalid_cause ?? '',
                'order_time'     => Carbon::parse($item->use_time)->toDateTimeString(),
                'created_at'     => Carbon::parse($item->created_at)->toDateTimeString(),
            ];
        }
        return $this->outSuccessResult([
            'records'       => $recordList,
            'statusOptions' => Coupon::$statusOptions,
            'current_page'  => $rewardList->currentPage(),
            'per_page'      => $rewardList->perPage(),
            'total'         => $rewardList->total(),
        ]);
    }
    
}
