<?php

namespace App\Models;

use App\Helper\Codec;
use App\Helper\PushMessage;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class Order extends Model
{
    //const UPDATED_AT = null;
    //const CREATED_AT = 'create_time';
    protected $table = 'orders';

    const ADMIN_KEY = '进度反馈';
    public $incrementing = false;

    protected $appends = ['status_desc'];

    // 订单状态 (10:未付款, 20:已付款, 30:已发货, 40:已收货, 78:退款中 80:已退款, 100:已取消, 110:过期)
    const STATUS_UNPAID   = 10; //未支付
    const STATUS_PAID     = 20; //已支付
    const STATUS_SHIPPED  = 30; //已发货
    const STATUS_RECEIVED = 40; //确认收货
    const STATUS_REFUNDING = 78; //退款中
    const STATUS_REFUND   = 80; //已退款
    const STATUS_CANCEL   = 100;//已取消
    const STATUS_EXPIRED  = 110;//已过期
    public static $order_status_desc = [
        self:: STATUS_UNPAID   => '待付款',
        self:: STATUS_PAID     => '已付款',
        self:: STATUS_SHIPPED  => '已发货',
        self:: STATUS_RECEIVED => '已收货',
        self:: STATUS_REFUNDING => '退款中',
        self:: STATUS_REFUND   => '已退款',
        self:: STATUS_CANCEL   => '已取消',
        self:: STATUS_EXPIRED  => '已过期'
    ];
    public static $statusDesc = [
        self::STATUS_UNPAID   => '待付款',
        self::STATUS_PAID     => '已付款',
        self::STATUS_SHIPPED  => '已发货',
        self::STATUS_RECEIVED => '已收货',
        self::STATUS_REFUND   => '已退款',
        self::STATUS_CANCEL   => '已取消',
        self::STATUS_EXPIRED  => '已过期'
    ];
    // 订单失效原因
    const INVALID_USER_CANCEL  = 1;
    const INVALID_ADMIN_CANCEL = 2;
    const INVALID_EXPIRED      = 3;
    public static $invalidCauseDesc = [
        self::INVALID_USER_CANCEL  => '用户取消订单',
        self::INVALID_ADMIN_CANCEL => '管理员取消订单',
        self::INVALID_EXPIRED      => '过期',
    ];
    //默认过期时间（单位：秒）
    const DEFAULT_EXPIRE_TIME = 3600;

    public function behalfPayUser()
    {
        return $this->belongsTo(User::class, 'behalf_pay_user_id');
    }

    
    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function product()
    {
        return $this->belongsTo(Product::class, 'product_id');
    }

    public function group()
    {
        return $this->belongsTo(Group::class, 'group_id');
    }

    public function logistics()
    {
        return $this->hasMany(OrderLogistics::class);
    }

    public static function createOrder(User $user, Product $product, $data, $coupon = null)
    {
        $orderId     = OrderGen::generateOrderId();

        $price = $product->price;

        $groupId = 0;

        //团购价格处理(新建或者参团)
        if($product->isGroupType()){
            $activity = Activity::where('product_id',$product->id)->where('status',Activity::STATUS_VALID)->first();
            $price = $activity->price;
            if(!empty($data['group_id'])){
                $groupId = $data['group_id'];
            }
        }
        
        // $amountTotal = round($product->price * $data['buy_num'], 2);
        $amountTotal = round($price * $data['buy_num'], 2);

        $buyNum = $data['buy_num'];

        // TODO:: 100010 特惠商品买2送1
        // if (100010 == $product->id && $data['buy_num'] >= 2) {
        //     $buyNum = $data['buy_num'] + floor($data['buy_num'] / 2);
        // }

        $amountCoupon = 0;
        if($coupon instanceof Coupon) {
            $amountCoupon = $coupon->getAmountCoupon($amountTotal);
            $coupon->setUse($orderId);
        }
        $amountPayable = round($amountTotal - $amountCoupon, 2);
        

        DB::beginTransaction();
        try {
            $order                 = new self();
            $order->id             = $orderId;
            $order->user_id        = $user->id;
            $order->product_id     = $product->id;
            $order->group_id       = $groupId;
            $order->product_type   = $product->type;
            $order->buy_num        = $buyNum;
            $order->amount_total   = $amountTotal;
            $order->amount_payable = $amountPayable;
            $order->amount_coupon  = $amountCoupon;
            $order->remark         = $data['remark'] ?? '';
            $order->expire_time    = now()->addSeconds(self::DEFAULT_EXPIRE_TIME)->toDateTimeString();
            $order->status         = self::STATUS_UNPAID;
            $order->save();

            // 收货地址
            $data['quantity'] = $order->buy_num;
            $orderLogistic = OrderLogistics::add($order->id, $data);
            $orderLogistic->setDefault();
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error('创建订单失败 errMsg:' . $e->getMessage() . ' data:' . print_r($data, 1));
            return null;
        }

        $log = "用户下单 数量:{$data['buy_num']} 金额:{$amountTotal}";
        OrderLog::addLog($orderId, $log, OrderLog::TYPE_USER, $user->id, $user->nick_name);
        return $order;
    }
    public function doPay($openId)
    {
        $userId = 0;
        $wxUser = WxUser::where('open_id', $openId)->first();
        if($wxUser instanceof WxUser){
            $userId = $wxUser->user_id;
        }
        $payRecord = OrderPayRecord::where('order_id', $this->id)
            ->where('trade_status', '<>', OrderPayRecord::STATUS_PAID)
            ->where('pay_user_id', $userId)
            ->first();
        if (!$payRecord instanceof OrderPayRecord) {
            $payRecord = OrderPayRecord::add($this,$userId);
        }

        $body = $this->product instanceof Product ? $this->product->title : '';
        return $payRecord->doPay($openId, $body);
    }

    public function changeCoupon($amount_coupon){
        if(!$this->isUnpaid()){
            return false;
        } 
        DB::beginTransaction();
        try {
            if ($this->amount_payable > $amount_coupon && $this->amount_total>$amount_coupon &&  $this->amount_coupon != $amount_coupon && $amount_coupon>0) {
                $this->amount_payable = $this->amount_total - $amount_coupon;
                $this->amount_coupon = $amount_coupon;
                $this->save();
                $payRecord = OrderPayRecord::where('order_id', $this->id)->where('trade_status', '<>', OrderPayRecord::STATUS_PAID)->first();
                if (!$payRecord instanceof OrderPayRecord) {
                    DB::rollBack();
                    return false;
                }
                $payRecord->changePayAmount($this->amount_payable);
            }
            DB::commit();
            return true;
        }catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . ':' . __FUNCTION__ . " orderId: {$this->id} amount_coupon: {$amount_coupon} errMsg:"
                . $e->getMessage());
            return false;
        }
        return false;
    }
    public function isUnpaid()
    {
        return self::STATUS_UNPAID == $this->status;
    }
    public function isPaid()
    {
        return self::STATUS_PAID == $this->status;
    }
    public function isShipped()
    {
        return self::STATUS_SHIPPED == $this->status;
    }
    public function isReceived()
    {
        return self::STATUS_RECEIVED == $this->status;
    }
    public function isRefund()
    {
        return self::STATUS_REFUND == $this->status;
    }
    public function isCancel()
    {
        return self::STATUS_CANCEL == $this->status;
    }

    public function isExpired()
    {
        return self::STATUS_EXPIRED == $this->status;
    }


    public function isRefundedStatus()
    {
        return self::STATUS_REFUND == $this->status;
    }

    public function isUnpaidStatus()
    {
        return self::STATUS_UNPAID == $this->status;
    }

    public function isInvalidStatus()
    {
        $invalidStatus = [self::STATUS_CANCEL, self::STATUS_EXPIRED];
        return \in_array($this->status, $invalidStatus);
    }


    public function getStatusDescAttribute()
    {
        return self::$order_status_desc[$this->status] ?? '';
    }

    public function markSend()
    {
        if ($this->status != self::STATUS_PAID) {
            return false;
        }
        $this->status = self::STATUS_SHIPPED;
        return $this->save();
    }

    public function markNoSend()
    {
        if ($this->status != self::STATUS_SHIPPED) {
            return false;
        }
        $this->status = self::STATUS_PAID;
        return $this->save();
    }


    public function modifyAdminNote($adminNote)
    {
        $this->adminnote  = $adminNote;
        return $this->save();
    }

    public static function getList($userId, $params)
    {
        $page     = $params['page'] ?? 1;
        $size     = $params['size'] ?? 20;
        $queryRes = self::with('product')->where('user_id', $userId)->orderByDesc('id')->simplePaginate($size);

        $orderList = [];
        foreach ($queryRes->items() as $item) {
            $orderList[] = $item->formatListData();
        }
        return [
            'list'     => $orderList,
            'has_more' => $queryRes->hasMorePages() ? 1 : 0,
            'page'     => $page
        ];
    }

    public function formatListData()
    {
        $nickName = $this->user->nick_name??'';
        $return  = [
            'id'                 => $this->id,
            'product_token'      => Codec::encodeId($this->product_id),
            'product_title'      => $this->product->title,
            'product_type'       => $this->product_type,
            'group_id'           => $this->group_id,
            'product_image'      => $this->product->getMainImage(),
            'amount_total'       => sprintf('%.2f', $this->amount_total),
            'buy_num'            => $this->buy_num,
            'status_desc'        => self::$statusDesc[$this->status] ?? '',
            'share_title'        => $nickName.$this->product->share_title,
            'share_image'        => $this->product->share_image,
            'order_time'         => $this->created_at->format('Y-m-d H:i'),
            'show_paid_btn'      => $this->isUnpaid() ? 1 : 0,
            'show_share_btn'     => $this->isUnpaid() ? 0 : 1,
            'show_again_buy_btn' => $this->isUnpaid() ? 0 : 1,
        ];
        if($this->product->isGroupType()){
            $groupId = $this->group_id;
            if(!empty($groupId)){
                $group = Group::find($groupId);
                if($group instanceof Group && $group->status == Group::STATUS_ING){
                    if($this->isPaid()){
                        $return['show_share_btn'] = 0;
                        $return['show_group_btn'] = 1;
                    }
                    
                }
            }
        }
        return $return;
    }
    public function getDetail()
    {
        $product = $this->product;
        if (!$product instanceof Product) {
            return [];
        }

        // 金额信息
        $amountInfo = [['title' => '商品总额', 'amount' => $this->amount_total, 'is_highlight' => 0]];
        if ($this->isUnpaid()) {
            $amountInfo[] = ['title' => '需支付', 'amount' => $this->amount_payable, 'is_highlight' => 1];
        } else if ($this->isPaid() || $this->isShipped() || $this->isReceived()) {
            $amountInfo[] = ['title' => '已支付', 'amount' => $this->amount_payable, 'is_highlight' => 1];
        }

        // 支付信息
        $payInfo = [
            ['title' => '订单编号', 'value' => $this->id],
            ['title' => '下单时间', 'value' => $this->created_at->format('Y-m-d H:i')],
        ];
        if ($this->isPaid() || $this->isShipped() || $this->isReceived()) {
            $payInfo[] = ['title' => '支付时间', 'value' => Carbon::parse($this->paid_time)->format('Y-m-d H:i')];
        }

        // 支付倒计时
        $paidCountdown = 0;
        if ($this->isUnpaid() && $this->expire_time > now()->toDateTimeString()) {
            $paidCountdown = now()->diffInSeconds($this->expire_time);
        }

        // 物流信息
        $logisticList = [];
        foreach ($this->logistics as $logistic) {
            $logisticList[] = [
                'id'               => $logistic->id,
                'receiver_name'    => $logistic->receiver_name,
                'receiver_mobile'  => $logistic->getEncryptMobile(),
                'receiver_address' => $logistic->getReceiverFullAddress(),
                'quantity'         => $logistic->quantity,
                'status_desc'      => OrderLogistics::$statusDesc[$logistic->status] ?? '',
                'is_can_delete'    => $logistic->isCanDelete() ? 1 : 0,
                'express_name'     => $logistic->express_name ?: '',
                'express_no'       => $logistic->express_no,
            ];
        }

        $sendQuantity    = OrderLogistics::getSendQuantity($this->id);
        $showAddLogistic = 0;
        if ($this->isPaid() && $this->buy_num > $sendQuantity) {
            $showAddLogistic = 1;
        }

        $nickName = $this->user->nick_name??'';
        $return = [
            'id'                 => $this->id,
            'product_token'      => Codec::encodeId($product->id),
            'product_title'      => $product->title,
            'product_type'       => $this->product_type,
            'group_id'           => $this->group_id,
            'product_image'      => $product->getMainImage(),
            'amount_total'       => $this->amount_total,
            'buy_num'            => $this->buy_num,
            'status'             => $this->status,
            'status_desc'        => self::$statusDesc[$this->status] ?? '',
            'paid_countdown'     => $paidCountdown,
            'amount_info'        => $amountInfo,
            'pay_info'           => $payInfo,
            'logistics'          => current($logisticList),
            'logistic_list'      => $logisticList,
            'share_title'        => $nickName.$product->share_title,
            'share_image'        => $product->share_image,
            'show_paid_btn'      => $this->isUnpaid() ? 1 : 0,
            'show_share_btn'     => $this->isUnpaid() ? 0 : 1,
            'show_again_buy_btn' => $this->isUnpaid() ? 0 : 1,
            'adminnote'          => $this->adminnote,
            'adminkey'           => self::ADMIN_KEY,
            'tips'               => '多功能饮品机，可冲咖啡、奶泡等饮品',
            'show_add_logistic'  => $showAddLogistic,
            'show_group_btn'     => 0,
        ];

        if($this->product->isGroupType()){
            $groupId = $this->group_id;
            if(!empty($groupId)){
                $group = Group::find($groupId);
                if($group instanceof Group && $group->status == Group::STATUS_ING){
                    $return['show_share_btn'] = 0;
                    $return['show_group_btn'] = 1;
                }
            }
        }
        return $return;
    }
    public function afterPaySuccess()
    {
        $user    = User::find($this->user_id);
        $product = Product::withTrashed()->find($this->product_id);
        if (!$user instanceof User || !$product instanceof Product) {
            return false;
        }
        //预售产品的逻辑先不管
        if ($product->isAdvanceSale()) {
            $orderCnt = self::where('user_id', $this->user_id)
                ->whereIn('status', [self::STATUS_PAID, self::STATUS_SHIPPED, self::STATUS_RECEIVED])
                ->where('product_type', Product::TYPE_ADVANCE)
                ->sum('buy_num');

            // 更新用户预售用户等级
            $nextLevelDiff    = 0;
            $advanceSaleLevel = User::ADVANCE_SALE_LEVEL_ZERO;
            foreach (User::$advanceSaleGradeNum as $level => $num) {
                if ($orderCnt < $num) {
                    $nextLevelDiff = $num - $orderCnt;
                    continue;
                }
                $advanceSaleLevel = $level;
                break;
            }
            $isUpgrade = $advanceSaleLevel > $user->advance_sale_level;
            if (!$user->isAdvanceSaleUser()) {
                $user->becomeAdvanceSale($advanceSaleLevel);
            } else if ($isUpgrade) {
                $user->updateAdvanceSaleLevel($advanceSaleLevel);
            }

            DistributorIncome::addIncomeByAdvanceSale($this, $isUpgrade);

            $this->sendAdvanceSalePaySuccessMsg($product->title, $isUpgrade, $advanceSaleLevel, $nextLevelDiff);
        } else {
            DistributorIncome::addIncomeByNormalSale($this);
            // if ($user->distributor_level < User::DISTRIBUTOR_LEVEL_SECOND) {
            //     $user->upgradeDistributor();
            // }
            // $parentId   = UserRelationship::where('user_id', $this->id)->value('parent_id');
            // $parentUser = User::find($parentId);
            // if ($parentUser instanceof User && $parentUser->distributor_level < User::DISTRIBUTOR_LEVEL_SECOND) {
            //     $parentUser->upgradeDistributor();
            // }
            $this->sendPaySuccessMsg();
        }
        //处理拼团成员问题
        if($product->isGroupType()){
           $this->checkGroup($user,$product);
        }
    }
    public function checkGroup($user, $product){
        //入团
        if(!empty($this->group_id)){
            GroupTeam::add($this,GroupTeam::ROLE_MEMBER);
        }else{
            //活动有效才开新团
            $activity = Activity::where('product_id',$product->id)->where('status',Activity::STATUS_VALID)->first();
            if($activity instanceof Activity && $activity->product_id == $product->id){
                //新团
                $groupId = Group::create($user,$product);
                if($groupId>0){
                    $this->updateGroupId($groupId);
                    GroupTeam::add($this,GroupTeam::ROLE_LEADER);
                }
            }
        }
    }

    private function updateGroupId($groupId){
        $this->group_id = $groupId;
        return $this->save();
    }
     // 发送支付成功消息
     private function sendAdvanceSalePaySuccessMsg($productTitle, $isUpgrade, $advanceSaleLevel, $nextLevelDiff)
     {
         try {
             // 支付成功通知
             $wxUser = WxUser::where('user_id', $this->user_id)->where('source', WxUser::SOURCE_WXAPP)->first();
             if ($wxUser instanceof WxUser) {
                 $levelDesc  = User::$advanceSaleLevelDesc[$advanceSaleLevel];
                 $percentage = User::$advanceSaleRebatePercentage[$advanceSaleLevel];
                 $desc       = $isUpgrade ? "恭喜你成为{$levelDesc}" : "尊贵的{$levelDesc}";
                 if ($percentage > 0) {
                     $desc .= "，你预售的商品每件享受{$percentage}%返利";
                 }
                 if ($nextLevelDiff > 0) {
                     $nextLevelDesc  = User::$advanceSaleLevelDesc[$advanceSaleLevel + 1] ?? $levelDesc;
                     $nextPercentage = User::$advanceSaleRebatePercentage[$advanceSaleLevel + 1] ?? $percentage;
                     $desc           .= "，你再购买{$nextLevelDiff}件商品，升级为{$nextLevelDesc}，可享受{$nextPercentage}%返利";
                 }
                 $page        = PushMessage::PAGE_ORDER_DETAIL . '?id=' . $this->id;
                 $orderStatus = '已付款';
                 PushMessage::sendOrderStatusChangeNotice($wxUser->open_id, $page, $this->id, $productTitle, $orderStatus, $desc);
             }
         } catch (\Throwable $e) {
             Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 发消息异常 orderId:' . $this->id . ' errMsg:' . $e->getMessage());
         }
     }
 
     // 发送支付成功消息
     private function sendPaySuccessMsg()
     {
         try {
             // 支付成功通知
             $wxUser  = WxUser::where('user_id', $this->user_id)->where('source', WxUser::SOURCE_WXAPP)->first();
             $product = Product::withTrashed()->find($this->product_id);
             if ($wxUser instanceof WxUser && $product instanceof Product) {
                 $page        = PushMessage::PAGE_ORDER_DETAIL . '?id=' . $this->id;
                 $logistics   = OrderLogistics::where('order_id', $this->id)->first();
                 $fullAddress = '';
                 if ($logistics instanceof OrderLogistics) {
                     $fullAddress = $logistics->getReceiverFullAddress();
                 }
                 $paidTime = Carbon::parse($this->paid_time)->format('Y-m-d H:i');
                 PushMessage::sendPaymentSuccessNotice(
                     $wxUser->open_id,
                     $page,
                     $this->id,
                     $product->title,
                     $this->amount_total,
                     $this->buy_num,
                     $paidTime,
                     $fullAddress
                 );
             }
         } catch (\Throwable $e) {
             Log::error(__CLASS__ . '_' . __FUNCTION__ . ' 发消息异常 orderId:' . $this->id . ' errMsg:' . $e->getMessage());
         }
     }

    // 订单设置为过期
    public function doExpired()
    {
        $this->status        = self::STATUS_EXPIRED;
        $this->invalid_cause = self::INVALID_EXPIRED;
        return $this->save();
    }

    // 订单确认收货
    public function doFinished()
    {
        $this->status        = self::STATUS_RECEIVED;
        $this->received_time = now()->toDateTimeString();
        return $this->save();
    }


    public function doRefund($refundAmount, &$errorMsg)
    {
        if ($this->isRefundedStatus()) {
            $errorMsg = '订单' . $this->status_desc . '，请勿重复提交';
            return false;
        }
        if ($this->isUnpaidStatus() || $this->isInvalidStatus()) {
            $errorMsg = '订单' . $this->status_desc . '，不能退款';
            return false;
        }

        $canRefundAmount = round($this->amount_paid - $this->amount_refundable, 2);
        if (bccomp($refundAmount, $canRefundAmount, 2) > 0) {
            $errorMsg = "退款金额不能超过 {$canRefundAmount}";
            return false;
        }

        $payRecordList = OrderPayRecord::where('order_id', $this->id)
            ->where('trade_status', OrderPayRecord::STATUS_PAID)->get();
        if ($payRecordList->isEmpty()) {
            $errorMsg = '用户无支付记录，不能退款';
            return false;
        }

        $refundedList = OrderRefundRecord::where('order_id', $this->id)
            ->whereIn('refund_status', [OrderRefundRecord::STATUS_REFUNDING, OrderRefundRecord::STATUS_REFUNDED])
            ->selectRaw('pay_record_id, sum(amount) as refund_amount')
            ->groupBy('pay_record_id')
            ->pluck('refund_amount', 'pay_record_id')->toArray();

        $refundRecordList = array();
        DB::beginTransaction();
        try {
            foreach ($payRecordList as $payRecord) {
                $refundedAmount = $refundedList[$payRecord->id] ?? 0;
                $amountDiff     = bcsub($payRecord->amount, $refundedAmount, 2);
                if ($amountDiff <= 0) {
                    continue;
                }

                if (bccomp($refundAmount, $amountDiff, 2) <= 0) {
                    $refundRecordList[] = OrderRefundRecord::add($payRecord, $refundAmount);
                    $refundAmount       = 0;
                    break;
                } else {
                    $refundAmount       = bcsub($refundAmount, $amountDiff, 2);
                    $refundRecordList[] = OrderRefundRecord::add($payRecord, $amountDiff);
                }
            }

            if ($refundAmount > 0) {
                DB::rollBack();
                $errorMsg = '退款金额不足';
                return false;
            }
            DB::commit();
        } catch (\Throwable $e) {
            DB::rollBack();
            Log::error(__CLASS__ . ':' . __FUNCTION__ . " orderId: {$this->id} refundAmount: {$refundAmount} errMsg:"
                . $e->getMessage());
            $errorMsg = '申请退款失败，请稍后重试-2';
            return false;
        }

        // 发起退款
        foreach ($refundRecordList as $refundRecord) {
            if (!$refundRecord instanceof OrderRefundRecord) {
                continue;
            }
            $rest = $refundRecord->doRefund();
            if (!$rest) {
                $errorMsg = '申请退款失败，请稍后重试-3';
                return false;
            }
        }
        return true;
    }
}
