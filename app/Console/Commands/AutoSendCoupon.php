<?php
/*
 * @Author: your name
 * @Date: 2021-02-09 14:08:28
 * @LastEditTime: 2021-02-11 13:33:22
 * @LastEditors: Please set LastEditors
 * @Description: In User Settings Edit
 * @FilePath: /paota/ptweb/app/Console/Commands/AutoSendCoupon.php
 */

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Group;
use App\Models\GroupTeam;
use App\Models\Coupon;
use App\Models\CouponTemplate;
use App\Models\Order;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use App\Models\WxUser;

class AutoSendCoupon extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'command:auto_send_coupon';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '拼团订单自动发券';

    const COUPON_TEMPLATE_ID = 6;

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        Log::info('start to send');
        $groups = Group::where('status',Group::STATUS_SUCC)->get();
        $groupNum = count($groups);
        Log::info('total '.$groupNum . ' groups');
        foreach($groups as $group){
            $groupId = $group->id;
            if($groupId > 0) {
               $orders = Order::where('group_id',$groupId)->whereIn('status',[Order::STATUS_SHIPPED,Order::STATUS_RECEIVED])->where('is_send_coupon',0)->get();
               foreach ($orders as $order) {
                   $user = $order->user;
                   $wxUser = WxUser::where('user_id', $user->id)->first();
                   if (!$wxUser instanceof WxUser) {
                       Log::info('领取失败，用户信息出错===orderId='.$order->id);
                       continue;
                   }
                   if (CouponTemplate::VALID_TYPE_RANGE == $this->valid_type) {
                       $validStartTime = $this->valid_start_time;
                       $validEndTime   = $this->valid_end_time;
                   } else {
                       $validStartTime = today()->toDateTimeString();
                       $validEndTime   = today()->addDays($this->valid_day)->toDateTimeString();
                   }

                   $couponTemplate = CouponTemplate::find(self::COUPON_TEMPLATE_ID);
                   DB::beginTransaction();
                   try {
                       $couponCnt = 14;
                       while ($couponCnt--) {
                           $coupon                   = new Coupon();
                           $coupon->user_id          = $user->id;
                           $coupon->wx_user_id       = $wxUser->id;
                           $coupon->template_id      = $couponTemplate->id;
                           $coupon->name             = $couponTemplate->name;
                           $coupon->type             = $couponTemplate->type;
                           $coupon->cut_value        = $couponTemplate->cut_value;
                           $coupon->cost             = $couponTemplate->cost;
                           $coupon->valid_start_time = $validStartTime;
                           $coupon->valid_end_time   = $validEndTime;
                           $coupon->status           = Coupon::STATUS_NOT_USE;
                           $coupon->save();
                       }
                       $order->is_send_coupon = 1;
                       $order->save();
                       $couponTemplate->increment('used_cnt', 14);
                       DB::commit();
                       Log::info('领取成功===orderId='.$order->id);
                   } catch (\Throwable $e) {
                       DB::rollBack();
                       Log::info('领取优惠券失败===orderId='.$order->id.'errMsg:' . $e->getMessage());
                   }
               }
            }
        }
        
    }
}
