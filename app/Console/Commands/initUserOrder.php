<?php

namespace App\Console\Commands;

use App\Models\OrderLogistics;
use App\Models\User;
use Illuminate\Console\Command;
use App\Models\WxUser;
use App\Models\Order;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

ini_set('max_execution_time', 0);
ini_set('memory_limit', '500M');

class initUserOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:userorder';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';
    private   $startTime   = '';
    private   $endTime     = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->startTime = '2019-12-01 00:00:00';
        $this->endTime   = '2019-12-31 23:59:59';
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $orderList = Order::where('status', Order::STATUS_PAID)->get();
        foreach ($orderList as $order) {
            $orderLogistic = OrderLogistics::where('order_id', $order->id)->first();
            if (false === $orderLogistic instanceof OrderLogistics) {
                continue;
            }
            $buy_num = $order->buy_num;
            $express_nos = $orderLogistic->express_no;
            if(empty($express_nos)) continue;
            $count_express = @explode(',',$express_nos);
            if(count($count_express) == $buy_num) {
                //Log::info('订单ID:'.$order->id.'都发货了,没标记buy_num='.$buy_num.'express_no='.$express_nos);
                $order->markSend();
            }
        }

        /*
        WxUser::chunk(1000, function ($wxUserList) {
            $str = '';
            foreach($wxUserList as $wxUser) {
                $record              = [];
                $record['nick_name'] = $wxUser->nick_name;
                //自己购买
                if (empty($wxUser->user_id)) {
                    $selfBuy = 0;
                } else {
                    $selfBuy = Order::where('status', '<=', Order::STATUS_RECEIVED)->where('status', '>=', Order::STATUS_PAID)
                        ->where('paid_time', '<=', $this->endTime)
                        ->where('paid_time', '>=', $this->startTime)
                        ->where('user_id', $wxUser->user_id)
                        ->sum('buy_num');
                }
                $user = User::find($wxUser->user_id);
                if(!$user instanceof User || $user->is_distributor === User::DISTRIBUTOR_NO) {
                    Log::info($wxUser->nick_name.'不是分销员');
                    continue;
                }
                $record['selfBuy'] = $selfBuy;

                //下一级购买
                $nextBuy           = DB::table('user_relationships as ur')
                    ->join('orders as o', 'ur.user_id', '=', 'o.user_id')
                    ->where('ur.parent_id', $wxUser->user_id)
                    ->where('o.status', '<=', Order::STATUS_RECEIVED)
                    ->where('o.status', '>=', Order::STATUS_PAID)
                    ->where('o.paid_time', '<=', $this->endTime)
                    ->where('o.paid_time', '>=', $this->startTime)
                    ->sum('o.buy_num');
                $record['nextBuy'] = $nextBuy;

                //下两级购买
                $nextTwoBuy           = DB::table('user_relationships as ur')
                    ->join('user_relationships as urs', 'ur.user_id', '=', 'urs.parent_id')
                    ->join('orders as o', 'urs.user_id', '=', 'o.user_id')
                    ->where('ur.parent_id', $wxUser->user_id)
                    ->where('o.status', '<=', Order::STATUS_RECEIVED)
                    ->where('o.status', '>=', Order::STATUS_PAID)
                    ->where('o.paid_time', '<=', $this->endTime)
                    ->where('o.paid_time', '>=', $this->startTime)
                    ->sum('o.buy_num');
                $record['nextTwoBuy'] = $nextTwoBuy;

                //下三级购买
                $nextThreeBuy           = DB::table('user_relationships as ur')
                    ->join('user_relationships as urs', 'ur.user_id', '=', 'urs.parent_id')
                    ->join('user_relationships as urss', 'urs.user_id', '=', 'urss.parent_id')
                    ->join('orders as o', 'urss.user_id', '=', 'o.user_id')
                    ->where('ur.parent_id', $wxUser->user_id)
                    ->where('o.status', '<=', Order::STATUS_RECEIVED)
                    ->where('o.status', '>=', Order::STATUS_PAID)
                    ->where('o.paid_time', '<=', $this->endTime)
                    ->where('o.paid_time', '>=', $this->startTime)
                    ->sum('o.buy_num');
                $record['nextThreeBuy'] = $nextThreeBuy;
                $dataStr = $record['nick_name'].','.$record['selfBuy'].','.$nextBuy.','.$nextTwoBuy.','.$nextThreeBuy.PHP_EOL;
                Log::info($dataStr);
                $str .= $wxUser->id.','.$record['nick_name'].','.$record['selfBuy'].','.$nextBuy.','.$nextTwoBuy.','.$nextThreeBuy.PHP_EOL;
            }
            file_put_contents(storage_path() . '/txt.txt', $str, FILE_APPEND | LOCK_EX);
        });
        */


    }
}
