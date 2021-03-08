<?php

namespace App\Http\Controllers;

use App\Helper\Codec;
use App\Models\Activity;
use App\Models\Order;
use App\Models\OrderLogistics;
use App\Models\Product;
use App\Models\User;
use App\Models\WxUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Models\Coupon;
use App\Models\Group;

class OrderController extends Controller
{
    /**
     * @api {post} /api/order/confirm 确认订单页
     * @apiName confirm
     * @apiGroup Order
     *
     * @apiParam {String} product_token 产品token
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {String} data.product_token 产品Token
     * @apiSuccess {String} data.title 产品标题
     * @apiSuccess {Float} data.price 产品价格
     * @apiSuccess {String} data.main_img 产品主图
     * @apiSuccess {Number} data.buy_min_num 最少购买数量
     * @apiSuccess {Number} data.buy_max_num 最多购买数量
     * @apiSuccess {Number} data.buy_step 每次加减数量
     * @throws \Illuminate\Validation\ValidationException
     */
    public function confirm(Request $request)
    {
        $params    = $this->validate($request, [
            '_token'        => 'required|string',
            'product_token' => 'required|string',
            'group_id'      => 'nullable|integer',
        ], [
            '*' => '加载数据失败[-1]'
        ]);
        $productId = Codec::decodeId($params['product_token']);
        if (empty($productId)) {
            return $this->outErrorResultApi(500, '加载数据失败[-2]');
        }
      
        $product = Product::find($productId);
        if (!$product instanceof Product) {
            return $this->outErrorResultApi(500, '加载数据失败[-3]');
        }
        $openId    = WxUser::getOpenId($params['_token']);
        $wxUser = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(500, '加载数据失败[-4]');
        }
        
        $data = $product->getConfirmOrderData();
        if($product->isGroupType()){
            //入团
            if(!empty($params['group_id'])){
                $group = Group::find($params['group_id']);
                if($group instanceof Group && $group->product_id == $product->id){
                    $data['price'] = round($group->price, 2);
                    $data['buy_min_num'] = 1 ;
                    $data['buy_max_num'] = 1 ;
                    $data['buy_step'] = 1 ;
                }
            }else{
                //新团需要活动有效期内
                $activity = Activity::where('product_id',$product->id)->where('status',Activity::STATUS_VALID)->first();
                if($activity instanceof Activity && $activity->product_id == $product->id){
                    $data['price'] = round($activity->price, 2);
                    $data['buy_min_num'] = 1 ;
                    $data['buy_max_num'] = 1 ;
                    $data['buy_step'] = 1 ;
                }
            }
        }
        //非团购商品才可以用优惠券
        $data['usable_coupons'] = [];
        if(!$product->isGroupType()){
            $data['usable_coupons'] = Coupon::getUsableCouponByProduct($wxUser->user_id, $product->id);
        }
        
