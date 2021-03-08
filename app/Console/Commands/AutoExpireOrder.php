<?php

namespace App\Console\Commands;

use App\Helper\CacheKey;
use App\Helper\Codec;
use App\Helper\PushMessage;
use App\Helper\RemoteRequest;
use App\Models\Order;
use App\Models\OrderLog;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class AutoExpireOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crontab:auto_expire_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将到了过期时间还未支付的订单状态改为过期';

    /**
     * Create a new command instance.
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
        Log::info('===系统自动过期 start===');
        $now    = now()->toDateTimeString();
        $orders = Order::where('status', Order::STATUS_UNPAID)->where('expire_time', '<', $now)->get();

        $orderIds = [];
        foreach ($orders as $order) {
            Log::info('系统自动过期 orderId:' . $order->id);
            $orderIds[] = $order->id;
            try {
                $order->doExpired();
                OrderLog::addLog($order->id, '系统自动过期', OrderLog::TYPE_SYSTEM);
            } catch (\Throwable $e) {
                Log::error('系统自动过期异常 orderId:' . $order->id . ' errorMsg:' . $e->getMessage());
            }
        }

        //发送订单过期通知
        $queryRes    = DB::table('orders as o')->join('products as p', 'o.product_id', '=', 'p.id')
            ->join('wx_users as wu', 'wu.user_id', '=', 'o.user_id')
            ->selectRaw('o.id as order_id, o.product_id, p.title as product_title, wu.open_id')
            ->whereIn('o.id', $orderIds)
            ->get();
        $desc        = '亲，订单已过期，点击重新购买>>';
        $orderStatus = '已过期';
        foreach ($queryRes as $item) {
            try {
                $page = PushMessage::PAGE_PRODUCT_DETAIL . '?productID=' . Codec::encodeId($item->product_id);
                PushMessage::sendOrderStatusChangeNotice($item->open_id, $page, $item->order_id, $item->product_title, $orderStatus, $desc);
            } catch (\Throwable $e) {
                Log::error('发送订单过期通知异常 orderId:' . $item->order_id . ' errorMsg:' . $e->getMessage());
            }
        }

        //发送订单即将过期通知
        $now         = now()->addMinutes(30)->toDateTimeString();
        $queryRes    = DB::table('orders as o')->join('products as p', 'o.product_id', '=', 'p.id')
            ->join('wx_users as wu', 'wu.user_id', '=', 'o.user_id')
            ->selectRaw('o.id as order_id, o.product_id, p.title as product_title, wu.open_id')
            ->where('o.status', Order::STATUS_UNPAID)->where('o.expire_time', '<', $now)
            ->get();
        $desc        = '亲，订单还有30分钟自动关闭，赶紧去支付吧>>';
        $orderStatus = '待付款';
        foreach ($queryRes as $item) {
            try {
                $cacheKey = CacheKey::CK_ABOUT_TO_EXPIRE_REMIND . $item->order_id;
                if (Cache::has($cacheKey)) {
                    continue;
                }
                Cache::put($cacheKey, 1, 3600);

                $page = PushMessage::PAGE_ORDER_DETAIL . '?id=' . $item->order_id;
                PushMessage::sendOrderStatusChangeNotice($item->open_id, $page, $item->order_id, $item->product_title, $orderStatus, $desc);
            } catch (\Throwable $e) {
                Log::error('发送订单即将过期通知 orderId:' . $item->order_id . ' errorMsg:' . $e->getMessage());
            }
        }
        Log::info('===系统自动过期 end===');
    }


}
