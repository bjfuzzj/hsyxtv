<?php
/*
 * @Author: your name
 * @Date: 2020-12-28 14:39:55
 * @LastEditTime: 2021-02-20 17:35:19
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Http/Controllers/CouponController.php
 */

namespace App\Http\Controllers;

use App\Http\Resources\CouponResource;
use Illuminate\Http\Request;
use App\Models\Coupon;
use App\Models\CouponTemplate;
use App\Models\OpLog;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use App\Models\WxUser;
use App\Helper\Codec;

class CouponController extends Controller
{
    /**
     * @api {post} /api/coupon/receive 我的
     * @apiName receiveCoupon
     * @apiGroup Coupon
     *
     * @apiParam {Integer} template_id 优惠券模板ID.
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息、
     * @throws \Illuminate\Validation\ValidationException
     */
    public function receiveCoupon(Request $request)
    {
        $params = $this->validate($request, [
            '_token'        => 'required|string',
            'template_id' => 'required|integer',
        ], [
            '*' => '领取失败，服务器异常',
        ]);
        Log::info(__CLASS__ . '_' . __FUNCTION__, $params);

        $openId    = WxUser::getOpenId($params['_token']);
        //$user = $this->getCurrUser();
        // if (!$user instanceof User) {
        //     return $this->outErrorResultApi(-1, '领取失败-1');
        // }
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(-1, '领取失败-1');
        }
        $user   = User::find($wxUser->user_id);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(-1, '领取失败-4');
        }

        $template = CouponTemplate::find($params['template_id']);
        if (!$template instanceof CouponTemplate) {
            return $this->outErrorResultApi(-1, '领取失败-2');
        }

        $rest = $template->receiveCoupon($user);
        if (empty($rest['isSuccess'])) {
            return $this->outErrorResultApi(-1, $rest['msg'] ?? '领取失败-3');
        }

        $log = "用户领取优惠券 用户ID:{$user->id} 昵称:{$user->nick_name}";
        OpLog::add('CouponTemplate', $template->id, $log, OpLog::OPER_TYPE_USER, $user->id);

        $result = $template->getReceiveResult($user->id);
        return $this->outSuccessResultApi($result);
    }

    /**
     * @api {get} /api/coupon/list 用户优惠券列表
     * @apiName getList
     * @apiGroup Coupon
     *
     * @apiParam {Integer} status 状态 1:未使用 2:已使用 3:已失效
     * @apiParam {Integer} page 当前页
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Number} data.per_page 每页数据
     * @apiSuccess {Number} data.current_page 当前页
     * @apiSuccess {Number} data.total 总数
     * @apiSuccess {Number} data.has_more 是否还有更多数据 1:是 0:否
     * @apiSuccess {Array} data.list 优惠券列表数据
     * @apiSuccess {Number} data.list.id 优惠ID
     * @apiSuccess {Number} data.list.user_id 优惠券用户ID
     * @apiSuccess {String} data.list.status_desc 状态描述
     * @apiSuccess {String} data.list.name 优惠券名称
     * @apiSuccess {Float} data.list.cut_value 优惠金额或折扣
     * @apiSuccess {Float} data.list.cost 满减条件
     * @apiSuccess {Float} data.list.discount_unit 折扣单位 元/着
     * @apiSuccess {Float} data.list.discount_desc 折扣描述
     * @apiSuccess {String} data.list.valid_end_time_zh 有效截止时间
     * @apiSuccess {Number} data.list.is_used 是否使用 1:是 0:否
     * @apiSuccess {String} data.list.use_time_zh 使用时间
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getList(Request $request)
    {
        $params = $this->validate($request, [
            '_token' => 'required|string',
            'status' => 'required|integer',
            'page'   => 'nullable|integer',
        ], [
            '*' => '获取优惠券失败',
        ]);
        $openId    = WxUser::getOpenId($params['_token']);
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(-1, '获取优惠券失败-1');
        }
        $user   = User::find($wxUser->user_id);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(-1, '获取优惠券失败-4');
        }
        $params['size'] = 20;

        $result = Coupon::getUserCouponList($user->id, $params);
        return $this->outSuccessResult($result);
    }


    /**
     * @api {get} /api/coupon/sendDetail 获取赠送的优惠券信息
     * @apiName sendDetail
     * @apiGroup Coupon
     *
     * @apiParam {Integer} _token 当前登录者的 token
     * @apiParam {Integer} send_token 赠送优惠券的用户 token
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function sendDetail(Request $request){
        $params = $this->validate($request, [
            'send_token' => 'required|string',
            'coupon_id'  => 'required|integer',
        ], [
            '*' => '获取优惠券信息失败',
        ]);
        $sendUserId = Codec::decodeId($params['send_token']);
        $sendUser = User::find($sendUserId);
        if (!$sendUser instanceof User) {
            return $this->outErrorResultApi(-1, '获取优惠券信息失败-2');
        }

        $coupon = Coupon::with('user')->find($params['coupon_id']);
        if(!$coupon instanceof Coupon){
            return $this->outErrorResultApi(-1, '获取优惠券信息失败-3');
        }
        if($coupon->user_id != $sendUser->id) {
            return $this->outErrorResultApi(-1, '获取优惠券信息失败-4');
        }

        if($coupon->isNotUse()){
            $coupon->setTransing();
        }
        //$result = CouponResource::collection($coupon);
        $result = CouponResource::make($coupon)->toArray($request);
        $result['intro_content'] = "1. 本券可在 00:00 - 23:59 使用。\n
        2. 本券使用支持线上、线下使用。\n
        3. 本优惠券不与其他优惠券同享。\n";
        return $this->outSuccessResultApi($result);
    }


    public function changeCoupon(Request $request){
        
        $params = $this->validate($request, [
            '_token'     => 'required|string',
            'send_token' => 'required|string',
            'coupon_id'  => 'required|integer',
        ], [
            '*' => '获取优惠券信息失败',
        ]);
        $sendUserId = Codec::decodeId($params['send_token']);
        $sendUser = User::find($sendUserId);
        if (!$sendUser instanceof User) {
            Log::info('获取优惠券信息失败,user不存在'.print_r($params,1));
            return $this->outErrorResultApi(-1, '获取优惠券信息失败-2');
        }

        $openId    = WxUser::getOpenId($params['_token']);
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            Log::info('获取优惠券信息失败,wxuser不存在'.print_r($params,1));
            return $this->outErrorResultApi(-1, '获取优惠券信息失败-3');
        }
        
        // $user   = User::find($wxUser->user_id);
        // if (!$user instanceof User) {
        //     return $this->outErrorResultApi(-1, '获取优惠券信息失败-4');
        // }

        $coupon = Coupon::find($params['coupon_id']);
        if(!$coupon instanceof Coupon){
            Log::info('获取优惠券信息失败,coupon不存在'.print_r($params,1));
            return $this->outErrorResultApi(-1, '获取优惠券信息失败-5');
        }

        if($coupon->user_id != $sendUserId){
            Log::info('优惠券已被其他人领取'.print_r($params,1));
            return $this->outErrorResultApi(-1, '优惠券已被其他人领取');
        }

        if(!empty($wxUser->user_id) && $wxUser->user_id == $sendUser->id){
            Log::info('自己不能领取自己的优惠券哦！'.print_r($params,1));
            return $this->outErrorResultApi(-1, '自己不能领取自己的优惠券哦！');
        }

        //$rest = $coupon->trans($wxUser->user_id);
        $rest = $coupon->trans($wxUser);
        if (empty($rest['isSuccess'])) {
            return $this->outErrorResultApi(-1, $rest['msg'] ?? '领取优惠券失败-6');
        }

        $log = "用户获取优惠券 优惠券原始ID:{$coupon->id} 原始用户ID:{$coupon->user_id} 新优惠券ID:{$rest['data']}  领取微信用户ID:{$wxUser->id} 昵称:{$wxUser->nick_name}";
        OpLog::add('CouponTrans', $coupon->id, $log, OpLog::OPER_TYPE_USER, $wxUser->id);
        
        $result = ['coupon_id'=>$rest['data']];
        return $this->outSuccessResultApi($result);

       
    }

}