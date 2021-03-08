<?php

namespace App\Http\Controllers;

use App\Helper\Codec;
use App\Helper\MiniProgram;
use App\Models\CouponTemplate;
use App\Models\DistributorIncome;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Models\UserRelationship;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class UserController extends Controller
{

    /**
     * @api {get} /api/user/index 我的
     * @apiName index
     * @apiGroup User
     *
     * @apiParam {String} _token 用户登录Token
     * @apiParam {String} user_token 用户user_token
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.nick_name 用户昵称
     * @apiSuccess {String} data.head_image 用户头像
     * @apiSuccess {Number} data.integrals 积分
     * @apiSuccess {Number} data.is_distributor 是否分销员 0:否 1:是
     * @apiSuccess {Float} data.income_total 累计总收入
     * @apiSuccess {Number} data.team_people_num 团队人数
     * @apiSuccess {Array} data.customer_tip 咨询客服提示文案
     * @apiSuccess {Array} data.customer_wechat 客服微信号
     * @apiSuccess {String} data.member_level 会员等级  0:无 1: 会员 2:银牌会员 3:金牌会员 4:钻石会员 5:皇冠会员
     * @apiSuccess {String} data.member_desc 会员等级描述
     * @apiSuccess {String} data.member_tip 提示文案，如果提示文案为空 就没有弹窗提示
     * @apiSuccess {String} data.quanyi_link 权益链接，如果为空不展示
     * @apiSuccess {Number} data.is_replenish_mobile 是否需要补充手机号 0:否 1:是
     * @apiSuccess {Object} data.order_info 最新一个订单
     * @apiSuccess {Number} data.order_info.id 订单ID
     * @apiSuccess {String} data.order_info.product_token 产品token
     * @apiSuccess {String} data.order_info.product_title 产品标题
     * @apiSuccess {String} data.order_info.product_image 产品图片
     * @apiSuccess {Float} data.order_info.amount_total 订单金额
     * @apiSuccess {Number} data.order_info.buy_num 购买数量
     * @apiSuccess {String} data.order_info.status_desc 状态描述
     * @apiSuccess {String} data.order_info.share_title 分享标题
     * @apiSuccess {String} data.order_info.share_image 分享图片
     * @apiSuccess {String} data.order_info.order_time 下单时间
     * @apiSuccess {Number} data.order_info.show_paid_btn 是否展示去支付按钮 0:否 1:是
     * @apiSuccess {Number} data.order_info.show_share_btn 是否展示分享按钮 0:否 1:是
     * @apiSuccess {Number} data.order_info.show_again_buy_btn 是否展示再次购买按钮 0:否 1:是
     * @throws \Illuminate\Validation\ValidationException
     */
    public function index(Request $request)
    {
        $params = $this->validate($request, [
            '_token'     => 'required|string',
            'user_token' => 'required|string',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);
        $userId = Codec::decodeId($params['user_token']);

        $user = User::find($userId);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '用户不存在，请重试[-1]');
        }

        $customerWeChatId = env('CUSTOMER_WECHAT_ID', '');

        $userLevel = $user->getUserLevel();

        $isReplenishMobile = 0;
        if ($user->is_distributor && empty($user->mobile)) {
            $isReplenishMobile = 1;
        }

        $data   = [
            'nick_name'           => $user->nick_name,
            'head_image'          => $user->head_image,
            'integrals'           => $user->integrals,
            'is_distributor'      => $user->is_distributor,
            'income_total'        => 0,
            'team_people_num'     => UserRelationship::getTeamPeopleNum($userId),
            'customer_tip'        => "添加客服微信号：{$customerWeChatId} 咨询",
            'customer_wechat'     => $customerWeChatId,
            'member_level'        => $userLevel,
            'member_desc'         => User::$userLevelDesc[$userLevel] ?? '',
            'member_tip'          => '',
            'quanyi_link'         => 'https://mp.weixin.qq.com/s/6tOUwdcwb5IT--CN5W9umQ',
            // 'tel_no'              => '010-6653520',
            'tel_no'              => '',
            'is_replenish_mobile' => $isReplenishMobile
        ];
        $income = DistributorIncome::where('user_id', $userId)->first();
        if ($income instanceof DistributorIncome) {
            $data['income_total'] = round($income->income_total, 2);
        }

        // 最近一个订单信息
        $orderStatus = [Order::STATUS_UNPAID, Order::STATUS_PAID, Order::STATUS_SHIPPED, Order::STATUS_RECEIVED];
        $order       = Order::with('product')->where('user_id', $userId)
            ->whereIn('status', $orderStatus)->orderByDesc('id')->first();
        if ($order instanceof Order && $order->product instanceof Product) {
            $data['order_info'] = $order->formatListData();
        }

        //提示文案
        $data['toastContent'] = '您不是代理商，请联系客服，申请正式代理商享受分享返利';
        return $this->outSuccessResultApi($data);
    }

    /**
     * @api {post} /api/user/replenish_mobile 补充手机号
     * @apiName replenishMobile
     * @apiGroup User
     *
     * @apiParam {String} user_token 用户user_token
     * @apiParam {String} mobile 用户手机号
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function replenishMobile(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
            'mobile'     => 'required|regex:/^1[3456789][0-9]{9}$/',
        ], [
            'mobile.*' => '亲，请填写正确的手机号',
            '*'        => '更新数据失败，请稍后重试[-1]',
        ]);
        Log::info(__CLASS__ . '_' . __FUNCTION__, $params);
        $userId = Codec::decodeId($params['user_token']);

        $user = User::find($userId);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '更新数据失败，请稍后重试[-2]');
        }
        $rest = $user->replenishIdCardAuth($params);
        if (!$rest) {
            return $this->outErrorResultApi(500, '更新数据失败，请稍后重试[-3]');
        }
        return $this->outSuccessResultApi();
    }

    /**
     * @api {post} /api/user/replenish_idcard 补充身份认证数据
     * @apiName replenishIdCard
     * @apiGroup User
     *
     * @apiParam {String} user_token 用户user_token
     * @apiParam {String} real_name 真实姓名
     * @apiParam {String} mobile 用户手机号
     * @apiParam {String} id_card 身份证号
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function replenishIdCard(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
            'real_name'  => 'required|string',
            'mobile'     => 'required|regex:/^1[3456789][0-9]{9}$/',
            'id_card'    => 'required|string',
        ], [
            'real_name.*' => '亲，请填写中文姓名',
            'mobile.*'    => '亲，请填写正确的手机号',
            'id_card.*'   => '亲，请填写正确的身份证号',
            '*'           => '更新数据失败，请稍后重试[-1]',
        ]);
        Log::info(__CLASS__ . '_' . __FUNCTION__, $params);
        $userId = Codec::decodeId($params['user_token']);

        if (!preg_match("/^[\x{4e00}-\x{9fa5}]+$/u", $params['real_name'])) {
            return $this->outErrorResultApi(500, '亲，请填写中文姓名');
        }
        if (!preg_match('/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $params['id_card'])) {
            return $this->outErrorResultApi(500, '亲，请填写正确的身份证号');
        }

        $user = User::find($userId);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '更新数据失败，请稍后重试[-2]');
        }
        $rest = $user->replenishIdCardAuth($params);
        if (!$rest) {
            return $this->outErrorResultApi(500, '更新数据失败，请稍后重试[-3]');
        }
        return $this->outSuccessResultApi();
    }

    /**
     * @api {get} /api/user/teams 我的团队
     * @apiName getTeamList
     * @apiGroup User
     *
     * @apiParam {String} user_token 用户user_token
     * @apiParam {String} type 列表类型默认'all' all:总榜 month:月榜
     * @apiParam {Number} page 当前第几页
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.nick_name 用户昵称
     * @apiSuccess {String} data.head_image 用户头像
     * @apiSuccess {Float} data.total_rebate 返佣金额
     * @apiSuccess {String} data.order_info.join_time 加入时间
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getTeamList(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
            'type'       => 'nullable|string',
            'page'       => 'nullable|integer',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);
        $userId = Codec::decodeId($params['user_token']);

        $result = UserRelationship::getTeamList($userId, $params);
        $result['shareTitle'] = '邀请好友';
        $result['shareImg'] = '';
        return $this->outSuccessResultApi($result);
    }


    /**
     * @api {get} /api/user/coupons 我的优惠券
     * @apiName getCouponList
     * @apiGroup User
     *
     * @apiParam {String} user_token 用户user_token
     * @apiParam {Number} page 当前第几页
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.lists 列表数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getCouponList(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
            'page'       => 'nullable|integer',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);
        $userId = Codec::decodeId($params['user_token']);
        $user = User::find($userId);
        $nickName = $user->nick_name ?? '好友';

        $result = CouponTemplate::getUserCoupon($userId, $params);
        //$result['shareTitle'] = $nickName.'送你一张优惠券，快去领取吧！';
        $result['shareTitle'] = '亲，'.$nickName.'给你送了一张优惠券: )';
        $result['shareImg'] = 'https://img.66gou8.com/20fa333981a9253e81d42101ebbe1510.jpeg';
        return $this->outSuccessResultApi($result);
    }

}
