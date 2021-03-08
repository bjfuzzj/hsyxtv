<?php

namespace App\Console\Commands;

use App\Models\Order;
use App\Models\OrderLogistics;
use Illuminate\Console\Command;


class changeOlStatus extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'script:changeolstatus';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '将物流表的第一条记录改成默认地址';

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
        $orders = Order::with(['logistics' => function ($query) {
            $query->withTrashed();
        }])->get();
        foreach ($orders as $order) {
            $logisticList = $order->logistics;
            foreach ($logisticList as $firstLogistic) {
                $firstLogistic->setDefault();
                break;
            }
        }
    }
}
