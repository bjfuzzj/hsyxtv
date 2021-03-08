<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderLog;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class AutoFinishOrder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crontab:auto_finish_order';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将到了自动完成时间已到诊的订单状态改为已完成';

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
        Log::info('===系统自动确认完成 start===');
//        $today  = today()->toDateString();
//        $orders = Order::where('status', Order::STATUS_SHIPPED)->where('auto_finish_time', '<=', $today)->get();
        $orders = Order::where('status', Order::STATUS_SHIPPED)->where('updated_at', '<=', today()->addDays(-3)->toDateString())->get();

        foreach ($orders as $order) {
            Log::info('orderId:' . $order->id);
            try {
                $order->doFinished();
                OrderLog::addLog($order->id, '系统自动确认完成', OrderLog::TYPE_SYSTEM);
            } catch (\Throwable $e) {
                Log::error('系统自动确认完成异常 orderId:' . $order->id . ' errorMsg:' . $e->getMessage());
            }
        }
        Log::info('===系统自动确认完成 end===');
    }
}