        //$data['tips'] = '生鲜水果，不退不换，请谨慎购买。';
        $data['tips'] = '多功能饮品机，可冲咖啡、奶泡等饮品';
        return $this->outSuccessResultApi($data);
    }

    /**
     * @api {post} /api/order/submit_pay 提交订单并发起支付
     * @apiName submitAndPay
     * @apiGroup Order
     *
     * @apiParam {String} _token 个人身份token
     * @apiParam {String} product_token 产品token
     * @apiParam {Number} buy_num 购买数量
     * @apiParam {String} receiver_name 收货人姓名
     * @apiParam {String} receiver_mobile 收货人联系方式
     * @apiParam {String} receiver_province 收货地址省份
     * @apiParam {String} receiver_city 收货地址城市
     * @apiParam {String} receiver_district 收货地址地区
     * @apiParam {String} receiver_address 收货详情地址
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Number} data.order_id 订单ID
     * @apiSuccess {Object} data.pay_data 支付数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function submitAndPay(Request $request)
    {
        Log::info(__CLASS__ . ':' . __FUNCTION__, $request->all());

        $params = $this->validate($request, [
            '_token'            => 'required|string',
            'product_token'     => 'required|string',
            'buy_num'           => 'required|integer',
            'receiver_name'     => 'required|string',
            'receiver_mobile'   => 'required|string',
            'receiver_province' => 'required|string',
            'receiver_city'     => 'required|string',
            'receiver_district' => 'required|string',
            'receiver_address'  => 'required|string',
            'remark'            => 'nullable|string',
            'remak'             => 'nullable|string',
            'coupon_id'         => 'nullable|integer',
            'group_id'          => 'nullable|integer',
        ], [
            'buy_num.*'           => '亲，购买数量有误😯',
            'receiver_name.*'     => '亲，请填写收货人姓名😯',
            'receiver_mobile.*'   => '亲，请填写收货人联系方式😯',
            'receiver_province.*' => '亲，请填写收货地址😯',
            'receiver_city.*'     => '亲，请填写收货地址😯',
            'receiver_district.*' => '亲，请填写收货地址😯',
            'receiver_address.*'  => '亲，请填写收货地址😯',
            '*'                   => '发起支付失败[-1]'
        ]);
        if (!empty($params['remak'])) {
            $params['remark'] = $params['remak'];
        }

        $productId = Codec::decodeId($params['product_token']);
        $openId    = WxUser::getOpenId($params['_token']);
        $wxUser    = WxUser::where('open_id', $openId)->first();
        if (!$wxUser instanceof WxUser) {
            return $this->outErrorResultApi(500, '发起支付失败[-2]');
        }
        $user = User::find($wxUser->user_id);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '发起支付失败[-3]');
        }
        $product = Product::find($productId);
        if (!$product instanceof Product) {
            return $this->outErrorResultApi(500, '发起支付失败[-4]');
        }
        if ($product->isSellOut()) {
            return $this->outErrorResultApi(500, '亲，产品已售罄，可以看看其他产品😯');
        }
        if ($params['buy_num'] > $product->buy_max_num) {
            return $this->outErrorResultApi(500, "亲，该产品最多购买{$product->buy_max_num}件😯");
        }
        if ($params['buy_num'] < $product->buy_min_num) {
            return $this->outErrorResultApi(500, "亲，该产品最少购买{$product->buy_min_num}件😯");
        }
        if ((($params['buy_num'] - $product->buy_min_num) % $product->buy_step) > 0) {
            return $this->outErrorResultApi(500, '亲，购买数量有误😯');
        }
        if ($product->buy_limit_num > 0) {
            $orderCnt = Order::where('product_id', $productId)->where('user_id', $user->id)
                ->whereNotIn('status', [Order::STATUS_CANCEL, Order::STATUS_EXPIRED, Order::STATUS_REFUND])
                ->count();
            if ($orderCnt >= $product->buy_limit_num) {
                return $this->outErrorResultApi(500, "亲，该产品每人限购{$product->buy_limit_num}想😯");
            }
        }
        //优惠券处理
        $coupon = null;
        if (!empty($params['coupon_id'])) {
            $coupon = Coupon::find($params['coupon_id']);
            if (!$coupon instanceof Coupon || !$coupon->isNotUse()) {
                return $this->outErrorResult(500, '亲，该优惠券已使用或已失效');
            }
        }
        
        if($product->isGroupType()){
            //先判断是否是拼团加进来的，这个时候不管activiy的有效期
            if (!empty($params['group_id'])) {
                $group = Group::find($params['group_id']);
                if (!$group instanceof Group || $group->isFail() || $group->isSucc()) {
                    return $this->outErrorResult(500, '亲，该拼团已满或已失效');
                }
            }else{
                $activity = Activity::where('product_id',$product->id)->where('status',Activity::STATUS_VALID)->first();
                if (!$activity instanceof Activity || $activity->isInValid()) {
                    return $this->outErrorResult(500, '亲，该活动已失效');
                }
            }
        }
        // 创建订单
        $order = Order::createOrder($user, $product, $params, $coupon);
        if (!$order instanceof Order) {
            return $this->outErrorResultApi(500, '发起支付失败[-4]');
        }

        // 发起支付
        $payData = $order->doPay($openId);
        if (empty($payData)) {
            return $this->outErrorResultApi(500, '发起支付失败[-5]');
        }

        return $this->outSuccessResultApi([
            'order_id' => $order->id,
            'pay_data' => $payData,
        ]);
    }

    /**
     * @api {get} /api/order/list 订单列表
     * @apiName getOrderList
     * @apiGroup Order
     *
     * @apiParam {String} user_token 用户token
     * @apiParam {Number} page 当前第几页
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Number} data.page 当前第几页
     * @apiSuccess {Number} data.has_more 是否存在更多数据 0:不存在 1:存在
     * @apiSuccess {Array} data.list 记录列表
     * @apiSuccess {Number} data.list.id 订单ID
     * @apiSuccess {String} data.list.product_token 产品token
     * @apiSuccess {String} data.list.product_title 产品标题
     * @apiSuccess {String} data.list.product_image 产品图片
     * @apiSuccess {Float} data.list.amount_total 订单金额
     * @apiSuccess {Number} data.list.buy_num 购买数量
     * @apiSuccess {String} data.list.status_desc 状态描述
     * @apiSuccess {String} data.list.share_title 分享标题
     * @apiSuccess {String} data.list.share_image 分享图片
     * @apiSuccess {String} data.list.order_time 下单时间
     * @apiSuccess {Number} data.list.show_paid_btn 是否展示去支付按钮 0:否 1:是
     * @apiSuccess {Number} data.list.show_share_btn 是否展示分享按钮 0:否 1:是
     * @apiSuccess {Number} data.list.show_again_buy_btn 是否展示再次购买按钮 0:否 1:是
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getOrderList(Request $request)
    {
        $params = $this->validate($request, [
            'user_token' => 'required|string',
            'page'       => 'nullable|integer'
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);
        $userId = Codec::decodeId($params['user_token']);

        $user = User::find($userId);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-2]');
        }
        $result = Order::getList($userId, $params);
        return $this->outSuccessResultApi($result);
    }

    /**
     * @api {get} /api/order/detail 订单详情
     * @apiName getOrderDetail
     * @apiGroup Order
     *
     * @apiParam {String} order_id 订单ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Number} data.id 订单ID
     * @apiSuccess {String} data.product_token 产品token
     * @apiSuccess {String} data.product_title 产品标题
     * @apiSuccess {String} data.product_image 产品图片
     * @apiSuccess {Float} data.amount_total 订单总金额
     * @apiSuccess {Number} data.buy_num 购买数量
     * @apiSuccess {Number} data.status 订单状态 订单状态 (10:未付款, 20:已付款, 30:已发货, 40:确认收货, 80:已退款, 100:已取消, 110:过期)
     * @apiSuccess {String} data.status_desc 状态描述
     * @apiSuccess {String} data.paid_countdown 支付倒计时
     * @apiSuccess {Object} data.amount_info 金额信息
     * @apiSuccess {String} data.amount_info.title 标题
     * @apiSuccess {Float} data.amount_info.amount 金额
     * @apiSuccess {Number} data.amount_info.is_highlight 是否高亮 0:否 1:是
     * @apiSuccess {Object} data.pay_info 支付信息
     * @apiSuccess {String} data.pay_info.title 标题
     * @apiSuccess {String} data.pay_info.value 支付值
     * @apiSuccess {Array} data.logistic_list 订单物流信息
     * @apiSuccess {Number} data.logistic_list.id 物流地址ID
     * @apiSuccess {String} data.logistic_list.receiver_name 收货人姓名
     * @apiSuccess {String} data.logistic_list.receiver_mobile 收货人电话
     * @apiSuccess {String} data.logistic_list.receiver_address 收货人详细地址
     * @apiSuccess {Number} data.logistic_list.quantity 发货数量
     * @apiSuccess {String} data.logistic_list.statusDesc 状态描述
     * @apiSuccess {Number} data.logistic_list.isCanDelete 是否可以删除 0:否 1:是
     * @apiSuccess {String} data.logistic_list.express_name 快递名字
     * @apiSuccess {String} data.logistic_list.express_no 快递物流号数组
     * @apiSuccess {String} data.share_title 分享标题
     * @apiSuccess {String} data.share_image 分享图片
     * @apiSuccess {Number} data.show_paid_btn 是否展示去支付按钮 0:否 1:是
     * @apiSuccess {Number} data.show_share_btn 是否展示分享按钮 0:否 1:是
     * @apiSuccess {Number} data.show_again_buy_btn 是否展示再次购买按钮 0:否 1:是
     * @apiSuccess {Number} data.show_add_logistic 是否展示新增发货按钮 0:否 1:是
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getOrderDetail(Request $request)
    {
        $params = $this->validate($request, [
            'order_id' => 'required|string',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);

        $order = Order::find($params['order_id']);
        if (!$order instanceof Order) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-2]');
        }

        $result = $order->getDetail();
        return $this->outSuccessResultApi($result);
    }

    /**
     * @api {get} /api/order/send_detail 代付订单详情
     * @apiName getOrderDetail
     * @apiGroup Order
     *
     * @apiParam {String} order_id 订单ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Number} data.id 订单ID
     * @apiSuccess {String} data.product_token 产品token
     * @apiSuccess {String} data.product_title 产品标题
     * @apiSuccess {String} data.product_image 产品图片
     * @apiSuccess {Float} data.amount_total 订单总金额
     * @apiSuccess {Number} data.buy_num 购买数量
     * @apiSuccess {Number} data.status 订单状态 订单状态 (10:未付款, 20:已付款, 30:已发货, 40:确认收货, 80:已退款, 100:已取消, 110:过期)
     * @apiSuccess {String} data.status_desc 状态描述
     * @apiSuccess {String} data.paid_countdown 支付倒计时
     * @apiSuccess {Object} data.amount_info 金额信息
     * @apiSuccess {String} data.amount_info.title 标题
     * @apiSuccess {Float} data.amount_info.amount 金额
     * @apiSuccess {Number} data.amount_info.is_highlight 是否高亮 0:否 1:是
     * @apiSuccess {Object} data.pay_info 支付信息
     * @apiSuccess {String} data.pay_info.title 标题
     * @apiSuccess {String} data.pay_info.value 支付值
     * @apiSuccess {Array} data.logistic_list 订单物流信息
     * @apiSuccess {Number} data.logistic_list.id 物流地址ID
     * @apiSuccess {String} data.logistic_list.receiver_name 收货人姓名
     * @apiSuccess {String} data.logistic_list.receiver_mobile 收货人电话
     * @apiSuccess {String} data.logistic_list.receiver_address 收货人详细地址
     * @apiSuccess {Number} data.logistic_list.quantity 发货数量
     * @apiSuccess {String} data.logistic_list.statusDesc 状态描述
     * @apiSuccess {Number} data.logistic_list.isCanDelete 是否可以删除 0:否 1:是
     * @apiSuccess {String} data.logistic_list.express_name 快递名字
     * @apiSuccess {String} data.logistic_list.express_no 快递物流号数组
     * @apiSuccess {String} data.share_title 分享标题
     * @apiSuccess {String} data.share_image 分享图片
     * @apiSuccess {Number} data.show_paid_btn 是否展示去支付按钮 0:否 1:是
     * @apiSuccess {Number} data.show_share_btn 是否展示分享按钮 0:否 1:是
     * @apiSuccess {Number} data.show_again_buy_btn 是否展示再次购买按钮 0:否 1:是
     * @apiSuccess {Number} data.show_add_logistic 是否展示新增发货按钮 0:否 1:是
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getOrderSendDetail(Request $request)
    {
        $params = $this->validate($request, [
            'order_id'   => 'required|string',
            'user_token' => 'required|string',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);

        $userId = Codec::decodeId($params['user_token']);

        $user = User::find($userId);
        if (!$user instanceof User) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-1]');
        }

        $order = Order::find($params['order_id']);
        if (!$order instanceof Order) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-2]');
        }
        $isSelf = $order->user_id == $userId ? 1 : 0;

                $result        = $order->getDetail();
                $nick_name     = $order->user->nick_name ?? '好友';
        $result['share_title'] = $nick_name."希望你帮他付 ".$order->amount_payable.' 元';
        $result['share_image'] = $order->product->getMainImage() ?? '';
        $result['nick_name']   = $nick_name;
        $result['head_image']  = $order->user->head_image;
        $result['is_self']      = $isSelf;

        if($isSelf == 0) {
            if($result['status'] > Order::STATUS_UNPAID){
                $result['status_desc'] = '此订单已被支付';
                $result['share_title'] = '此订单已被支付';
            }
        }
        return $this->outSuccessResultApi($result);
    }

    /**
     * @api {get} /api/order/logistics_num 获取订单物流数量
     * @apiName getOrderLogisticsNum
     * @apiGroup Order
     *
     * @apiParam {String} order_id 订单ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @apiSuccess {Number} data.order_id 订单ID
     * @apiSuccess {String} data.product_title 产品标题
     * @apiSuccess {String} data.product_image 产品图片
     * @apiSuccess {Float} data.amount_total 订单总金额
     * @apiSuccess {String} data.logistics_num 物流数量
     * @apiSuccess {Number} data.unsend_num 待发货数量
     * @throws \Illuminate\Validation\ValidationException
     */
    public function getOrderLogisticsNum(Request $request)
    {
        $params  = $this->validate($request, [
            'order_id' => 'required|string',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);
        $orderId = $params['order_id'];

        $order = Order::with('product')->find($orderId);
        if (!$order instanceof Order || !$order->product instanceof Product) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-2]');
        }

        $sendQuantity = OrderLogistics::getSendQuantity($orderId);
        $unsendNum    = $order->buy_num - $sendQuantity;


        return $this->outSuccessResultApi([
            'order_id'      => $orderId,
            'product_title' => $order->product->title,
            'product_image' => $order->product->getMainImage(),
            'amount_total'  => $order->amount_total,
            'logistics_num' => "共{$order->buy_num}件 / 已发货{$sendQuantity}件 / 待发货{$unsendNum}件",
            'unsend_num'    => $unsendNum,
        ]);
    }

    /**
     * @api {post} /api/order/add_logistics 添加发货地址
     * @apiName addOrderLogistics  
     * @apiGroup Order
     *
     * @apiParam {String} order_id 订单ID
     * @apiParam {String} receiver_name 收货人姓名
     * @apiParam {String} receiver_mobile 收货人联系方式
     * @apiParam {String} receiver_province 收货地址省份
     * @apiParam {String} receiver_city 收货地址城市
     * @apiParam {String} receiver_district 收货地址地区
     * @apiParam {String} receiver_address 收货详情地址
     * @apiParam {Number} quantity 发货数量
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @throws \Illuminate\Validation\ValidationException
     */
    public function addOrderLogistics(Request $request)
    {
        $params  = $this->validate($request, [
            'order_id'          => 'required|string',
            'receiver_name'     => 'required|string',
            'receiver_mobile'   => 'required|string',
            'receiver_province' => 'required|string',
            'receiver_city'     => 'required|string',
            'receiver_district' => 'required|string',
            'receiver_address'  => 'required|string',
            'quantity'          => 'required|integer',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);
        $orderId = $params['order_id'];

        $order = Order::find($orderId);
        if (!$order instanceof Order) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-2]');
        }

        $sendQuantity = OrderLogistics::getSendQuantity($orderId);
        $unsendNum    = $order->buy_num - $sendQuantity;
        if ($unsendNum < $params['quantity']) {
            return $this->outErrorResultApi(500, "亲，最多可发货{$unsendNum}件哦");
        }

        OrderLogistics::add($orderId, $params);
        return $this->outSuccessResultApi();
    }

    /**
     * @api {post} /api/order/del_logistics 删除发货地址
     * @apiName addOrderLogistics
     * @apiGroup Order
     *
     * @apiParam {String} orderId 订单ID
     * @apiParam {Int} id 物流ID
     *
     * @apiSuccess {String} errorCode 操作返回code 0为正常，其他则为异常
     * @apiSuccess {String} errorMsg 操作返回消息
     * @apiSuccess {Object} data 返回数据
     * @throws \Exception
     * @throws \Illuminate\Validation\ValidationException
     */
    public function delOrderLogistics(Request $request)
    {
        $params = $this->validate($request, [
            'orderId' => 'required|string',
            'id'      => 'nullable|integer',
        ], [
            '*' => '加载数据失败，请稍后重试[-1]',
        ]);

        if (!empty($params['id'])) {
            $orderLogistics = OrderLogistics::where('id', $params['id'])->where('order_id', $params['orderId'])->first();
        } else {
            $orderLogistics = OrderLogistics::where('order_id', $params['orderId'])->first();
        }

        if (!$orderLogistics instanceof OrderLogistics) {
            return $this->outErrorResultApi(500, '加载数据失败，请稍后重试[-2]');
        }

        $orderLogistics->delete();
        return $this->outSuccessResultApi();
    }

}
